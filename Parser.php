<?php
/**
 * @package axy\ml
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */

namespace axy\ml;

use axy\ml\helpers\Tokenizer;
use axy\ml\helpers\Normalizer;

/**
 * The parser of axyml document
 */
class Parser
{
    /**
     * The constructor
     *
     * @param array $options [optional]
     *        the custom options list
     * @param array $tags [optional]
     *        the custom tags list
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
     * Parses an axyml document
     *
     * @param string $content
     *        the document content
     * @param string $cut [optional]
     *        the anchor for document cutting
     * @return \axy\ml\Result
     *         the parsing result
     */
    public function parse($content, $cut = null)
    {
        $content = Normalizer::toParse($content, $this->options);
        $tokenizer = new Tokenizer($content, $this->options);
        $tokenizer->tokenize($cut);
        return new Result($tokenizer, $this->options, $this->tags, $this->custom);
    }

    /**
     * Parses of an axyml document from a file
     *
     * @param string $filename
     *        the file name of the document (must exist)
     * @param string $cut [optional]
     *        the anchor for document cutting
     * @return \axy\ml\Result
     *         the result of parsing
     */
    public function parseFile($filename, $cut = null)
    {
        return $this->parse(file_get_contents($filename), $cut);
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
