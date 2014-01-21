<?php
/**
 * @package axy\ml
 */

namespace axy\ml\helpers;

use axy\ml\Error;
use axy\callbacks\Callback;

/**
 * Parameters of current block (during render)
 *
 * @autor Oleg Grigoriev <go.vasac@gmail.com>
 */
class Block
{
    /**
     * Construct
     *
     * @param \axy\ml\helpers\Token $container
     *        a token of the current block
     * @param \axy\ml\Context $context
     *        the parsing context
     */
    public function __construct(Token $container, \axy\ml\Context $context)
    {
        $this->container = $container;
        $this->context = $context;
    }

    /**
     * Render block
     *
     * @param array &$errors
     *        a list of parsing errors
     * @return string
     */
    public function getHTMLBlocks(array &$errors)
    {
        $context = $this->context;
        $options = $context->options->getSource();
        $tags = $context->tags;
        $blocks = [];
        $block = [];
        $lastCr = true;
        $listlevel = 0;
        $list = null;
        $lists = [];
        $lnl = $options['beauty'] ? "\n" : '';
        foreach ($this->container->subs as $token) {
            switch ($token->type) {
                case Token::TYPE_TEXT:
                    $lastCr = true;
                    $block[] = Highlight::text($token->content, $options);
                    break;
                case Token::TYPE_TAG:
                    $tag = $tags->create($token->name, $token->content, $context);
                    if ($tag) {
                        $html = $tag->getHtml();
                        foreach ($tag->getErrors() as $err) {
                            $data = [
                                'tag' => $token->name,
                                'info' => $err,
                            ];
                            $errors[] = new Error(Error::TAG_INVALID, $token->line, $data);
                        }
                        if (empty($block)) {
                            $block[] = $html;
                        } else {
                            if ($tag->shouldSplitBlock()) {
                                foreach (\array_reverse($lists) as $l) {
                                    $block[] = '</li>'.$lnl.'</'.$l.'>'.$lnl;
                                }
                                $listlevel = 0;
                                $lists = [];
                                $blocks[] = self::wrapBlock($block, $options, $lastCr);
                                $blocks[] = self::wrapBlock([$html], $options, $tag->shouldCreateBlock());
                                $block = [];
                            } else {
                                $block[] = $html;
                            }
                        }
                        $lastCr = $tag->shouldCreateBlock();
                    } else {
                        $data = [
                            'tag' => $token->name,
                        ];
                        $errors[] = new Error(Error::TAG_UNKNOWN, $token->line, $data);
                    }
                    break;
                case Token::TYPE_LI:
                    $delta = $token->level - $listlevel;
                    if ($delta > 0) {
                        $list = ($token->start === null) ? 'ul' : 'ol';
                        for ($i = 0; $i < $delta; $i++) {
                            $s = ($token->start > 1) ? ' start="'.$token->start.'"' : '';
                            $block[] = (empty($block) ? '' : $lnl).'<'.$list.$s.'>'.$lnl.'<li>';
                            $lists[] = $list;
                        }
                    } elseif ($delta < 0) {
                        $rlists = \array_reverse(\array_slice($lists, $token->level));
                        $lists = \array_slice($lists, 0, $token->level);
                        foreach ($rlists as $l) {
                            $block[] = '</li>'.$lnl.'</'.$l.'>';
                        }
                        $block[] = '</li>'.$lnl.'<li>';
                    } else {
                        $block[] = '</li>'.$lnl.'<li>';
                    }
                    $listlevel = $token->level;
                    break;
            }
        }
        if (!empty($block)) {
            foreach (\array_reverse($lists) as $l) {
                $block[] = '</li>'.$lnl.'</'.$l.'>';
            }
            $blocks[] = self::wrapBlock($block, $options, $lastCr);
        }
        return $blocks;

    }

    /**
     * @param array $block
     * @param array $options
     * @param boolean $lastCr
     * @return array
     */
    private static function wrapBlock(array $block, $options, $lastCr)
    {
        if ((!$lastCr) && (\count($block) === 1)) {
            return $block[0];
        }
        $block = \trim(\implode('', $block));
        if ($options['bHandler']) {
            return Callback::call($options['bHandler'], [$block]);
        }
        $t = $options['bTags'];
        return $t[0].$block.$t[1];
    }

    /**
     * @var \axy\ml\helpers\Token
     */
    private $container;

    /**
     * @var \axy\ml\Context
     */
    private $context;
}
