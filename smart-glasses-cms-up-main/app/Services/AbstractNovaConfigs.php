<?php

namespace App\Services;

abstract class AbstractNovaConfigs{
    public int $priority = 10;

    public function __construct()
    {
        $this->priority = $this->setPriority();
    }

    abstract public function setPriority():int ;
    abstract public function pageName():string ;
    abstract public function casts():array ;
    abstract public function fields():array ;
}
