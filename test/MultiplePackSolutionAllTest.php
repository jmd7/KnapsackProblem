<?php

require_once 'autoload.php';

use KnapsackSolutionTestHelper as Helper;
use rg4\knapsack\KnapsackItem as KI;
use rg4\knapsack\KnapsackPack as KP;
use PHPUnit\Framework\TestCase;

class MultiplePackSolutionAllTest extends TestCase {
    private $solutions = [
        "rg4\\knapsack\\MultiplePack_SolutionAll_01",
        "rg4\\knapsack\\MultiplePack_SolutionAll_02",
        "rg4\\knapsack\\MultiplePack_SolutionAll_03",
        "rg4\\knapsack\\MultiplePack_SolutionAll_final",
    ];

    public function test01() {
        $items[] = new KI("栗子", 4, 4500, 5);
        $items[] = new KI("苹果", 5, 5700, 7);
        $items[] = new KI("橘子", 2, 2300, 7);
        $items[] = new KI("草莓", 3, 1200, 5);
        $items[] = new KI("甜瓜", 6, 5600, 2);

        $pack = new KP("背包", 43);

        Helper::getInstance()->performCheckingAllSolutions(__METHOD__, $this->solutions, $items, $pack, 49100);
    }

    public function test02() {
        $items[] = new KI("苹果", 5, 5700, 5);
        $items[] = new KI("橘子", 2, 2270, 5);
        $items[] = new KI("栗子", 4, 4500, 9);
        //$items[] = new KI("草莓", 1, 1100, 9);
        //$items[] = new KI("甜瓜", 6, 5600, 9);

        // $pack = new KP("背包", 14);
        $pack = new KP("背包", 23);
        
        Helper::getInstance()->performCheckingAllSolutions(__METHOD__, $this->solutions, $items, $pack);
    }
}
