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
     * @param string $content
     *        a file name (must exists)
     * @param boolean $meta [optional]
     *        extract a meta data
     * @param boolean|\axy\ml\Parser $parser [optional]
     *        use the system parser if a title is not found
     * @param boolean $normalized [optional]
     *        flag that the content is normalized ("\n" and etc)
     * @return object
     *         (title, meta)
     */
    public static function extractHead($content, $meta = true, $parser = true, $normalized = false)
    {
        if (!$normalized) {
            $content = helpers\Normalizer::toParse($content, Config::getOptions());
        }
        $result = (object)[
            'title' => null,
            'meta' => null,
        ];
        $meta = $meta ? [] : null;
        $process = true;
        $remain = $content;
        while ($process) {
            $parts = \explode("\n", $remain, 2);
            $line = \rtrim($parts[0]);
            if (isset($parts[1])) {
                $remain = $parts[1];
            } else {
                $process = false;
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
        if ($meta !== null) {
            $result->meta = new Meta($meta);
        }
        if (($result->title === null) && $parser) {
            if (!($parser instanceof Parser)) {
                $parser = new Parser();
            }
            $presult = $parser->parse($content);
            $result->title = $presult->title;
            $result->meta = $presult->meta;
        }
        return $result;
    }
}
