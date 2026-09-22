<?php

namespace App;

use App\Services\RandomStrings;

class Sample{
    use RandomStrings;

    public function make(){
        return  sprintf("%s %s %s %s",$this->getActionRand(), $this->getColorRand(), $this->getStrRand(),$this->getNameRand());
    }
}
