<?php

require_once 'autoload.php';

use KnapsackSolutionTestHelper as Helper;
use rg4\knapsack\KnapsackItem as KI;
use rg4\knapsack\KnapsackPack as KP;
use PHPUnit\Framework\TestCase;

class MixedPackSolutionTest extends TestCase {
    private $solutions = [
        "rg4\\knapsack\\MixedPack_Solution_final",
    ];

    public function test01() {
        $items[] = new KI("栗子", 4, 4500, 5);
        $items[] = new KI("苹果", 5, 5700, 1);
        $items[] = new KI("橘子", 2, 2300, 7);
        $items[] = new KI("草莓", 1, 900, INFINITE);
        $items[] = new KI("甜瓜", 6, 5600, 1);

        $pack = new KP("背包", 51);

        Helper::getInstance()->performChecking(__METHOD__, $this->solutions, $items, $pack);
    }

}
