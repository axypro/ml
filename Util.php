<?php
/**
 * @package axy/ml
 */

namespace axy\ml;

/**
 * Some utilites
 *
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */
class Util
{
    /**
     * Extract head information without tokenize (for fast processing)
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
                $fp = @\fopen($options['filename'], 'rt');
                if (!$fp) {
                    throw new \RuntimeException('File not found');
                }
            } else {
                $content = \file_get_contents($options['filename']);
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
                $line = \rtrim(\fgets($fp));
                $process = !\feof($fp);
            } else {
                $parts = \explode("\n", $content, 2);
                $line = \rtrim($parts[0]);
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
            switch (\substr($line, 1, 1)) {
                case '#':
                    break;
                case '=':
                    if ($meta !== null) {
                        $m = \explode(':', \substr($line, 2), 2);
                        $name = \trim($m[0]);
                        if ($name !== null) {
                            $meta[$name] = isset($m[1]) ? \trim($m[1]) : true;
                        }
                    }
                    break;
                default:
                    if ($result->title === null) {
                        if (\preg_match('/^#(\[.*?\])?(.*?)$/is', $line, $matches)) {
                            $result->title = \trim($matches[2]);
                            if ($meta === null) {
                                break;
                            }
                        }
                    }
            }
        }
        if ($fp) {
            \fclose($fp);
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
                    $content = \file_get_contents($options['filename']);
                }
                $presult = $parser->parse($content);
                $result->title = $presult->title;
                $result->meta = $presult->meta;
            }
        }
        return $result;
    }

    /**
     * Creates a menu from a headers list
     *
     * @param array|\axy\ml\Result $result
     * @param int $min [optional]
     * @param int $max [optional]
     * @return array
     */
    public static function createMenu($result, $min = 2, $max = null)
    {
        if ($result instanceof \axy\ml\Result) {
            $result = $result->getHeaders($max);
        }
        if (!\is_array($result)) {
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
     * Render a menu
     *
     * @param mixed $result
     *        a Result instance, an array of headers or a menu struct
     * @param string $nl [optional]
     *        a line break
     * @param int $min [optional]
     * @param int $max [optional]
     * @return string
     *         a result html
     */
    public static function renderMenu($result, $nl = \PHP_EOL, $min = 2, $max = null)
    {
        if ((!\is_array($result)) || (!isset($result[0])) || (!isset($result[0]['subs']))) {
            $result = self::createMenu($result, $min, $max);
        }
        if (empty($result)) {
            return '';
        }
        return self::renderMenuSubs($result, $nl);
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
                \array_shift($headers);
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
            $title = \htmlspecialchars($item['title'], \ENT_COMPAT, 'UTF-8');
            if ($item['link'] !== null) {
                $link = \htmlspecialchars($item['link'], \ENT_COMPAT, 'UTF-8');
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
}
