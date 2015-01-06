<?php
/**
 * @package \axy\ml
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */

namespace axy\ml;

use axy\ml\helpers\Block;
use axy\magic\LazyField;
use axy\magic\ReadOnly;

/**
 * The context of the parsing
 *
 * @property-read \axy\ml\Result $result
 *                the result of the parsing of the current document
 * @property-read \axy\ml\Options $options
 *                the parsing options
 * @property-read \axy\ml\TagsList $tags
 *                the list of available tags
 * @property-read mixed $custom
 *                the custom context
 * @property-read Block $block
 *                a current block (during render)
 * @property-read array $errors
 *                the errors list of the last parsing
 * @property-read object $vars
 *                custom variables of the parsing
 */
class Context
{
    use LazyField;
    use ReadOnly;

    /**
     * The constructor
     *
     * @param \axy\ml\Result $result
     * @param \axy\ml\Options $options
     * @param \axy\ml\TagsList $tags
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
            'vars' => null,
        ];
    }

    /**
     * Initialization before rendering
     *
     * @param array $errors [optional]
     *        the errors list of the tokenize
     */
    public function startRender(array $errors = [])
    {
        $this->magicFields['fields']['vars'] = (object)[];
        $this->magicFields['fields']['errors'] = $errors;
    }

    /**
     * Ends the parsing
     */
    public function endRender()
    {
        $this->magicFields['fields']['vars'] = (object)[];
        $this->magicFields['fields']['errors'] = [];
    }

    /**
     * Sets the current block (while render)
     *
     * @param Block $block
     */
    public function setCurrentBlock(Block $block = null)
    {
        $this->magicFields['fields']['block'] = $block;
    }

    /**
     * Appends an error to the errors list
     *
     * @param \axy\ml\Error $error
     */
    public function addError($error)
    {
        $this->magicFields['fields']['errors'][] = $error;
    }
}
