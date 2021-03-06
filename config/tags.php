<?php
/**
 * The list of standard tags
 *
 * @package axy\ml
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */

return [
    'b' => 'HtmlTag',
    'i' => 'HtmlTag',
    'u' => 'HtmlTag',
    'br' => ['HtmlTag', ['single' => true]],
    'ul' => 'HtmlTag',
    'ol' => 'HtmlTag',
    'li' => 'HtmlTag',
    'table' => 'HtmlTag',
    'tr' => 'HtmlTag',
    'td' => 'HtmlTag',
    '/' => 'ClosingTag',
    'http' => 'Scheme',
    'https' => 'Scheme',
    'tag' => 'Tag',
    'url' => 'Url',
    'img' => 'Img',
    'code' => 'Code',
    'html' => 'Html',
    'opt' => 'Opt',
    '*' => 'Li',
    '' => 'Html',
];
