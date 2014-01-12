<?php
/**
 * @package axy\ml
 */

namespace axy\ml;

use axy\ml\helpers\Token;

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
            ],
            'loaders' => [
                'title' => '::loadTitle',
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
