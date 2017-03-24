<?php

require_once 'autoload.php';

use rg4\knapsack\AbstractKnapsackSolution as aKS;
use rg4\knapsack\KnapsackItem as KI;
use rg4\knapsack\KnapsackPack as KP;
use PHPUnit\Framework\TestCase;

class KnapsackSolutionTestHelper extends TestCase {
    protected $key_max_value =  "Value of best solution";
    protected $key_max_items = "Items of best solution";
    protected $key_loop_count = "Loop count";

    public function __construct() {
        //
    }

    public function checkCostAndValue($items, $pack, $res, $fit, $expect_max_value = null) {
        $this->assertTrue(is_array($res));
        $this->assertTrue(array_key_exists($this->key_max_value, $res));
        if (!is_null($expect_max_value) and is_numeric($expect_max_value))
            $this->assertEquals($res[$this->key_max_value], $expect_max_value);
        
        $this->assertTrue(array_key_exists($this->key_max_items, $res));
        $this->assertTrue(is_array($res[$this->key_max_items]));
        // $this->assertNotEmpty($res[$this->key_max_items]);
        $cost = $value = 0;
        foreach ($res[$this->key_max_items] as $item) {
            $cost += $item->getCost() * $item->getCount();
            $value += $item->getValue() * $item->getCount();
        }
        $this->assertEquals($res[$this->key_max_value], $value);
        if ($fit && $cost > 0) $this->assertEquals($pack->getVolume(), $cost);
        else $this->assertLessThanOrEqual($pack->getVolume(), $cost);
    }

    public function checkResultSets($results) {
        foreach ($results as $res) {
            $this->assertEquals($results[0][$this->key_max_value], $res[$this->key_max_value]);
            //$this->assertEquals($results[0][KEY_MAX_ITEMS], $res[KEY_MAX_ITEMS]);
        }
    }

    public function outputResult($items, $pack, $class, $method, $res, $time) {
        $best_items = "";
        $best_cost = 0;
        foreach ($res[$this->key_max_items] as $item) {
            $best_items .= "  [" . $item . "]" . PHP_EOL;
            $best_cost += $item->getCost()*$item->getCount();
        }

        $out = sprintf("[%s::%s] [Pack=%d] [Best Cost=%d] [Best Value=%d]", 
            substr($class, strrpos($class, "\\")+1),
            substr($method, strrpos($method, ":")+1), 
            $pack->getVolume(),
            $best_cost,
            $res[$this->key_max_value]
        );
        echo PHP_EOL . $out;
        $out = sprintf(" [Time=%.6f] [Loop=%s]", 
            $time,
            $res[$this->key_loop_count]
        );
        echo $out . PHP_EOL;
        echo $best_items . PHP_EOL;
    }

    public function performChecking($method, $solutions, $items, $pack, $expect_max_value = null) {
        $res_no_fit = [];
        $res_do_fit = [];
        foreach ($solutions as $class) {
            $time_start = microtime(true);
            $res = $class::fillPack($items, $pack ,false);
            $this->outputResult($items, $pack, $class, $method, $res, microtime(true) - $time_start);
            $this->checkCostAndValue($items, $pack, $res, false, $expect_max_value);
            $res_no_fit[] = $res;

            $time_start = microtime(true);
            $res = $class::fillPack($items, $pack ,true);
            $this->outputResult($items, $pack, $class, $method, $res, microtime(true) - $time_start);
            $this->checkCostAndValue($items, $pack, $res, true, $expect_max_value);
            $res_do_fit[] = $res;
        }
        $this->checkResultSets($res_no_fit);
        $this->checkResultSets($res_do_fit);
    }

    public static function getInstance() {
        return new KnapsackSolutionTestHelper();
    }
}
