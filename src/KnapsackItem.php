<?php

namespace rg4\knapsack;

class KnapsackItem {
    private $name = "Knapsack Item";
    private $cost;
    private $value;
    private $count = 1;

    public function __construct($name, $cost, $value, $count = 1) {
        if ($cost <= 0 || $value <= 0 || $count <= 0) {
            throw new \Exception("Knapsack item initialization failed. \n".
                "[cost = $cost] [value = $value] [count = $count] \n");
        }
        $this->cost = $cost;
        $this->value = $value;
        $this->count = $count;

        if (empty($name)) {
            $this->name = "Knapsack Item";
        } else $this->name = $name;
    }

    public function getCost() {
        return $this->cost;
    }

    public function getValue() {
        return $this->value;
    }

    public function getCount() {
        return $this->count;
    }

    public function getName() {
        return $this->name;
    }

    public function __toString() {
        return $this->name." [c:$this->cost][v:$this->value][n:$this->count]";
    }

    public function setCost($cost) {
        if ($cost > 0) {
            $this->cost = $cost;
            return $this;
        } else return false; 
    }

    public function setValue($value) {
        if ($value > 0) {
            $this->value = $value;
            return $this;
        } else return false; 
    }

    public function setCount($count) {
        if ($count > 0) {
            $this->count = $count;
            return $this;
        } else return false; 
    }

    public function setName($name) {
        if (isEmpty($name)) {
            $this->name = "Knapsack Item";
        } else $this->name = $name;
    }
}

?>