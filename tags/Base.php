<?php
/**
 * @package axy\ml
 */

namespace axy\ml\tags;

use axy\ml\helpers\TagParser;

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
     * @param string $content
     * @param array $options [optional]
     */
    public function __construct($content, array $options = null)
    {
        $this->value = $content;
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
        return \htmlspecialchars($text, \ENT_COMPAT, 'UTF-8');
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
     * The current value of the tag (can be trimmed)
     *
     * @var string
     */
    protected $value;

    /**
     * The errors list. (array of messages)
     *
     * @var array
     */
    protected $errors = [];
}
