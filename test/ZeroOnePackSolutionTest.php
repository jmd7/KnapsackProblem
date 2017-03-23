<?php

require_once 'autoload.php';

use KnapsackSolutionTestHelper as Helper;
use rg4\knapsack\KnapsackItem as KI;
use rg4\knapsack\KnapsackPack as KP;
use PHPUnit\Framework\TestCase;

class ZeroOnePackSolutionTest extends TestCase {
    private $solutions = [
        "rg4\\knapsack\\ZeroOnePack_Solution_01",
        "rg4\\knapsack\\ZeroOnePack_Solution_02",
        "rg4\\knapsack\\ZeroOnePack_Solution_final",
    ];

    public function test01() {
        $items[] = new KI("栗子", 4, 4500, 1);
        $items[] = new KI("苹果", 5, 5700, 1);
        $items[] = new KI("橘子", 2, 2250, 1);
        $items[] = new KI("草莓", 1, 1100, 1);
        $items[] = new KI("甜瓜", 6, 6700, 1);

        $pack = new KP("背包", 13);

        Helper::getInstance()->performChecking($this->solutions, $items, $pack, 14650);
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
        
        Helper::getInstance()->performChecking($this->solutions, $items, $pack, 49100);
    }

    public function test03() {
        $items[] = new KI("#1", 3, 4, 1);
        $items[] = new KI("#2", 4, 6, 1);
        $items[] = new KI("#3", 5, 7, 1);

        $pack = new KP("背包", 10);

        Helper::getInstance()->performChecking($this->solutions, $items, $pack);
    }

}
