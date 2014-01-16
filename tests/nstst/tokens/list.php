<?php

use axy\ml\helpers\Token;

return [
    'tokens' => [
        [
            'type' => Token::TYPE_HEADER,
            'line' => 1,
            'name' => null,
            'content' => 'Lists test',
            'level' => 1,
        ],
        [
            'type' => Token::TYPE_BLOCK,
            'line' => 3,
            'subs' => [
                [
                    'type' => Token::TYPE_LI,
                    'line' => 3,
                    'level' => 1,
                    'start' => null,
                ],
                [
                    'type' => Token::TYPE_TEXT,
                    'line' => 3,
                    'content' => 'One',
                ],
                [
                    'type' => Token::TYPE_LI,
                    'line' => 4,
                    'level' => 1,
                    'start' => null,
                ],
                [
                    'type' => Token::TYPE_TEXT,
                    'line' => 4,
                    'content' => 'Two',
                ],
                [
                    'type' => Token::TYPE_LI,
                    'line' => 5,
                    'level' => 2,
                    'start' => 1,
                ],
                [
                    'type' => Token::TYPE_TEXT,
                    'line' => 5,
                    'content' => 'Two.One',
                ],
                [
                    'type' => Token::TYPE_LI,
                    'line' => 6,
                    'level' => 2,
                    'start' => null,
                ],
                [
                    'type' => Token::TYPE_TEXT,
                    'line' => 6,
                    'content' => 'Two.Two',
                ],
                [
                    'type' => Token::TYPE_LI,
                    'line' => 7,
                    'level' => 1,
                    'start' => null,
                ],
                [
                    'type' => Token::TYPE_TEXT,
                    'line' => 7,
                    'content' => 'Three ',
                ],
                [
                    'type' => Token::TYPE_TAG,
                    'line' => 7,
                    'name' => 'b',
                    'content' => '',
                ],
                [
                    'type' => Token::TYPE_TEXT,
                    'line' => 7,
                    'content' => "bold\n",
                ],
                [
                    'type' => Token::TYPE_TAG,
                    'line' => 8,
                    'name' => '/',
                    'content' => 'B',
                ],
                [
                    'type' => Token::TYPE_TEXT,
                    'line' => 8,
                    'content' => ' no bold',
                ],
                [
                    'type' => Token::TYPE_LI,
                    'line' => 9,
                    'level' => 1,
                    'start' => null,
                ],
                [
                    'type' => Token::TYPE_TEXT,
                    'line' => 9,
                    'content' => 'Four',
                ],
            ],
        ],
        [
            'type' => Token::TYPE_BLOCK,
            'line' => 11,
            'subs' => [
                [
                    'type' => Token::TYPE_TEXT,
                    'line' => 11,
                    'content' => "*First\n* Second",
                ],
            ],
        ],
    ],
];
