<?php
/**
 * @package axy\ml
 */

namespace axy\ml;

use axy\ml\helpers\Token;
use axy\ml\helpers\Handlers;
use axy\ml\helpers\Normalizer;

/**
 * The result of parsing of an axyml document
 *
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 *
 * @property-read string $html
 * @property-read string $plain
 * @property-read string $title
 * @property-read \axy\ml\Meta $meta
 * @property-read boolead $isCutted
 * @property-read array $tokens
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
     */
    public function __construct(helpers\Tokenizer $tokenizer, Options $options, TagsList $tags)
    {
        $this->tokenizer = $tokenizer;
        $this->options = $options;
        $this->tags = $tags;
        $this->magicFields = [
            'fields' => [
                'meta' => $tokenizer->getMeta(),
                'isCutted' => $tokenizer->isCutted(),
                'tokens' => $tokenizer->getTokens(),
                'errors' => $tokenizer->getErrors(),
            ],
            'loaders' => [
                'title' => '::loadTitle',
                'html' => '::createHtml',
                'plain' => '::createPlain',
            ],
        ];
    }

    /**
     * @return string
     */
    private function loadTitle()
    {
        foreach ($this->tokenizer->getTokens() as $token) {
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
        $options = $this->options->getSource();
        $tags = $this->tags;
        $errors = [];
        $blocks = [];
        foreach ($this->tokenizer->getTokens() as $token) {
            switch ($token->type) {
                case Token::TYPE_HEADER:
                    if (!empty($token->content)) {
                        $blocks[] = Handlers::header($token, $options);
                    }
                    break;
                case Token::TYPE_ANCHOR:
                    if ($token->name) {
                        $blocks[] = '<a name="'.Handlers::escape($token->name).'"></a>';
                    }
                    break;
                case Token::TYPE_BLOCK:
                    $blocks = \array_merge($blocks, Handlers::block($token, $options, $tags, $errors));
                    break;
            }
        }
        $this->magicFields['fields']['errors'] = $this->mergeErrors($errors);
        $html = \implode($options['blocks_separator'], $blocks);
        return Normalizer::toResult($html, $options);
    }

    /**
     * @return string
     */
    private function createPlain()
    {
        $options = $this->options->getSource();
        $tags = $this->tags;
        $blocks = [];
        foreach ($this->tokenizer->getTokens() as $token) {
            switch ($token->type) {
                case Token::TYPE_HEADER:
                    if (!empty($token->content)) {
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
                                $tag = $tags->create($item->name, $item->content);
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
        return \implode("\n", $blocks);
    }

    /**
     * @param array $errors
     * @return array
     */
    private function mergeErrors(array $errors)
    {
        $tokerr = $this->tokenizer->getErrors();
        if (!empty($tokerr)) {
            $errors = \array_merge($errors, $tokerr);
            \usort($errors, function ($a, $b) {
                if ($a->line > $b->line) {
                    return 1;
                } elseif ($a->line < $b->line) {
                    return -1;
                }
                return 0;
            });
        }
        return $errors;
    }

    /**
     * @var \axy\ml\helpers\Tokenizer
     */
    private $tokenizer;

    /**
     * @var \axy\ml\Options
     */
    private $options;

    /**
     * @var \axy\ml\TagsList
     */
    private $tags;
}
