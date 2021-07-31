<?php

namespace App\Parsers;

interface ParserInterface
{
    public function parse(array $data): bool;
}
