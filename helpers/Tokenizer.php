<?php
/**
 * @package axy\ml
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */

namespace axy\ml\helpers;

use axy\ml\Error;
use axy\ml\Meta;

/**
 * The axyML-tokenizer
 */
class Tokenizer
{
    /**
     * The constructor
     *
     * @param string $content
     * @param array $options [optional]
     */
    public function __construct($content, $options = null)
    {
        $this->content = $content;
        if ($options) {
            $brackets = $options['brackets'];
            $this->openBracket = $brackets[0];
            $this->closeBracket = $brackets[1];
            if (isset($options['hLinkPrefix'])) {
                $this->hLinkPrefix = $options['hLinkPrefix'];
            }
            $this->hLinkNeed = !empty($options['hLinkNeed']);
        } else {
            $this->openBracket = '[';
            $this->closeBracket = ']';
        }
        $len = strlen($this->openBracket);
        if ($len > 1) {
            $this->openLength = $len;
            $this->openPattern = '/^(('.preg_quote($this->openBracket, '/').')*)(.*)$/s';
        } else {
            $this->openLength = null;
            $this->openPattern = '/^('.preg_quote($this->openBracket, '/').'*)(.*)$/s';
        }
    }

    /**
     * Tokenize, please!
     *
     * @param string $cut [optional]
     * @return bool
     */
    public function tokenize($cut = null)
    {
        if ($this->meta) {
            return true;
        }
        $this->cut = $cut;
        $this->meta = new Meta();
        $this->process = true;
        $mt = microtime(true);
        while ($this->process) {
            $this->loadNextLine();
        }
        $this->duration = microtime(true) - $mt;
    }

    /**
     * Returns the tokens list
     *
     * @return array
     */
    public function getTokens()
    {
        return $this->tokens;
    }

    /**
     * Returns the meta data
     *
     * @return Meta
     */
    public function getMeta()
    {
        return $this->meta;
    }

    /**
     * Returns the list of errors
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Checks if the document was cutted by more-anchor
     *
     * @return boolean
     */
    public function isCutted()
    {
        return $this->cutted;
    }

    /**
     * Returns a duration of the parsing
     *
     * @return float
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * Replaces a tokens list
     *
     * @param array $tokens
     */
    public function replaceTokens(array $tokens)
    {
        $this->tokens = $tokens;
    }

