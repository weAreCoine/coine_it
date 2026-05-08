<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Email blocklist
    |--------------------------------------------------------------------------
    |
    | Words that, if found as an exact segment of either the email's host
    | (split by ".") or its local part (split by "." "_" "+" "-"), mark the
    | address as obviously fake. Match is case-insensitive and exact on the
    | segment, so "test" blocks "test@test.it", "test@test.ai" and
    | "mario.test@gmail.com" but does NOT block "latest@gmail.com" or
    | "besttest@gmail.com".
    |
    */

    'blocklist' => [
        'test',
        'prova',
        'fake',
        'asd',
        'asdf',
        'asdfasdf',
        'qwerty',
        'foo',
        'bar',
        'foobar',
        'example',
        'domain',
        'xxx',
        'aaa',
        'aaaa',
    ],

];
