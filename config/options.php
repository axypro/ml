<?php
/**
 * Default options for axyml-parser
 *
 * @package axy\ml
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */

return [

    /* newline symbol(s) in result */
    'nl' => "\n",

    /* count of spaces for tab */
    'tab' => 4,

    /* escape html special chars */
    'escape' => true,

    /* handler for text (callback) */
    'textHandler' => null,

    /* number of top level <h> (<h1> by default) */
    'hStart' => 1,

    /* wrapper for header (callback) */
    'hHandler' => null,

    /* tags for block */
    'bTags' => ['<p>', '</p>'],

    /* wrapper for block (callback) */
    'bHandler' => null,

    /* separator of blocks */
    'blocks_separator' => "\n \n",
];
