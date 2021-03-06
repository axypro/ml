<?php
/**
 * @package axy\ml
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */

namespace axy\ml\helpers;

/**
 * The class of a single token
 *
 * @property array $subs
 *           the list of inline tokens for a block
 * @property string $name
 *           the anchor name
 * @property string $content
 *           the content of the token
 * @property int $level
 *           the level of a header
 * @property string $link
 *           the link of a header
 */
class Token extends \stdClass
{
    /**
     * The header
     * (name, level, content)
     *
     * @var string
     */
    const TYPE_HEADER = 'header';

    /**
     * The anchor
     * (name)
     *
     * @var string
     */
    const TYPE_ANCHOR = 'anchor';

    /**
     * The block (paragraph)
     * (subs)
     *
     * @var string
     */
    const TYPE_BLOCK = 'block';

    /**
     * The piece of text
     * (content)
     *
     * @var string
     */
    const TYPE_TEXT = 'text';

    /**
     * The axyml-tag
     * (name, content)
     *
     * @var string
     */
    const TYPE_TAG = 'tag';

    /**
     * HTML insert (replaceTokens)
     * (content)
     *
     * @var string
     */
    const TYPE_HTML = 'html';

    /**
     * The type of token
     * (constant Token::TYPE_*)
     *
     * @var string
     */
    public $type;

    /**
     * The line where the token was found
     *
     * @var int
     */
    public $line;

    /**
     * The constructor
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
     * Append a token to the block subtokens list
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
     * Returns a list of subs
     *
     * @return array
     */
    public function getSubs()
    {
        return empty($this->subs) ? [] : $this->subs;
    }

    /**
     * Represents the token as array
     *
     * @return array
     */
    public function asArray()
    {
        $result = (array)$this;
        if (!empty($result['subs'])) {
            foreach ($result['subs'] as &$item) {
                $item = $item->asArray();
            }
            unset($item);
        }
        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return '['.$this->type.' token:'.$this->line.']';
    }
}
