<?php
/**
 * @package axy/ml
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */

namespace axy\ml;

use axy\ml\helpers\Token;

/**
 * Some utilites
 */
class Util
{
    /**
     * Extracts the header information without tokenize (for fast processing)
     *
     * Options:
     * "content" - a content of the document
     * "filename" - a file name of the document
     * "meta" - extract meta data (true by default)
     * "parser" - use the system parser if a title is not found (true by default)
     * "fullload" - a file full load
     *
     * @param array $options
     * @return object
     *         (title, meta)
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     */
    public static function extractHead(array $options)
    {
        $fp = null;
        if (isset($options['content'])) {
            $content = $options['content'];
        } elseif (isset($options['filename'])) {
            if (empty($options['fullload'])) {
                $fp = @fopen($options['filename'], 'rt');
                if (!$fp) {
                    throw new \RuntimeException('File not found');
                }
                $content = null;
            } else {
                $content = file_get_contents($options['filename']);
            }
        } else {
            throw new \InvalidArgumentException('extractHead require content or filename');
        }
        $result = (object)[
            'title' => null,
            'meta' => null,
        ];
        if ((!isset($options['meta'])) || $options['meta']) {
            $meta = [];
        } else {
            $meta = null;
        }
        $process = true;
        while ($process) {
            if ($fp) {
                $line = rtrim(fgets($fp));
                $process = !feof($fp);
            } else {
                $parts = explode("\n", $content, 2);
                $line = rtrim($parts[0]);
                if (isset($parts[1])) {
                    $content = $parts[1];
                } else {
                    $process = false;
                }
            }
            if ($line === '') {
                continue;
            }
            if ($line[0] !== '#') {
                break;
            }
            switch (substr($line, 1, 1)) {
                case '#':
                    break;
                case '=':
                    if ($meta !== null) {
                        $m = explode(':', substr($line, 2), 2);
                        $name = trim($m[0]);
                        if ($name !== null) {
                            $meta[$name] = isset($m[1]) ? trim($m[1]) : true;
                        }
                    }
                    break;
                default:
                    if ($result->title === null) {
                        if (preg_match('/^#(\[.*?\])?(.*?)$/is', $line, $matches)) {
                            $result->title = trim($matches[2]);
                            if ($meta === null) {
                                break;
                            }
                        }
                    }
            }
        }
        if ($fp) {
            fclose($fp);
        }
        if ($meta !== null) {
            $result->meta = new Meta($meta);
        }
        if ($result->title === null) {
            $parser = isset($options['parser']) ? $options['parser'] : true;
            if ($parser) {
                if (!($parser instanceof Parser)) {
                    $parser = new Parser();
                }
                if ($fp) {
                    $content = file_get_contents($options['filename']);
                }
                $pResult = $parser->parse($content);
                $result->title = $pResult->title;
                $result->meta = $pResult->meta;
            }
        }
        return $result;
    }

    /**
     * Creates a menu from a headers list
     *
     * @param array|Result $result
     * @param int $min [optional]
     * @param int $max [optional]
     * @return array
     */
    public static function createMenu($result, $min = 2, $max = null)
    {
        if ($result instanceof Result) {
            $result = $result->getHeaders($max);
        }
        if (!is_array($result)) {
            throw new \InvalidArgumentException();
        }
        $headers = [];
        foreach ($result as $header) {
            $level = $header['level'];
            if (($level >= $min) && ((!$max) || ($level <= $max))) {
                $headers[] = $header;
            }
        }
        return self::loadMenuSubs($min - 1, $headers);
    }

    /**
     * Renders a menu
     *
     * @param mixed $result
     *        a Result instance, an array of headers or a menu structure
     * @param string $nl [optional]
     *        a line break
     * @param int $min [optional]
     * @param int $max [optional]
     * @return string
     *         a result html
     */
    public static function renderMenu($result, $nl = PHP_EOL, $min = 2, $max = null)
    {
        if ((!is_array($result)) || (!isset($result[0])) || (!isset($result[0]['subs']))) {
            $result = self::createMenu($result, $min, $max);
        }
        if (empty($result)) {
            return '';
        }
        return self::renderMenuSubs($result, $nl);
    }

