<?php

namespace rg4\knapsack;

class KnapsackPack {
    private $name = "Knapsack Pack";
    private $volume;

    public function __construct($name, $volume) {
        if ($volume <= 0) {
            throw new \Exception("Knapsack pack initialization failed. \n".
                "[volume = $volume] \n");
        }
        $this->volume = $volume;

        if (empty($name)) {
            $this->name = "Knapsack Pack";
        } else $this->name = $name;
    }

    public function getVolume() {
        return $this->volume;
    }

    public function getName() {
        return $this->name;
    }

    public function setVolume($volume) {
        if ($volume > 0) {
            $this->volume = $volume;
            return $this;
        } else return false; 
    }

    public function setName($name) {
        if (empty($name)) {
            $this->name = "Knapsack Pack";
        } else $this->name = $name;
        return $this;
    }

    public function __toString() {
        return $this->name." [V:$this->volume]";
    }

}

?>