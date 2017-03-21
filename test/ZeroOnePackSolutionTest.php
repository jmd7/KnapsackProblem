<?php

require_once 'autoload.php';

use rg4\knapsack\KnapsackItem as KI;
use rg4\knapsack\KnapsackPack as KP;
use PHPUnit\Framework\TestCase;

class ZeroOnePackSolutionTest extends TestCase {
    private $solutions = [
        "rg4\\knapsack\\ZeroOnePack_Solution_01",
        "rg4\\knapsack\\ZeroOnePack_Solution_02",
        "rg4\\knapsack\\ZeroOnePack_Solution_final",
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
            $cost += $item->getCost();
            $value += $item->getValue();
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
        $items[] = new KI("栗子", 4, 4500, 1);
        $items[] = new KI("苹果", 5, 5700, 1);
        $items[] = new KI("橘子", 2, 2250, 1);
        $items[] = new KI("草莓", 1, 1100, 1);
        $items[] = new KI("甜瓜", 6, 6700, 1);

        $pack = new KP("背包", 13);

        $res_no_fit = [];
        $res_do_fit = [];
        foreach ($this->solutions as $class) {
            $res = $class::fillPack($items, $pack ,false);
            $this->checkCostAndValue($items, $pack, $res, false, 14650);
            $res_no_fit[] = $res;

            $res = $class::fillPack($items, $pack ,true);
            $this->checkCostAndValue($items, $pack, $res, true, 14650);
            $res_do_fit[] = $res;
        }
        $this->checkResultSets($res_no_fit);
        $this->checkResultSets($res_do_fit);
    }

    public function test02() {
        $items[] = new KI("栗子1", 4, 4500, 1);
        $items[] = new KI("栗子2", 4, 4500, 1);
        $items[] = new KI("栗子3", 4, 4500, 1);
        $items[] = new KI("栗子4", 4, 4500, 1);
        $items[] = new KI("栗子5", 4, 4500, 1);

        $items[] = new KI("苹果1", 5, 5700, 1);
        $items[] = new KI("苹果2", 5, 5700, 1);
        $items[] = new KI("苹果3", 5, 5700, 1);
        $items[] = new KI("苹果4", 5, 5700, 1);
        $items[] = new KI("苹果5", 5, 5700, 1);
        $items[] = new KI("苹果6", 5, 5700, 1);
        $items[] = new KI("苹果7", 5, 5700, 1);

        $items[] = new KI("橘子1", 2, 2300, 1);
        $items[] = new KI("橘子2", 2, 2300, 1);
        $items[] = new KI("橘子3", 2, 2300, 1);
        $items[] = new KI("橘子4", 2, 2300, 1);
        $items[] = new KI("橘子5", 2, 2300, 1);
        $items[] = new KI("橘子6", 2, 2300, 1);
        $items[] = new KI("橘子7", 2, 2300, 1);

        $items[] = new KI("草莓1", 3, 3400, 1);
        $items[] = new KI("草莓2", 3, 3400, 1);
        $items[] = new KI("草莓3", 3, 3400, 1);
        $items[] = new KI("草莓4", 3, 3400, 1);
        $items[] = new KI("草莓5", 3, 3400, 1);

        $items[] = new KI("甜瓜1", 6, 5600, 1);
        $items[] = new KI("甜瓜2", 6, 5600, 1);
        $items[] = new KI("甜瓜3", 6, 5600, 1);
        $items[] = new KI("甜瓜4", 6, 5600, 1);
        $items[] = new KI("甜瓜5", 6, 5600, 1);
        $items[] = new KI("甜瓜6", 6, 5600, 1);

        $pack = new KP("背包", 43);
        
        $res_no_fit = [];
        $res_do_fit = [];
        foreach ($this->solutions as $class) {
            $res = $class::fillPack($items, $pack ,false);
            $this->checkCostAndValue($items, $pack, $res, false, 49100);
            $res_no_fit[] = $res;

            $res = $class::fillPack($items, $pack ,true);
            $this->checkCostAndValue($items, $pack, $res, true, 49100);
            $res_do_fit[] = $res;
        }
        $this->checkResultSets($res_no_fit);
        $this->checkResultSets($res_do_fit);
    }
}
