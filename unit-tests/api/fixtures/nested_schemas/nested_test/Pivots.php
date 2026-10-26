<?php

$pivots = [
    'tags,users' => 'users_tags',
];

$pivot_fks = [
    'users_tags' => [
        'users' => 'user_id',
        'tags' => 'tag_id',
    ],
];

$relationships = [
    'users_tags' => [
        'users' => [['users.id', 'users_tags.user_id']],
        'tags' => [['tags.id', 'users_tags.tag_id']],
    ],
];
