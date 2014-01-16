<?php
/**
 * @package axy\ml
 */

namespace axy\ml\helpers;

/**
 * The class of a single token
 *
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 *
 * @property-read array $subs
 * @property-read string $name
 * @property-read string $content
 */
class Token extends \stdClass
{
    const TYPE_HEADER = 'header';
    const TYPE_ANCHOR = 'anchor';
    const TYPE_BLOCK = 'block';
    const TYPE_TEXT = 'text';
    const TYPE_TAG = 'tag';
    const TYPE_LI = 'li';

    /**
     * @var string
     */
    public $type;

    /**
     * @var int
     */
    public $line;

    /**
     * Constructor
     *
     * @param string $type
     * @param int $line [optional]
     */
    public function __construct($type, $line = null)
    {
        $this->type = $type;
        $this->line = $line;
    }

    /**
     * Append token to subs
     *
     * @param \axy\ml\helpers\Token $token
     */
    public function append(Token $token)
    {
        if (empty($this->subs)) {
            $this->subs = [];
        }
        $this->subs[] = $token;
    }

    /**
     * Get list of subs
     *
     * @return array
     */
    public function getSubs()
    {
        return empty($this->subs) ? [] : $this->subs;
    }

    /**
     * Represent the token as array
     *
     * @return array
     */
    public function asArray()
    {
        $a = (array)$this;
        if (!empty($a['subs'])) {
            foreach ($a['subs'] as &$item) {
                $item = $item->asArray();
            }
            unset($item);
        }
        return $a;
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return '['.$this->type.' token:'.$this->line.']';
    }
}
