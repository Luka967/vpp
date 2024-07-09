<?php

namespace Skop\Core;

class DomainObjectBinding
{
    public readonly string $query;
    public readonly array $values;

    public function __construct(string $query, array $values)
    {
        $this->query = $query;
        $this->values = $values;
    }
}
