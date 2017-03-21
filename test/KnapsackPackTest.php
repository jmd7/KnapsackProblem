<?php

require_once 'autoload.php';

use PHPUnit\Framework\TestCase;

class KnapsackPackTest extends TestCase {
    public function testPackConstruct() {
        $name = "Pack";
        $volume = 7;

        $pack = new rg4\knapsack\KnapsackPack($name, $volume);
        $this->assertEquals($pack->getName(),  $name);
        $this->assertEquals($pack->getVolume(),  $volume);
    }

    public function testSetAttributes() {
        $name = "SetAttrPack";
        $name_default = "Knapsack Pack";
        $volume = 7;

        $pack = new rg4\knapsack\KnapsackPack("", 11);
        $this->assertAttributeEquals($name, 'name', $pack->setName($name));
        $this->assertAttributeEquals($volume, 'volume', $pack->setVolume($volume));

        $this->assertAttributeEquals($name_default, 'name', $pack->setName(""));
        $this->assertFalse($pack->setVolume(-1));
    }

    public function testDefaultValue() {
        $name_default = "Knapsack Pack";

        $this->expectException(Exception::class);
        $pack = new rg4\knapsack\KnapsackPack("", -1);
        $this->assertAttributeEquals(
            $name_default, 'name', new rg4\knapsack\KnapsackPack("", 1));
    }

    public function testToString() {
        $name = "Pack";
        $volume = 7;

        $pack = new rg4\knapsack\KnapsackPack($name, $volume);
        $this->assertEquals($name." [V:".$volume."]", sprintf("%s", $pack));

        $name_default = "Knapsack Pack";
        $pack = new rg4\knapsack\KnapsackPack("", $volume);
        $this->assertEquals($name_default." [V:".$volume."]", sprintf("%s", $pack)); 

    }
}

?>