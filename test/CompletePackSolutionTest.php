<?php

require_once 'autoload.php';

use KnapsackSolutionTestHelper as Helper;
use rg4\knapsack\KnapsackItem as KI;
use rg4\knapsack\KnapsackPack as KP;
use PHPUnit\Framework\TestCase;

class CompletePackSolutionTest extends TestCase {
    private $solutions = [
        "rg4\\knapsack\\CompletePack_Solution_01",
        "rg4\\knapsack\\CompletePack_Solution_02",
        "rg4\\knapsack\\CompletePack_Solution_final",
    ];

    public function test01() {
        $items[] = new KI("栗子", 6, 2000, INFINITE);
        $items[] = new KI("苹果", 5, 5700, INFINITE);
        $items[] = new KI("橘子", 2, 2270, INFINITE);
        $items[] = new KI("甜瓜", 99, 1000000, INFINITE);

        $pack = new KP("背包", 14);

        Helper::getInstance()->performChecking(__METHOD__, $this->solutions, $items, $pack, 15940);
    }

    public function test02() {
        $items[] = new KI("苹果", 5, 5700, INFINITE);
        $items[] = new KI("甜瓜", 6, 5600, INFINITE);

        $pack = new KP("背包", 6);
        
        Helper::getInstance()->performChecking(__METHOD__, $this->solutions, $items, $pack);
    }

    public function test03() {
        $items[] = new KI("@1", 5, 5700, INFINITE);
        $items[] = new KI("@2", 3, 1200, INFINITE);

        $pack = new KP("背包", 29);
        
        Helper::getInstance()->performChecking(__METHOD__, $this->solutions, $items, $pack);
    }

    public function test04() {
        $items[] = new KI("#1", 13, 135, INFINITE);
        $items[] = new KI("#2", 7, 76, INFINITE);

        $pack = new KP("背包", 107);
        
        Helper::getInstance()->performChecking(__METHOD__, $this->solutions, $items, $pack);
    }
}