    /**
     * Loads and parses a next line from the content
     */
    private function loadNextLine()
    {
        if ($this->content === '') {
            $this->process = false;
            return;
        }
        $this->numline++;
        $parts = explode("\n", $this->content, 2);
        $line = rtrim($parts[0]);
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
     * Loads a special line (begins with "#")
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
                $line = ltrim(substr($line, 2));
                if ($line === '') {
                    $this->errors[] = new Error(Error::META_EMPTY, $this->numline);
                    return;
                }
                $line = explode(':', $line, 2);
                $this->meta[rtrim($line[0])] = isset($line[1]) ? ltrim($line[1]) : true;
                return;
        }
        if (!preg_match('/^(#+)(\[(.*?)\])?\s*(.*)$/s', $line, $matches)) {
            return;
        }
        $defName = !empty($matches[2]);
        $name = $defName ? trim($matches[3]) : null;
        $content = $matches[4];
        if ($content !== '') {
            /* content is not empty - this is header */
            $token = new Token(Token::TYPE_HEADER, $this->numline);
            $token->content = $content;
            $token->name = $name;
            $token->level = strlen($matches[1]);
            if ($defName) {
                $token->link = $this->hLinkPrefix.$name;
            } elseif ($this->hLinkNeed) {
                $this->hLinkNum++;
                $token->link = $this->hLinkPrefix.'h-'.$this->hLinkNum;
            } else {
                $token->link = null;
            }
        } elseif ($defName) {
            /* content is empty, but exists name - anchor */
            $token = new Token(Token::TYPE_ANCHOR, $this->numline);
            $token->name = $name;
            $token->link = $this->hLinkPrefix.$token->name;
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
        $open = $this->openBracket;
        $firstLine = (!$this->block); // this is a first line of the block
        if ($firstLine) {
            /* create new block token */
            $this->block = new Token(Token::TYPE_BLOCK, $this->numline);
            $this->listblock = false;
            $this->tokens[] = $this->block;
            $this->text = null;
            $this->endtag = false;
        }
        $firstPart = true; // this is a first part of this line
        do {
            $parts = explode($open, $line, 2);
            $text = ($firstLine && $firstPart) ? ltrim($parts[0]) : $parts[0];
            $etag = isset($parts[1]); // on this line was found a tag
            if ($text !== '') {
                if ($this->text) {
                    $this->text->content .= ($firstPart ? "\n" : '').$text;
                } else {
                    $this->text = new Token(Token::TYPE_TEXT, $this->numline);
                    $this->block->append($this->text);
                    if ($this->endtag) {
                        $text = "\n".$text;
                        $this->endtag = false;
                    }
                    $this->text->content = $text;
                }
            } elseif ($firstPart) {
                if ($this->text) {
                    $this->text->content .= "\n";
                } elseif ($etag && (!$firstLine)) {
                    $text = new Token(Token::TYPE_TEXT, $this->numline);
                    $text->content = "\n";
                    $this->block->append($text);
                }
                $this->endtag = false;
            }
            if (!$etag) {
                /* A tag was not found - end of line */
                break;
            }
            /* A tag was found - load it */
            $this->text = null; // curent text ended
            $line = $parts[1];
            $firstPart = false;
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
        if (preg_match($this->openPattern, $line, $matches)) {
            if ($this->openLength) {
                if ($matches[1] !== '') {
                    $len = (int)(strlen($matches[1]) / $this->openLength) + 1;
                    $line = $matches[3];
                } else {
                    $len = 1;
                }
            } else {
                $len = strlen($matches[1]) + 1;
                $line = $matches[2];
            }
        } else {
            $len = 1;
            $line = '';
        }
        $close = str_repeat($this->closeBracket, $len);
        $parts = explode($close, $line, 2);
        if (isset($parts[1])) {
            /* The tag ends on the current line */
            $this->parseTag($parts[0]);
            $line = $parts[1];
        } else {
            /* The tag is multiline */
            $parts = explode($close, $this->content, 2);
            $tagContent = $line."\n".$parts[0];
            $isClosed = isset($parts[1]); // tag is correctly closed
            $this->parseTag($tagContent, !$isClosed);
            $this->numline += substr_count($tagContent, "\n") - 1;
            if ($isClosed) {
                $parts = explode("\n", $parts[1], 2);
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
     * Parses a tag
     *
     * @param string $content
     *        the tag content (after the tag name)
     * @param boolean $notClosed [optional]
     *        the tag is not closed
     */
    private function parseTag($content, $notClosed = false)
    {
        if ($content === '') {
            return;
        }
        $token = new Token(Token::TYPE_TAG, $this->numline);
        $this->block->append($token);
        switch ($content[0]) {
            case '/':
                $token->name = '/';
                $token->content = substr($content, 1);
                break;
            case '*':
                $token->name = '*';
                $token->content = $content;
                break;
            default:
                if (preg_match('/^([a-z0-9_]+)(.*?)$/is', $content, $matches)) {
                    $token->name = strtolower($matches[1]);
                    $token->content = $matches[2];
                } else {
                    $token->name = null;
                    $token->content = $content;
                }
        }
        if ($notClosed) {
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
     * @var Meta
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

    /**
     * The open bracket ("[" by default)
     *
     * @var boolean
     */
    private $openBracket;

    /**
     * The close bracket ("]" by default)
     *
     * @var boolean
     */
    private $closeBracket;

    /**
     * The length of the open bracket ("[" - 1)
     *
     * @var int
     */
    private $openLength;

    /**
     * The PCRE-pattern for open brackets
     *
     * @var string
     */
    private $openPattern;

    /**
     * The prefix for a header link
     *
     * @var string
     */
    private $hLinkPrefix = '';

    /**
     * The flag that a header link is necessary
     *
     * @var boolean
     */
    private $hLinkNeed = false;

    /**
     * The number of anonymous headers
     *
     * @var int
     */
    private $hLinkNum = 0;
}
