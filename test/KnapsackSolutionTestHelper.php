<?php

require_once 'autoload.php';

use rg4\knapsack\AbstractKnapsackSolution as aKS;
use rg4\knapsack\KnapsackItem as KI;
use rg4\knapsack\KnapsackPack as KP;
use PHPUnit\Framework\TestCase;
use KnapsackSolutionTestHelper as HELP;

class KnapsackSolutionTestHelper extends TestCase {
    protected const KEY_MAX_VALUE =  "Value of best solution";
    protected const KEY_MAX_ITEMS = "Items of best solution";
    protected const KEY_ACTUAL_COST = "Cost of best solution";
    protected const KEY_COUNT_SOLUTIONS = "Count of best solution";
    protected const KEY_BEST_SOLUTIONS = "Best Solutions";
    protected const KEY_LOOP_COUNT = "Loop count";

    public function __construct() {
        //
    }

    public function checkCostAndValue($items, $pack, $res, $fit, $expect_max_value = null) {
        $this->assertTrue(is_array($res));
        $this->assertTrue(array_key_exists(HELP::KEY_MAX_VALUE, $res));
        if (!is_null($expect_max_value) and is_numeric($expect_max_value))
            $this->assertEquals($res[HELP::KEY_MAX_VALUE], $expect_max_value);
        
        $this->assertTrue(array_key_exists(HELP::KEY_MAX_ITEMS, $res));
        $this->assertTrue(is_array($res[HELP::KEY_MAX_ITEMS]));
        // $this->assertNotEmpty($res[HELP::KEY_MAX_ITEMS]);
        $cost = $value = 0;
        foreach ($res[HELP::KEY_MAX_ITEMS] as $item) {
            $cost += $item->getCost() * $item->getCount();
            $value += $item->getValue() * $item->getCount();
        }
        $this->assertEquals($res[HELP::KEY_MAX_VALUE], $value);
        if ($fit && $cost > 0) $this->assertEquals($pack->getVolume(), $cost);
        else $this->assertLessThanOrEqual($pack->getVolume(), $cost);
    }

    public function checkResultSets($results) {
        foreach ($results as $res) {
            $this->assertEquals($results[0][HELP::KEY_MAX_VALUE], $res[HELP::KEY_MAX_VALUE]);
            //$this->assertEquals($results[0][KEY_MAX_ITEMS], $res[KEY_MAX_ITEMS]);
        }
    }

    public function checkAllSolutions($items, $pack, $res, $fit, $expect_max_value = null) {
        $this->assertLessThanOrEqual($pack->getVolume(), $res[HELP::KEY_ACTUAL_COST]);
        if (!empty($res[HELP::KEY_MAX_ITEMS])) {
            $this->assertGreaterThanOrEqual(1, $res[HELP::KEY_COUNT_SOLUTIONS]);
            $this->assertTrue(is_array($res[HELP::KEY_BEST_SOLUTIONS]));
            $this->assertGreaterThanOrEqual(1, count($res[HELP::KEY_BEST_SOLUTIONS]));
            $this->assertEquals($res[HELP::KEY_COUNT_SOLUTIONS], count($res[HELP::KEY_BEST_SOLUTIONS]));

            foreach ($res[HELP::KEY_BEST_SOLUTIONS] as $solution) {
                $s_cost = 0;
                $s_value = 0;

                $this->assertTrue(is_array($solution));
                $this->assertGreaterThanOrEqual(1, count($solution));
                foreach ($solution as $item) {
                    $s_cost += $item->getCost() * $item->getCount();
                    $s_value += $item->getValue() * $item->getCount();
                }
                $this->assertEquals($res[HELP::KEY_MAX_VALUE], $s_value);
                $this->assertEquals($res[HELP::KEY_ACTUAL_COST], $s_cost);
            }
        } else {
            $this->assertEquals(0, $res[HELP::KEY_COUNT_SOLUTIONS]);
            $this->assertFalse(array_key_exists(HELP::KEY_BEST_SOLUTIONS, $res));
        }
    }

    public function outputResult($items, $pack, $class, $method, $res, $time) {
        $best_items = "";
        $best_cost = 0;
        foreach ($res[KEY_MAX_ITEMS] as $item) {
            $best_items .= "  [" . $item . "]" . PHP_EOL;
            $best_cost += $item->getCost()*$item->getCount();
        }

        $out = sprintf("[%s::%s] [Pack=%d] [Best Cost=%d] [Best Value=%d]", 
            substr($class, strrpos($class, "\\")+1),
            substr($method, strrpos($method, ":")+1), 
            $pack->getVolume(),
            $best_cost,
            $res[HELP::KEY_MAX_VALUE]
        );
        echo PHP_EOL . $out;
        $out = sprintf(" [Time=%.6f] [Loop=%s]", 
            $time,
            $res[HELP::KEY_LOOP_COUNT]
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

    public function performCheckingAllSolutions($method, $solutions, $items, $pack, $expect_max_value = null) {
        $res_no_fit = [];
        $res_do_fit = [];
        foreach ($solutions as $class) {
            $time_start = microtime(true);
            $res = $class::fillPack($items, $pack ,false);
            $this->outputResult($items, $pack, $class, $method, $res, microtime(true) - $time_start);
            $this->checkCostAndValue($items, $pack, $res, false, $expect_max_value);
            $this->checkAllSolutions($items, $pack, $res, false, $expect_max_value);
            $res_no_fit[] = $res;

            $time_start = microtime(true);
            $res = $class::fillPack($items, $pack ,true);
            $this->outputResult($items, $pack, $class, $method, $res, microtime(true) - $time_start);
            $this->checkCostAndValue($items, $pack, $res, true, $expect_max_value);
            $this->checkAllSolutions($items, $pack, $res, false, $expect_max_value);
            $res_do_fit[] = $res;
        }
        $this->checkResultSets($res_no_fit);
        $this->checkResultSets($res_do_fit);
    }

    public static function getInstance() {
        return new KnapsackSolutionTestHelper();
    }
}
