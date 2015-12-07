<?php
/**
 * @package axy\ml
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */

namespace axy\ml;

use axy\magic\LazyField;
use axy\magic\ReadOnly;

/**
 * The item of parsing error
 *
 * @property-read string $code
 * @property-read int $line
 * @property-read string $message
 * @property-read array $data
 */
class Error
{
    use LazyField;
    use ReadOnly;

    const TAG_UNKNOWN = 'tag_unknown';
    const TAG_NOT_CLOSED = 'tag_not_closed';
    const TAG_INVALID = 'tag_invalid';
    const HEADER_EMPTY = 'header_empty';
    const META_EMPTY = 'meta_empty';

    /**
     * The constructor
     *
     * @param string $code
     * @param int $line [optional]
     * @param array $data [optional]
     */
    public function __construct($code, $line = null, array $data = [])
    {
        $this->magicInit();
        $this->magicFields['fields'] = [
            'code' => $code,
            'line' => $line,
            'data' => $data,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        $message = $this->__get('message');
        $line = $this->magicFields['fields']['line'];
        if ($line !== null) {
            $message .= ' on line '.$line;
        }
        return $message;
    }

    /**
     * Sorts the errors list by number of lines
     *
     * @param array $errors
     * @return array
     */
    public static function sortListByLine(array $errors)
    {
        $cmp = function ($a, $b) {
            if ($a->line > $b->line) {
                return 1;
            } elseif ($a->line < $b->line) {
                return -1;
            }
            return 0;
        };
        usort($errors, $cmp);
        return $errors;
    }

    /**
     * {@inheritdoc}
     */
    protected $magicDefaults = [
        'loaders' => [
            'message' => '::createMessage'
        ],
    ];

    /**
     * @return string
     */
    protected function createMessage()
    {
        $code = $this->magicFields['fields']['code'];
        $data = $this->magicFields['fields']['data'];
        if (isset($this->messages[$code])) {
            $tpl = $this->messages[$code];
        } else {
            $tpl = $this->messages[''];
        }
        $callback = function ($m) use ($data) {
            $m = trim($m[1]);
            return isset($data[$m]) ? $data[$m] : '';
        };
        return preg_replace_callback('/{{(.*?)}}/', $callback, $tpl);
    }

    /**
     * Message templates
     *
     * @var array
     */
    private $messages = [
        self::TAG_UNKNOWN => 'Unknown tag [{{tag}}]',
        self::TAG_NOT_CLOSED => 'Tag [{{tag}}] is not closed',
        self::TAG_INVALID => 'Invalid [{{tag}}]: {{info}}',
        self::HEADER_EMPTY => 'Header is empty',
        self::META_EMPTY => 'Meta is empty',
        '' => 'Unknown error',
    ];
}
