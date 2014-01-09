<?php
/**
 * @package axy\ml
 */

namespace axy\ml\helpers;

/**
 * The axyML-tokenizer
 *
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */
class Tokenizer
{
    /**
     * Constructor
     *
     * @param string $content
     */
    public function __construct($content)
    {
        $this->content = $content;
    }

    /**
     * Tokenize, please!
     *
     * @param string $cut [optional]
     */
    public function tokenize($cut = null)
    {
        if ($this->meta) {
            return true;
        }
        $this->cut = $cut;
        $this->meta = new \axy\ml\Meta();
        $this->process = true;
        while ($this->process) {
            $this->loadNextBlock();
        }
    }

    /**
     * Get the tokens list
     *
     * @return array
     */
    public function getTokens()
    {
        return $this->tokens;
    }

    /**
     * Get meta data
     *
     * @return \axy\ml\Meta
     */
    public function getMeta()
    {
        return $this->meta;
    }

    /**
     * Get list of errors
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Check if content was cutted by more-anchor
     *
     * @return boolean
     */
    public function isCutted()
    {
        return $this->cutted;
    }

    /**
     * Load and parse a next block from the content
     */
    private function loadNextBlock()
    {
        if (empty($this->content)) {
            $this->process = false;
            return;
        }
        $this->numline++;
        $e = \explode("\n", $this->content, 2);
        $line = $e[0];
        $this->content = isset($e[1]) ? $e[1] : '';
        if (empty($line)) {
            $this->block = null;
            return;
        }
        if ($line[0] === '#') {
            $this->loadSign($line);
        } else {
            $this->loadFromBlock($line);
        }
    }

    /**
     * @param string $line
     */
    private function loadSign($line)
    {
        $this->block = null;
        $line = \rtrim($line);
        if ($line === '#') {
            return;
        }
        switch ($line[1]) {
            case '*':
                return;
            case '=':
                $line = \preg_replace('/^#=\s*/is', '', $line);
                if (empty($line)) {
                    return;
                }
                $line = \explode(':', $line, 2);
                $this->meta[\rtrim($line[0])] = isset($line[1]) ? \ltrim($line[1]) : true;
                return;
        }
        if (!\preg_match('/^(#+)(.*)$/s', $line, $matches)) {
            return;
        }
        $level = \strlen($matches[1]);
        $line = $matches[2];
        if (empty($line)) {
            return;
        }
        if ($line[0] === '[') {
            $e = \explode(']', \substr($line, 1), 2);
            if (!isset($e[1])) {
                return;
            }
            $name = \trim($e[0]);
            $content = \trim($e[1]);
        } else {
            $name = null;
            $content = \trim($line);
            if (empty($content)) {
                return;
            }
        }
        if (($name !== null) && ($name === $this->cut)) {
            $this->process = false;
            $this->cutted = true;
            return;
        }
        if (empty($content)) {
            $token = new Token(Token::TYPE_ANCHOR, $this->numline);
            $token->name = $name;
        } else {
            $token = new Token(Token::TYPE_HEADER, $this->numline);
            $token->name = $name;
            $token->content = $content;
            $token->level = $level;
        }
        $this->tokens[] = $token;
    }

    /**
     * @param string $line
     */
    private function loadFromBlock($line)
    {
        $line = \rtrim($line);
        if (empty($line)) {
            return;
        }
        $fblock = (!$this->block);
        if ($fblock) {
            $this->block = new Token(Token::TYPE_BLOCK, $this->numline);
            $this->text = null;
            $this->tokens[] = $this->block;
        }
        $first = true;
        while (!empty($line)) {
            $e = \explode('[', $line, 2);
            $text = ($fblock && $first) ? \ltrim($e[0]) : $e[0];
            if (!empty($text)) {
                if (!$this->text) {
                    $this->text = new Token(Token::TYPE_TEXT, $this->numline);
                    $this->block->append($this->text);
                    $this->text->content = $text;
                } else {
                    $this->text->content .= ($first ? "\n" : '').$text;
                }
            }
            $first = false;
            if (!isset($e[1])) {
                break;
            }
            $line = $e[1];
            $this->text = null;
            $this->loadTag($line);
        }
    }

    /**
     * @param string &$line
     */
    private function loadTag(&$line)
    {
        if (\preg_match('/^(\[*)(.*)$/s', $line, $matches)) {
            $len = \strlen($matches[1]) + 1;
            $line = $matches[2];
        } else {
            $len = 1;
            $line = '';
        }
        $close = \str_repeat(']', $len);
        $e = \explode($close, $line, 2);
        if (isset($e[1])) {
            $this->parseTag($e[0]);
            $line = $e[1];
            return;
        }
        $line .= "\n".$this->content;
        $e = \explode($close, $line, 2);
        $line = '';
        $tag = $e[0];
        if (isset($e[1])) {
            $this->content = $e[1];
        } else {
            $this->content = ''; // @todo error
        }
        $this->parseTag($tag);
        $this->numline += \substr_count($tag, "\n") - 1;
    }

    /**
     * @param string $content
     */
    private function parseTag($content)
    {
        $token = new Token(Token::TYPE_TAG, $this->numline);
        $this->block->append($token);
        if (empty($content)) {
            return;
        }
        if ($content[0] === '/') {
            $token->name = '/';
            $token->content = \substr($content, 1);
            return;
        }
        if (\preg_match('/^([a-z0-9_]+)(.*?)$/is', $content, $matches)) {
            $token->name = \strtolower($matches[1]);
            $token->content = $matches[2];
        } else {
            $token->name = null;
            $token->content = $content;
        }
    }

    /**
     * @var string
     */
    private $content;

    /**
     * @var array
     */
    private $tokens = [];

    /**
     * @var \axy\ml\Meta
     */
    private $meta;

    /**
     * @var array
     */
    private $errors = [];

    /**
     * @var int
     */
    private $numline = 0;

    /**
     * @var boolean
     */
    private $process;

    /**
     * @var \axy\ml\helpers\Token
     */
    private $block;

    /**
     * @var \axy\ml\helpers\Token
     */
    private $text;

    /**
     * @var boolean
     */
    private $cut;

    /**
     * @var boolean
     */
    private $cutted = false;
}
