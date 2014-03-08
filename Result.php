<?php
/**
 * @package axy\ml
 */

namespace axy\ml;

use axy\ml\helpers\Token;
use axy\ml\helpers\Highlight;
use axy\ml\helpers\Normalizer;
use axy\ml\helpers\Block;
use axy\ml\Error;

/**
 * The result of parsing of an axyml document
 *
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 *
 * @property-read string $html
 *                a html representation
 * @property-read string $plain
 *                a plain text representaion
 * @property-read string $title
 *                a title of the document
 * @property-read \axy\ml\Meta $meta
 *                a meta data
 * @property-read boolead $isCutted
 *                a flag that the document was cutted
 * @property-read array $tokens
 *                a list of tokens
 * @property-read array $errors
 *                a list of errors
 * @property-read \axy\ml\Profiler $profiler
 *                a profiler
 */
class Result
{
    use \axy\magic\LazyField;

    /**
     * Constructor
     *
     * @param \axy\ml\helpers\Tokenizer $tokenizer
     *        the tokenizer with the result of the document tokenize
     * @param \axy\ml\Options $options
     *        the parsing options
     * @param \axy\ml\TagsList $tags
     *        the list of available tags
     * @param mixed $custom
     *        the custom context
     */
    public function __construct(helpers\Tokenizer $tokenizer, Options $options, TagsList $tags, $custom = null)
    {
        $this->tokenizer = $tokenizer;
        $this->tags = $tags;
        $this->context = new Context($this, $options, $tags, $custom);
        $profiler = new Profiler();
        $profiler->tokenize = $tokenizer->getDuration();
        $this->tokens = $tokenizer->getTokens();
        $this->magicFields = [
            'fields' => [
                'meta' => $tokenizer->getMeta(),
                'isCutted' => $tokenizer->isCutted(),
                'tokens' => $this->tokens,
                'errors' => $tokenizer->getErrors(),
                'profiler' => $profiler,
            ],
            'loaders' => [
                'title' => '::loadTitle',
                'html' => '::createHtml',
                'plain' => '::createPlain',
            ],
        ];
    }

    /**
     * Get the list of headers
     *
     * @param int $max [optional]
     * @return array (content, level, name)
     */
    public function getHeaders($max = null)
    {
        $headers = [];
        foreach ($this->tokens as $token) {
            if ($token->type !== Token::TYPE_HEADER) {
                continue;
            }
            if ($max && ($token->level > $max)) {
                continue;
            }
            $headers[] = [
                'content' => $token->content,
                'level' => $token->level,
                'name' => $token->name,
                'link' => $token->link,
            ];
        }
        return $headers;
    }

    /**
     * Replace a tokens list
     *
     * @param array $tokens
     * @return \axy\ml\Result
     */
    public function replaceTokens(array $tokens)
    {
        $this->tokens = $tokens;
        unset($this->magicFields['fields']['title']);
        unset($this->magicFields['fields']['html']);
        unset($this->magicFields['fields']['plain']);
        $this->magicFields['fields']['tokens'] = $tokens;
    }

    /**
     * @return string
     */
    private function loadTitle()
    {
        foreach ($this->tokens as $token) {
            if (($token->type === Token::TYPE_HEADER) && ($token->level === 1)) {
                return $token->content;
            }
        }
        return null;
    }

    /**
     * @return string
     */
    private function createHtml()
    {
        $mt = \microtime(true);
        $context = $this->context;
        $options = $this->context->options->getSource();
        $context->startRender($this->tokenizer->getErrors());
        $tags = $this->tags;
        $errors = [];
        $blocks = [];
        foreach ($this->tokens as $token) {
            switch ($token->type) {
                case Token::TYPE_HEADER:
                    if ($token->content !== '') {
                        $blocks[] = Highlight::header($token, $options);
                    }
                    break;
                case Token::TYPE_ANCHOR:
                    if ($token->name) {
                        $blocks[] = '<a name="'.Highlight::escape($token->name).'"></a>';
                    }
                    break;
                case Token::TYPE_BLOCK:
                    $block = new Block($token, $context);
                    $block->render();
                    $blocks = \array_merge($blocks, $block->blocks);
                    break;
            }
        }
        $this->magicFields['fields']['errors'] = Error::sortListByLine($this->context->errors);
        $sep = $options['beauty'] ? "\n\n" : "\n";
        $html = \implode($sep, $blocks);
        $html = Normalizer::toResult($html, $options);
        $this->context->endRender();
        $mt = \microtime(true) - $mt;
        $this->magicFields['fields']['profiler']->html = $mt;
        return $html;
    }

    /**
     * @return string
     */
    private function createPlain()
    {
        $mt = \microtime(true);
        $context = $this->context;
        $options = $this->context->options->getSource();
        $context->startRender($this->tokenizer->getErrors());
        $tags = $this->tags;
        $blocks = [];
        foreach ($this->tokens as $token) {
            switch ($token->type) {
                case Token::TYPE_HEADER:
                    if ($token->content !== '') {
                        $blocks[] = $token->content;
                    }
                    break;
                case Token::TYPE_ANCHOR:
                    break;
                case Token::TYPE_BLOCK:
                    $els = [];
                    foreach ($token->subs as $item) {
                        switch ($item->type) {
                            case Token::TYPE_TEXT:
                                $els[] = $item->content;
                                break;
                            case Token::TYPE_TAG:
                                $tag = $tags->create($item->name, $item->content, $context);
                                if ($tag) {
                                    $els[] = $tag->getPlain();
                                }
                                break;
                        }
                    }
                    $blocks[] = \implode('', $els);
                    break;
            }
        }
        $context->endRender();
        $result = \implode("\n", $blocks);
        $mt = \microtime(true) - $mt;
        $this->magicFields['fields']['profiler']->plain = $mt;
        return $result;
    }

    /**
     * The tokenizer for the current document
     *
     * @var \axy\ml\helpers\Tokenizer
     */
    private $tokenizer;

    /**
     * The current tags list
     *
     * @var \axy\ml\TagsList
     */
    private $tags;

    /**
     * The parsing context
     *
     * @var \axy\ml\Context
     */
    private $context;

    /**
     * @var array
     */
    private $tokens;
}
