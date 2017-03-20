<?php

use PHPUnit\Framework\TestCase;

class KnapsackItemTest extends TestCase {
    public function testSingleItemConstruct() {
        $name = "SingleItem";
        $cost = 3;
        $value = 7;
        $count = 1;

        $item = new rg4\knapsack\KnapsackItem($name, $cost, $value);
        $this->assertEquals($item->getName(),  $name);
        $this->assertEquals($item->getCost(),  $cost);
        $this->assertEquals($item->getValue(), $value);
        $this->assertEquals($item->getCount(), $count);
    }

    public function testInfiniteItemConstruct() {
        $name = "InfiniteItem";
        $cost = 1;
        $value = 10000;
        $count = INFINITE;

        $item = new rg4\knapsack\KnapsackItem($name, $cost, $value, $count);
        $this->assertEquals($item->getName(),  $name);
        $this->assertEquals($item->getCost(),  $cost);
        $this->assertEquals($item->getValue(), $value);
        $this->assertEquals($item->getCount(), $count);
    }

    public function testSetAttributes() {
        $name = "SetAttrItem";
        $name_default = "Knapsack Item";
        $cost = 9;
        $value = 11;
        $count = 13;

        $item = new rg4\knapsack\KnapsackItem("", 1, 1, 1);
        $this->assertAttributeEquals($name, 'name', $item->setName($name));
        $this->assertAttributeEquals($cost, 'cost', $item->setCost($cost));
        $this->assertAttributeEquals($value, 'value', $item->setValue($value));
        $this->assertAttributeEquals($count, 'count', $item->setCount($count));

        $this->assertAttributeEquals($name_default, 'name', $item->setName(""));
        $this->assertFalse($item->setCost(-1));
        $this->assertFalse($item->setValue(-1));
        $this->assertFalse($item->setCount(-1));
    }

    public function testDefaultValue() {
        $name_default = "Knapsack Item";

        $this->expectException(Exception::class);
        $item = new rg4\knapsack\KnapsackItem("", -1, 1, 1);
        $item = new rg4\knapsack\KnapsackItem("", 1, -1, 1);
        $item = new rg4\knapsack\KnapsackItem("", 1, 1, -1);
        $this->assertAttributeEquals(
            $name_default, 'name', new rg4\knapsack\KnapsackItem("", 1, 1, 1));
    }

    public function testToString() {
        $name = "InfiniteItem";
        $cost = 3;
        $value = 7;
        $count = INFINITE;

        $item = new rg4\knapsack\KnapsackItem($name, $cost, $value, $count);
        $this->assertEquals($name." [c:".$cost."][v:".$value."][n:".$count."]", sprintf("%s", $item));      
        $name_default = "Knapsack Item";
        $cost = 1;
        $value = 1;
        $count = 1;
        $item = new rg4\knapsack\KnapsackItem("", $cost, $value);
        $this->assertEquals($name_default." [c:".$cost."][v:".$value."][n:".$count."]", sprintf("%s", $item)); 

    }
}

?>