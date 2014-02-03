<?php
/**
 * Default options for axyml-parser
 *
 * @package axy\ml
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */

return [
    /* The tag brackets */
    'brackets' => ['[', ']'],

    /* The new-line symbol(s) in result */
    'nl' => "\n",

    /* The count of spaces for a tab */
    'tab' => 4,

    /* The flag that escape html special chars */
    'escape' => true,

    /* The handler for text (callable) */
    'textHandler' => null,

    /* Number of a top level header (<h1> by default) */
    'hStart' => 1,

    /* The wrapper for header (callable) */
    'hHandler' => null,

    /* The prefix for a header link */
    'hLinkPrefix' => null,

    /* The flag that a header link is necessary */
    'hLinkNeed' => false,

    /* The opening and closing html-tags for text blocks */
    'bTags' => ['<p>', '</p>'],

    /* The wrapper for block (callable) */
    'bHandler' => null,

    /* The beautiful output flag */
    'beauty' => true,
];
