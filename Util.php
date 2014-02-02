<?php
/**
 * @package axy/ml
 */

namespace axy\ml;

use axy\ml\helpers\Config;
use axy\ml\helpers\Normalizer;

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
     *
     * @param array $options
     * @return object
     *         (title, meta)
     */
    public static function extractHead(array $options)
    {
        if (isset($options['content'])) {
            $content = $options['content'];
            $fp = null;
        } elseif (isset($options['filename'])) {
            $fp = @\fopen($options['filename'], 'rt');
            if (!$fp) {
                throw new \RuntimeException('File not found');
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
}
