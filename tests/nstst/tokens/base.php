<?php

use axy\ml\helpers\Token;

return [
    'tokens' => [
        [
            'type' => Token::TYPE_HEADER,
            'line' => 2,
            'name' => null,
            'content' => 'This is header',
            'level' => 1,
        ],
        [
            'type' => Token::TYPE_HEADER,
            'line' => 9,
            'name' => 'h2',
            'content' => 'This is subheader',
            'level' => 2,
        ],
        [
            'type' => Token::TYPE_BLOCK,
            'line' => 13,
            'subs' => [
                [
                    'type' => Token::TYPE_TEXT,
                    'line' => 13,
                    'content' => "First line.\nSecond line.\nThird line.",
                ],
            ],
        ],
        [
            'type' => Token::TYPE_BLOCK,
            'line' => 17,
            'subs' => [
                [
                    'type' => Token::TYPE_TEXT,
                    'line' => 17,
                    'content' => "First Second.\n  Second ",
                ],
                [
                    'type' => Token::TYPE_TAG,
                    'line' => 18,
                    'name' => 'b',
                    'content' => '',
                ],
                [
                    'type' => Token::TYPE_TEXT,
                    'line' => 18,
                    'content' => 'bold',
                ],
                [
                    'type' => Token::TYPE_TAG,
                    'line' => 18,
                    'name' => '/',
                    'content' => 'B',
                ],
                [
                    'type' => Token::TYPE_TEXT,
                    'line' => 18,
                    'content' => ' Second.',
                ],
            ],
        ],
        [
            'type' => Token::TYPE_ANCHOR,
            'name' => 'cut',
            'line' => 20,
        ],
        [
            'type' => Token::TYPE_BLOCK,
            'line' => 22,
            'subs' => [
                [
                    'type' => Token::TYPE_TAG,
                    'line' => 22,
                    'name' => 'code',
                    'content' => ":php\n  x = 1;\n  y = A[x];",
                ],
            ],
        ],
        [
            'type' => Token::TYPE_BLOCK,
            'line' => 27,
            'subs' => [
                [
                    'type' => Token::TYPE_TEXT,
                    'line' => 27,
                    'content' => 'Text with ',
                ],
                [
                    'type' => Token::TYPE_TAG,
                    'line' => 27,
                    'name' => null,
                    'content' => '<html>',
                ],
                [
                    'type' => Token::TYPE_TEXT,
                    'line' => 27,
                    'content' => ' HTML!',
                ],
            ],
        ],
        [
            'type' => Token::TYPE_BLOCK,
            'line' => 29,
            'subs' => [
                [
                    'type' => Token::TYPE_TEXT,
                    'line' => 29,
                    'content' => 'Text with ',
                ],
                [
                    'type' => Token::TYPE_TAG,
                    'line' => 29,
                    'name' => null,
                    'content' => "<\nwrap\n\n>",
                ],
                [
                    'type' => Token::TYPE_TEXT,
                    'line' => 32,
                    'content' => '!',
                ],
            ],
        ],
        [
            'type' => Token::TYPE_BLOCK,
            'line' => 34,
            'subs' => [
                [
                    'type' => Token::TYPE_TEXT,
                    'line' => 34,
                    'content' => 'Closed tag: ',
                ],
                [
                    'type' => Token::TYPE_TAG,
                    'line' => 34,
                    'name' => 'tag',
                    'content' => '',
                ],
            ],
        ],
    ],
    'meta' => [
        'tags' => 'one, two, three',
        'url' => 'http://url',
        'cache' => true,
    ],
    'errors' => [

    ],
];
