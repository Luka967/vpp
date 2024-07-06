<?php

namespace Skop\Core;

trait Serializable
{
    public function as_array(): array
    {
        $ret = [];
        foreach ($this as $key => $value)
            $ret[$key] = $value;
        return $ret;
    }
}
