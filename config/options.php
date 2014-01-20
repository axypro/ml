<?php
/**
 * Default options for axyml-parser
 *
 * @package axy\ml
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */

return [
    /* New-line symbol(s) in result */
    'nl' => "\n",

    /* Count of spaces for tab */
    'tab' => 4,

    /* Flag for escape html special chars */
    'escape' => true,

    /* The handler for text (callable) */
    'textHandler' => null,

    /* Number of top level header (<h1> by default) */
    'hStart' => 1,

    /* Wrapper for header (callable) */
    'hHandler' => null,

    /* Opening and closing html-tags for text blocks */
    'bTags' => ['<p>', '</p>'],

    /* Wrapper for block (callable) */
    'bHandler' => null,

    /* Beautiful output */
    'beauty' => true,
];