    /**
     * Merges two custom tags list
     *
     * @param array $a
     * @param array $b
     * @return array
     */
    public static function mergeCustomTagsList(array $a, array $b)
    {
        foreach ($b as $k => $v) {
            if (!isset($a[$k])) {
                $a[$k] = $v;
            } elseif ($v === null) {
                $a[$k] = null;
            } else {
                $aa = self::normalizeTag($a[$k]);
                $v = self::normalizeTag($v);
                if (array_key_exists('classname', $v)) {
                    $aa['classname'] = $v['classname'];
                }
                if (array_key_exists('name', $v)) {
                    $aa['name'] = $v['name'];
                }
                if (isset($v['options'])) {
                    if (isset($aa['options'])) {
                        $aa['options'] = array_replace($aa['options'], $v['options']);
                    } else {
                        $aa['options'] = $v['options'];
                    }
                }
                $a[$k] = $aa;
            }
        }
        return $a;
    }

    /**
     * Inserts a HTML code after a title in a result
     *
     * @param Result $result
     * @param string $html
     */
    public static function insertHTMLAfterTitle(Result $result, $html)
    {
        $tokens = $result->tokens;
        $kTitle = null;
        foreach ($tokens as $k => $token) {
            if (($token->type === Token::TYPE_HEADER) && ($token->level === 1)) {
                $kTitle = $k;
                break;
            }
        }
        $hToken = new Token(Token::TYPE_HTML);
        $hToken->content = $html;
        if ($kTitle !== null) {
            array_splice($tokens, $kTitle + 1, 0, [$hToken]);
        } else {
            array_unshift($tokens, $hToken);
        }
        $result->replaceTokens($tokens);
    }

    /**
     * @param int $level
     * @param array &$headers
     * @return array
     */
    private static function loadMenuSubs($level, &$headers)
    {
        $subs = [];
        while (true) {
            if (empty($headers)) {
                break;
            }
            $current = $headers[0];
            $delta = $current['level'] - $level;
            if ($delta <= 0) {
                break;
            }
            if ($delta === 1) {
                array_shift($headers);
                $subs[] = [
                    'title' => $current['content'],
                    'link' => $current['link'],
                    'level' => $current['level'],
                    'subs' => self::loadMenuSubs($current['level'], $headers),
                ];
            } else {
                $subs[] = [
                    'title' => null,
                    'link' => null,
                    'level' => $level + 1,
                    'subs' => self::loadMenuSubs($level + 1, $headers),
                ];
            }
        }
        return $subs;
    }

    /**
     * @param array $subs
     * @param string $nl
     * @return string
     */
    private static function renderMenuSubs($subs, $nl)
    {
        $result = '';
        foreach ($subs as $item) {
            $result .= '<li>';
            $title = htmlspecialchars($item['title'], ENT_COMPAT, 'UTF-8');
            if ($item['link'] !== null) {
                $link = htmlspecialchars($item['link'], ENT_COMPAT, 'UTF-8');
                $result .= '<a href="#'.$link.'">'.$title.'</a>';
            } else {
                $result .= $title;
            }
            if (!empty($item['subs'])) {
                $s = self::renderMenuSubs($item['subs'], $nl);
                $result .= $nl.$s.$nl;
            }
            $result .= '</li>'.$nl;
        }
        return '<ol>'.$nl.$result.'</ol>';
    }

    /**
     * @param mixed $tag
     * @return array
     */
    private static function normalizeTag($tag)
    {
        if (!is_array($tag)) {
            return ['classname' => $tag];
        }
        if (!array_key_exists(0, $tag)) {
            return $tag;
        }
        $result = [
            'classname' => $tag[0],
        ];
        if (isset($tag[1]) && is_array($tag[1])) {
            $result['options'] = $tag[1];
        }
        if (array_key_exists(2, $tag)) {
            $result['name'] = $tag[2];
        }
        return $result;
    }
}
