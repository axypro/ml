<?php
/**
 * @package axy\ml
 */

namespace axy\ml\helpers;

use axy\ml\Error;

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
            $this->loadNextLine();
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
     * Get the meta data
     *
     * @return \axy\ml\Meta
     */
    public function getMeta()
    {
        return $this->meta;
    }

    /**
     * Get the list of errors
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Check if the document was cutted by more-anchor
     *
     * @return boolean
     */
    public function isCutted()
    {
        return $this->cutted;
    }

    /**
     * Load and parse a next line from the content
     */
    private function loadNextLine()
    {
        if ($this->content === '') {
            $this->process = false;
            return;
        }
        $this->numline++;
        $e = \explode("\n", $this->content, 2);
        $line = \rtrim($e[0]);
        $this->content = isset($e[1]) ? $e[1] : '';
        if ($line === '') {
            $this->block = null;
            return;
        }
        if ($line[0] === '#') {
            $this->loadSpecLine($line);
        } else {
            $this->loadFromBlock($line);
        }
    }

    /**
     * Load a special line (begins with "#")
     *
     * @param string $line
     */
    private function loadSpecLine($line)
    {
        $this->block = null;
        if ($line === '#') { // empty header
            $this->errors[] = new Error(Error::HEADER_EMPTY, $this->numline);
            return;
        }
        switch ($line[1]) {
            case '*': // comment #*
                return;
            case '=': // meta data #=
                $line = \ltrim(\substr($line, 2));
                if ($line === '') {
                    $this->errors[] = new Error(Error::META_EMPTY, $this->numline);
                    return;
                }
                $line = \explode(':', $line, 2);
                $this->meta[\rtrim($line[0])] = isset($line[1]) ? \ltrim($line[1]) : true;
                return;
        }
        if (!\preg_match('/^(#+)(\[(.*?)\])?\s*(.*)$/s', $line, $matches)) {
            return;
        }
        $ename = !empty($matches[2]);
        $name = $ename ? \trim($matches[3]) : null;
        $content = $matches[4];
        if ($content !== '') { // content is not empty - this is header
            $token = new Token(Token::TYPE_HEADER, $this->numline);
            $token->content = $content;
            $token->name = $name;
            $token->level = \strlen($matches[1]);
        } elseif ($ename !== null) { // content is empty, but exists name - anchor
            $token = new Token(Token::TYPE_ANCHOR, $this->numline);
            $token->name = $name;
        } else { // empty header
            $this->errors[] = new Error(Error::HEADER_EMPTY, $this->numline);
            return;
        }
        if (($this->cut !== null) && ($this->cut === $name)) { // cut document by anchor
            $this->process = false;
            $this->cutted = true;
            return;
        }
        $this->tokens[] = $token;
    }

    /**
     * Load a line from a text block
     *
     * @param string $line
     */
    private function loadFromBlock($line)
    {
        $fblock = (!$this->block);
        if ($fblock) {
            $this->block = new Token(Token::TYPE_BLOCK, $this->numline);
            $this->text = null;
            $this->tokens[] = $this->block;
        }
        $first = true;
        while ($line !== '') {
            $e = \explode('[', $line, 2);
            $text = ($fblock && $first) ? \ltrim($e[0]) : $e[0];
            $etag = isset($e[1]);
            if ($text !== '') {
                if (!$this->text) {
                    $this->text = new Token(Token::TYPE_TEXT, $this->numline);
                    $this->block->append($this->text);
                    $this->text->content = $text;
                } else {
                    $this->text->content .= ($first ? "\n" : '').$text;
                }
            } elseif ($first && $this->text) {
                $this->text->content .= "\n";
            }
            if (!$etag) {
                break;
            }
            $first = false;
            $line = $e[1];
            $this->text = null;
            $this->loadTag($line);
            if ($line === '') {
                break;
            }
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
        $err = !isset($e[1]);
        if (!$err) {
            $this->content = $e[1];
            $err =  false;
        } else {
            $this->content = '';
            $err = true;
        }
        $this->parseTag($tag, $err);
        $this->numline += \substr_count($tag, "\n") - 1;
    }

    /**
     * @param string $content
     * @param boolean $notclosed [optional]
     */
    private function parseTag($content, $notclosed = false)
    {
        $token = new Token(Token::TYPE_TAG, $this->numline);
        $this->block->append($token);
        if ($content === '') {
            return;
        }
        if ($content[0] === '/') {
            $token->name = '/';
            $token->content = \substr($content, 1);
            if ($notclosed) {
                $this->errors[] = new Error(Error::TAG_NOT_CLOSED, $this->numline, ['tag' => '/']);
            }
            return;
        }
        if (\preg_match('/^([a-z0-9_]+)(.*?)$/is', $content, $matches)) {
            $token->name = \strtolower($matches[1]);
            $token->content = $matches[2];
        } else {
            $token->name = null;
            $token->content = $content;
        }
        if ($notclosed) {
            $this->errors[] = new Error(Error::TAG_NOT_CLOSED, $this->numline, ['tag' => $token->name]);
        }
    }

    /**
     * The remaining content of the document
     *
     * @var string
     */
    private $content;

    /**
     * The anchor for cutting
     *
     * @var boolean
     */
    private $cut;

    /**
     * The tokens list of the document
     *
     * @var array
     */
    private $tokens = [];

    /**
     * The meta-data of the document
     *
     * @var \axy\ml\Meta
     */
    private $meta;

    /**
     * The list of document format errors
     *
     * @var array
     */
    private $errors = [];

    /**
     * The number of the current line
     *
     * @var int
     */
    private $numline = 0;

    /**
     * The flag indicating that the parsing is in the process
     *
     * @var boolean
     */
    private $process;

    /**
     * The token of the current block of text
     *
     * @var \axy\ml\helpers\Token
     */
    private $block;

    /**
     * The current text chunk
     *
     * @var string
     */
    private $text;

    /**
     * The flag of cutted document
     *
     * @var boolean
     */
    private $cutted = false;
}
