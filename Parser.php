<?php
/**
 * @package axy\ml
 */

namespace axy\ml;

use axy\ml\helpers\Tokenizer;
use axy\ml\helpers\Normalizer;

/**
 * The parser of axyml documents
 *
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */
class Parser
{
    /**
     * Constructor
     *
     * @param array $options [optional]
     *        the list of custom options
     * @param array $tags [optional]
     *        the list of custom tags
     * @param mixed $custom [optional]
     *        the custom context
     */
    public function __construct(array $options = null, array $tags = null, $custom = null)
    {
        $this->options = new Options($options);
        $this->tags = new TagsList($tags);
        $this->custom = $custom;
    }

    /**
     * Parsing of an axyml document
     *
     * @param string $content
     *        the content of the document
     * @param string $cut [optional]
     *        the anchor for document cutting
     * @return \axy\ml\Result
     *         the result of parsing
     */
    public function parse($content, $cut = null)
    {
        $content = Normalizer::toParse($content, $this->options);
        $tokenizer = new Tokenizer($content, $this->options);
        $tokenizer->tokenize($cut);
        return new Result($tokenizer, $this->options, $this->tags, $this->custom);
    }

    /**
     * The parsing options
     *
     * @var \axy\ml\Options
     */
    private $options;

    /**
     * The current tags list
     *
     * @var \axy\ml\TagsList
     */
    private $tags;

    /**
     * The custom context
     *
     * @var mixed
     */
    private $custom;
}
