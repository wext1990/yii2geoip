<?php

namespace wext\yii2geoip\models;

class AbstractModel implements ModelInterface
{
    public function __construct(array $props = [])
    {
        $this->assign($props);
    }
    
    protected function assign(array $props)
    {
        foreach ($props as $name => $val) {
            $this->{$name} = $val;
        }
    }
    
    public function toArray(): array
    {
        return get_object_vars($this);
    }
}
