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
        $mt = \microtime(true);
        while ($this->process) {
            $this->loadNextLine();
        }
        $this->duration = \microtime(true) - $mt;
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
     * Get a duration of the parsing
     *
     * @return float
     */
    public function getDuration()
    {
        return $this->duration;
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
        $parts = \explode("\n", $this->content, 2);
        $line = \rtrim($parts[0]);
        $this->content = isset($parts[1]) ? $parts[1] : '';
        if ($line === '') {
            $this->block = null;
            return;
        }
        if ($line[0] === '#') {
            $this->loadSpecLine($line);
        } else {
            $this->loadBlockLine($line);
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
            case '*':
                /* comment #* */
                return;
            case '=':
                /* meta data #= */
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
        $defname = !empty($matches[2]);
        $name = $defname ? \trim($matches[3]) : null;
        $content = $matches[4];
        if ($content !== '') {
            /* content is not empty - this is header */
            $token = new Token(Token::TYPE_HEADER, $this->numline);
            $token->content = $content;
            $token->name = $name;
            $token->level = \strlen($matches[1]);
        } elseif ($defname) {
            /* content is empty, but exists name - anchor */
            $token = new Token(Token::TYPE_ANCHOR, $this->numline);
            $token->name = $name;
        } else {
            /* empty header */
            $this->errors[] = new Error(Error::HEADER_EMPTY, $this->numline);
            return;
        }
        if (($this->cut !== null) && ($this->cut === $name)) {
            /* cut document by anchor */
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
    private function loadBlockLine($line)
    {
        $firstline = (!$this->block); // this is a first line of the block
        if ($firstline) {
            /* create new block token */
            $this->block = new Token(Token::TYPE_BLOCK, $this->numline);
            $this->listblock = false;
            $this->tokens[] = $this->block;
            $this->text = null;
            $this->endtag = false;
        }
        $firstpart = true; // this is a first part of this line
        do {
            $parts = \explode('[', $line, 2);
            $text = ($firstline && $firstpart) ? \ltrim($parts[0]) : $parts[0];
            $etag = isset($parts[1]); // on this line was found a tag
            if ($text !== '') {
                if ($this->text) {
                    $this->text->content .= ($firstpart ? "\n" : '').$text;
                } else {
                    $this->text = new Token(Token::TYPE_TEXT, $this->numline);
                    $this->block->append($this->text);
                    if ($this->endtag) {
                        $text = "\n".$text;
                    }
                    $this->text->content = $text;
                }
            } elseif ($firstpart) {
                if ($this->text) {
                    $this->text->content .= "\n";
                } elseif ($etag && (!$firstline)) {
                    $text = new Token(Token::TYPE_TEXT, $this->numline);
                    $text->content = "\n";
                    $this->block->append($text);
                }
            }
            if (!$etag) {
                /* A tag was not found - end of line */
                break;
            }
            /* A tag was found - load it */
            $this->text = null; // curent text ended
            $line = $parts[1];
            $firstpart = false;
            $this->loadTag($line);
            if ($line === '') {
                $this->endtag = true;
                break;
            }
        } while (true);
    }

    /**
     * Load a tag from start of the content
     *
     * @param string &$line
     */
    private function loadTag(&$line)
    {
        /* Determine the size of the opening bracket */
        if (\preg_match('/^(\[*)(.*)$/s', $line, $matches)) {
            $len = \strlen($matches[1]) + 1;
            $line = $matches[2];
        } else {
            $len = 1;
            $line = '';
        }
        $close = \str_repeat(']', $len);
        $parts = \explode($close, $line, 2);
        if (isset($parts[1])) {
            /* The tag ends on the current line */
            $this->parseTag($parts[0]);
            $line = $parts[1];
        } else {
            /* The tag is multiline */
            $parts = \explode($close, $this->content, 2);
            $tagcontent = $line."\n".$parts[0];
            $isclosed = isset($parts[1]); // tag is correctly closed
            $this->parseTag($tagcontent, !$isclosed);
            $this->numline += \substr_count($tagcontent, "\n") - 1;
            if ($isclosed) {
                $parts = \explode("\n", $parts[1], 2);
                $line = ($parts[0]);
                $this->content = isset($parts[1]) ? $parts[1] : '';
                $this->numline++;
            } else {
                $this->content = '';
                $line = '';
            }
        }
    }

    /**
     * Parse tag
     *
     * @param string $content
     *        the tag content (after the tag name)
     * @param boolean $notclosed [optional]
     *        the tag is not closed
     */
    private function parseTag($content, $notclosed = false)
    {
        if ($content === '') {
            return;
        }
        $token = new Token(Token::TYPE_TAG, $this->numline);
        $this->block->append($token);
        switch ($content[0]) {
            case '/':
                $token->name = '/';
                $token->content = \substr($content, 1);
                break;
            case '*':
                $token->name = '*';
                $token->content = $content;
                break;
            default:
                if (\preg_match('/^([a-z0-9_]+)(.*?)$/is', $content, $matches)) {
                    $token->name = \strtolower($matches[1]);
                    $token->content = $matches[2];
                } else {
                    $token->name = null;
                    $token->content = $content;
                }
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

    /**
     * The current block is list
     *
     * @var boolean
     */
    private $listblock;

    /**
     * The previous line has a tag on the end
     *
     * @var boolean
     */
    private $endtag;

    /**
     * The parsing duration
     *
     * @var float
     */
    private $duration;
}
