<?php

require_once 'autoload.php';

use rg4\knapsack\KnapsackItem as KI;
use rg4\knapsack\KnapsackPack as KP;
use PHPUnit\Framework\TestCase;

class CompletePackSolutionTest extends TestCase {
    private $solutions = [
        "rg4\\knapsack\\CompletePack_Solution_01",
        "rg4\\knapsack\\CompletePack_Solution_02",
        "rg4\\knapsack\\CompletePack_Solution_final",
    ];

    function checkCostAndValue($items, $pack, $res, $fit, $expect_max_value = null) {
        $this->assertTrue(is_array($res));
        $this->assertTrue(array_key_exists(KEY_MAX_VALUE, $res));
        if (!is_null($expect_max_value) and is_numeric($expect_max_value))
            $this->assertEquals($res[KEY_MAX_VALUE], $expect_max_value);
        
        $this->assertTrue(array_key_exists(KEY_MAX_ITEMS, $res));
        $this->assertTrue(is_array($res[KEY_MAX_ITEMS]));
        $this->assertNotEmpty($res[KEY_MAX_ITEMS]);
        $cost = $value = 0;
        foreach ($res[KEY_MAX_ITEMS] as $item) {
            $cost += $item->getCost()*$item->getCount();
            $value += $item->getValue()*$item->getCount();
        }
        $this->assertEquals($res[KEY_MAX_VALUE], $value);
        if ($fit) $this->assertEquals($pack->getVolume(), $cost);
        else $this->assertLessThanOrEqual($pack->getVolume(), $cost);
    }

    function checkResultSets($results) {
        foreach ($results as $res) {
            $this->assertEquals($results[0][KEY_MAX_VALUE], $res[KEY_MAX_VALUE]);
            //$this->assertEquals($results[0][KEY_MAX_ITEMS], $res[KEY_MAX_ITEMS]);
        }
    }

    public function test01() {
        $items[] = new KI("栗子", 6, 2000, INFINITE);
        $items[] = new KI("苹果", 5, 5700, INFINITE);
        $items[] = new KI("橘子", 2, 2270, INFINITE);
        $items[] = new KI("甜瓜", 99, 1000000, INFINITE);

        $pack = new KP("背包", 14);

        $res_no_fit = [];
        $res_do_fit = [];
        foreach ($this->solutions as $class) {
            $res = $class::fillPack($items, $pack ,false);
            $this->checkCostAndValue($items, $pack, $res, false, 15940);
            $res_no_fit[] = $res;

            $res = $class::fillPack($items, $pack ,true);
            $this->checkCostAndValue($items, $pack, $res, true, 15940);
            $res_do_fit[] = $res;
        }
        $this->checkResultSets($res_no_fit);
        $this->checkResultSets($res_do_fit);
    }

    public function test02() {
        $items[] = new KI("苹果", 5, 5700, INFINITE);
        $items[] = new KI("甜瓜", 6, 5600, INFINITE);

        $pack = new KP("背包", 6);
        
        $res_no_fit = [];
        $res_do_fit = [];
        foreach ($this->solutions as $class) {
            $res = $class::fillPack($items, $pack ,false);
            $this->checkCostAndValue($items, $pack, $res, false, 5700);
            $res_no_fit[] = $res;

            $res = $class::fillPack($items, $pack ,true);
            $this->checkCostAndValue($items, $pack, $res, true, 5600);
            $res_do_fit[] = $res;
        }
        $this->checkResultSets($res_no_fit);
        $this->checkResultSets($res_do_fit);
    }

    public function test03() {
        $items[] = new KI("苹果", 5, 5700, 7);
        $items[] = new KI("草莓", 3, 1200, 5);

        $pack = new KP("背包", 29);
        
        $res_no_fit = [];
        $res_do_fit = [];
        foreach ($this->solutions as $class) {
            $res = $class::fillPack($items, $pack ,false);
            $this->checkCostAndValue($items, $pack, $res, false, 29700);
            $res_no_fit[] = $res;

            $res = $class::fillPack($items, $pack ,true);
            $this->checkCostAndValue($items, $pack, $res, true, 26400);
            $res_do_fit[] = $res;
        }
        $this->checkResultSets($res_no_fit);
        $this->checkResultSets($res_do_fit);
    }
}
