<?php

namespace Skop\Core;

trait DomainObject
{
    public function asArray(): array
    {
        $ret = [];
        foreach ($this as $key => $value)
            $ret[$key] = $value;
        return $ret;
    }
}
