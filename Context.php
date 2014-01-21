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
 *                The result of parsing the current document
 * @property-read \axy\ml\Options $options
 *                The options of parsing
 * @property-read \axy\ml\TagsList $tags
 *                The list of available tags
 * @property-read mixed $custom
 *                The custom context
 * @property-read \axy\ml\helpers\Block $block
 *                A current block (during render)
 * @property-read array $errors
 *                The list of errors of last parsing
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
            'errors' => [],
            'block' => null,
        ];
    }

    /**
     * Set a current block (during render)
     *
     * @param \axy\ml\helpers\Block $block
     */
    public function setCurrentBlock(\axy\ml\helpers\Block $block = null)
    {
        $this->magicFields['fields']['block'] = $block;
    }

    /**
     * Initialization the errors list before rendering
     *
     * @param array $errors [optional]
     */
    public function initErrorsList(array $errors = [])
    {
        $this->magicFields['fields']['errors'] = $errors;
    }

    /**
     * Append an error to the errors list
     *
     * @param \axy\ml\Error $error
     */
    public function addError($error)
    {
        $this->magicFields['fields']['errors'][] = $error;
    }
}
