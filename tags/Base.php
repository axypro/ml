<?php
/**
 * @package axy\ml
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */

namespace axy\ml\tags;

use axy\ml\helpers\TagParser;

/**
 * The basic class of axyml-tags
 */
abstract class Base
{
    /**
     * The constructor
     *
     * @param string $name
     *        the tag name
     * @param string $content
     *        the content of the tag
     * @param array $options [optional]
     *        the custom options
     * @param \axy\ml\Context $context [optional]
     *        the parsing context
     */
    public function __construct($name, $content, array $options = null, $context = null)
    {
        $this->name = $name;
        $this->value = $content;
        $this->context = $context;
        $this->sEscape = $context ? $context->options->escape : true;
        if ($options) {
            $this->options = array_replace($this->options, $options);
        }
        $this->preparse();
        $this->parse();
    }

    /**
     * Returns the HTML representation of tag
     *
     * @return string
     */
    abstract public function getHTML();

    /**
     * Returns the plain text representation of tag
     *
     * @return string
     */
    public function getPlain()
    {
        return $this->getHTML();
    }

    /**
     * Returns the errors list
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
     * Returns a next component of the value
     *
     * @return string
     */
    protected function getNextComponent()
    {
        return TagParser::loadNextComponent($this->value);
    }

    /**
     * Returns a last component of the value
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
        if ($this->sEscape) {
            $text = htmlspecialchars($text, \ENT_COMPAT, 'UTF-8');
        }
        return $text;
    }

    /**
     * Returns an argument by number
     *
     * @param int $num [optional]
     * @param mixed $default [optional]
     * @return string
     */
    protected function getArg($num = 0, $default = null)
    {
        $num = $num ?: 0;
        return isset($this->args[$num]) ? $this->args[$num] : $default;
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
     * The parsing context
     *
     * @var \axy\ml\Context
     */
    protected $context;

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
     * The text should be escaped
     *
     * @var boolean
     */
    private $sEscape;
}
