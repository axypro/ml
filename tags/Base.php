<?php
/**
 * @package axy\ml
 */

namespace axy\ml\tags;

use axy\ml\helpers\TagParser;
use axy\ml\Options;

/**
 * The basic class of axyml-tags
 *
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */
abstract class Base
{
    /**
     * Constructor
     *
     * @param string $name
     *        the tag name
     * @param string $content
     *        the content of the tag
     * @param array $options [optional]
     *        the custom options
     * @param array $goptions [optional]
     *        the global options
     */
    public function __construct($name, $content, array $options = null, $goptions = null)
    {
        $this->name = $name;
        $this->value = $content;
        $this->goptions = $goptions ?: new Options();
        if ($options) {
            $this->options = \array_replace($this->options, $options);
        }
        $this->preparse();
        $this->parse();
    }

    /**
     * Get the HTML representation of tag
     *
     * @return string
     */
    abstract public function getHTML();

    /**
     * Get the plain text representation of tag
     *
     * @return string
     */
    public function getPlain()
    {
        return $this->getHTML();
    }

    /**
     * Get the errors list
     *
     * @return array
     */
    final public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Should the block be broken?
     * (for inline tags)
     *
     * @return boolean
     */
    public function shouldSplitBlock()
    {
        return $this->splitBlock;
    }

    /**
     * Should the block be created?
     * (for standalone tags)
     *
     * @return boolean
     */
    public function shouldCreateBlock()
    {
        return $this->createBlock;
    }

    /**
     * Parse (called from the constructor)
     */
    protected function parse()
    {
    }

    /**
     * Get a next component of the value
     *
     * @return string
     */
    protected function getNextComponent()
    {
        return TagParser::loadNextComponent($this->value);
    }

    /**
     * Get a last component of the value
     *
     * @return string
     */
    protected function getLastComponent()
    {
        return TagParser::loadLastComponent($this->value);
    }

    /**
     * Pre process
     */
    protected function preparse()
    {
        if ($this->args !== false) {
            $this->args = TagParser::loadAttrs($this->value);
        } else {
            $this->args = [];
        }
    }

    /**
     * Text escape
     *
     * @param string $text
     * @return string
     */
    protected function escape($text)
    {
        if ($this->goptions['escape']) {
            $text = \htmlspecialchars($text, \ENT_COMPAT, 'UTF-8');
        }
        return $text;
    }

    /**
     * The list of arguments
     * Initially - if FALSE - do not load the arguments
     *
     * @var mixed
     */
    protected $args = true;

    /**
     * The list of options
     * Initially - default options
     *
     * @var array
     */
    protected $options = [];

    /**
     * Global options
     *
     * @var array
     */
    protected $goptions;

    /**
     * Tag name
     *
     * @var string
     */
    protected $name;

    /**
     * The current value of the tag (can be trimmed)
     *
     * @var string
     */
    protected $value;

    /**
     * The errors list (array of messages)
     *
     * @var array
     */
    protected $errors = [];

    /**
     * Should the block be broken?
     *
     * @var string
     */
    protected $splitBlock = false;

    /**
     * Should the block be created?
     *
     * @var string
     */
    protected $createBlock = true;
}
