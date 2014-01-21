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
                    'type' => Token::TYPE_TAG,
                    'line' => 3,
                    'name' => '*',
                    'content' => '*',
                ],
                [
                    'type' => Token::TYPE_TEXT,
                    'line' => 3,
                    'content' => " One\n",
                ],
                [
                    'type' => Token::TYPE_TAG,
                    'line' => 4,
                    'name' => '*',
                    'content' => '*',
                ],
                [
                    'type' => Token::TYPE_TEXT,
                    'line' => 4,
                    'content' => " Two\n",
                ],
                [
                    'type' => Token::TYPE_TAG,
                    'line' => 5,
                    'name' => '*',
                    'content' => '**:1',
                ],
                [
                    'type' => Token::TYPE_TEXT,
                    'line' => 5,
                    'content' => " Two.One\n",
                ],
                [
                    'type' => Token::TYPE_TAG,
                    'line' => 6,
                    'name' => '*',
                    'content' => '**',
                ],
                [
                    'type' => Token::TYPE_TEXT,
                    'line' => 6,
                    'content' => " Two.Two\n",
                ],
                [
                    'type' => Token::TYPE_TAG,
                    'line' => 7,
                    'name' => '*',
                    'content' => '*',
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
                    'content' => " no bold\n",
                ],
                [
                    'type' => Token::TYPE_TAG,
                    'line' => 9,
                    'name' => '*',
                    'content' => '*',
                ],
                [
                    'type' => Token::TYPE_TEXT,
                    'line' => 9,
                    'content' => ' Four',
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
                    'content' => "First\n",
                ],
                [
                    'type' => Token::TYPE_TAG,
                    'line' => 12,
                    'name' => '*',
                    'content' => '*',
                ],
                [
                    'type' => Token::TYPE_TEXT,
                    'line' => 12,
                    'content' => ' Second',
                ],
            ],
        ],
    ],
];
