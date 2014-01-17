<?php
/**
 * @package \axy\ml
 */

namespace axy\ml;

/**
 * The context of parsing
 *
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 *
 * @property-read \axy\ml\Result $result
 * @property-read \axy\ml\Options $options
 * @property-read \axy\ml\TagsList $tags
 * @property-read mixed $custom
 */
class Context
{
    use \axy\magic\LazyField;
    use \axy\magic\ReadOnly;

    /**
     * Constructor
     *
     * @param \axy\ml\Result $result
     * @param \axy\ml\Options $options
     * @param \axy\ml\TagList $tags
     * @param mixed $custom
     */
    public function __construct(Result $result, Options $options, TagsList $tags, $custom)
    {
        $this->magicFields['fields'] = [
            'result' => $result,
            'options' => $options,
            'tags' => $tags,
            'custom' => $custom,
        ];
    }
}
