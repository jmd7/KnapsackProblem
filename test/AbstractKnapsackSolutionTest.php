<?php

require_once 'autoload.php';

use rg4\knapsack\AbstractKnapsackSolution as aKS;
use rg4\knapsack\KnapsackItem as KI;
use PHPUnit\Framework\TestCase;

class AbstractKnapsackSolutionTest extends TestCase {
    public function testUnableConstruct() {
        $this->expectException(Error::class);
        $s = new aKS();
    }

    public function testKPMax() {
        $a = 100;
        $b = 101;

        $this->assertNull(aKS::kp_max(null, null));
        $this->assertEquals($a, aKS::kp_max(null, $a));
        $this->assertEquals($a, aKS::kp_max($a, null));
        $this->assertEquals($a, aKS::kp_max($a, $a));
        $this->assertEquals($b, aKS::kp_max($a, $b));
    }

    public function testKPMaxTracing() {
        $a = 100;
        $b = 101;
        $ai = "left";
        $bi = "right";
        $which = null;

        $this->assertNull(aKS::kp_max_tracing(null, null, $which, $ai, $bi));
        $this->assertNull($which);

        $this->assertEquals($a, aKS::kp_max_tracing($a, null, $which, $ai, $bi));
        $this->assertEquals($ai, $which);

        $this->assertEquals($b, aKS::kp_max_tracing(null, $b, $which, $ai, $bi));
        $this->assertEquals($bi, $which);

        $this->assertEquals($a, aKS::kp_max_tracing($a, $a, $which, $ai, $bi));
        $this->assertEquals($bi, $which);
        
        $this->assertEquals($b, aKS::kp_max_tracing($a, $b, $which, $ai, $bi));
        $this->assertEquals($bi, $which);

        $this->assertEquals($b, aKS::kp_max_tracing($b, $a, $which, $ai, $bi));
        $this->assertEquals($ai, $which);
    }

    public function testSortItem() {
        $items = $items_org = "string";
        $name_default = "Knapsack Item";
        
        aKS::sortItems($items);
        $this->assertEquals($items, $items_org);

        $items = $items_org = array();
        aKS::sortItems($items);
        $this->assertEquals($items, $items_org);

        $items = $items_org = [new DateTime()];
        aKS::sortItems($items);
        $this->assertEquals($items, $items_org);

        $items = $items_org = [
            new KI("a", 1, 1, 1),
            new KI("0", 1, 1, 1),
            new KI("A", 1, 1, 1),
            new KI(" ", 1, 1, 1),
        ];

        aKS::sortItems($items, "NotExists");
        $this->assertEquals($items, $items_org);

        aKS::sortItems($items);
        $this->assertEquals($items, $items_org);
        
        aKS::sortItems($items, "Name");
        $this->assertEquals($items[0]->getName(), "A");
        $this->assertEquals($items[1]->getName(), $name_default);
        $this->assertEquals($items[2]->getName(), $name_default);
        $this->assertEquals($items[3]->getName(), "a");

        $items = $items_org = [
            new KI("a", 1, 1, 1),
            new KI("0", 3, 1, 1),
            new KI("A", 4, 1, 1),
            new KI(" ", 2, 1, 1),
        ];
        aKS::sortItems($items);
        $this->assertEquals($items[0]->getName(), "a");
        $this->assertEquals($items[1]->getName(), $name_default);
        $this->assertEquals($items[2]->getName(), $name_default);
        $this->assertEquals($items[3]->getName(), "A");

        $items = $items_org = [
            new KI("a", 1, 2, 1),
            new KI("0", 1, 4, 1),
            new KI("A", 1, 3, 1),
            new KI(" ", 1, 1, 1),
        ];
        aKS::sortItems($items, "Value");
        $this->assertEquals($items[0]->getName(), $name_default);
        $this->assertEquals($items[1]->getName(), "a");
        $this->assertEquals($items[2]->getName(), "A");
        $this->assertEquals($items[3]->getName(), $name_default);

        $items = $items_org = [
            new KI("a", 1, 1, 3),
            new KI("0", 1, 1, 1),
            new KI("A", 1, 1, INFINITE),
            new KI(" ", 1, 1, 2),
        ];
        aKS::sortItems($items, "Count");
        $this->assertEquals($items[0]->getName(), $name_default);
        $this->assertEquals($items[1]->getName(), $name_default);
        $this->assertEquals($items[2]->getName(), "a");
        $this->assertEquals($items[3]->getName(), "A");
    }

    public function testConvertTo01Pack() {
        $items = $items_org = "string";
        $name_default = "Knapsack Item";
        
        aKS::convertTo01Pack($items);
        $this->assertEquals($items, $items_org);

        $items = $items_org = array();
        aKS::convertTo01Pack($items);
        $this->assertEquals($items, $items_org);

        $items = $items_org = [new DateTime()];
        aKS::convertTo01Pack($items);
        $this->assertEquals($items, $items_org);

        $items = $items_org = [
            new KI("a", 1, 1, 1),
            new KI("0", 1, 1, 1),
            new KI("A", 1, 1, 1),
            new KI(" ", 1, 1, 1),
        ];

        aKS::convertTo01Pack($items);
        $this->assertCount(4, $items);
        $this->assertTrue(in_array(new KI("a 1", 1, 1, 1), $items, false));
        $this->assertTrue(in_array(new KI($name_default." 1", 1, 1, 1), $items, false));
        $this->assertTrue(in_array(new KI("A 1", 1, 1, 1), $items, false));
        $this->assertTrue(in_array(new KI($name_default." 2", 1, 1, 1), $items, false));

        $items = $items_org = [
            new KI("a", 1, 1, 3),
            new KI("0", 1, 1, 2),
            new KI("A", 1, 1, 1),
            new KI(" ", 1, 1, 4),
        ];

        aKS::convertTo01Pack($items);
        $this->assertCount(10, $items);
        $this->assertTrue(in_array(new KI("a 1", 1, 1, 1), $items));
        $this->assertTrue(in_array(new KI("a 2", 1, 1, 1), $items));
        $this->assertTrue(in_array(new KI("a 3", 1, 1, 1), $items));
        $this->assertTrue(in_array(new KI($name_default." 1", 1, 1, 1), $items));
        $this->assertTrue(in_array(new KI($name_default." 2", 1, 1, 1), $items));
        $this->assertTrue(in_array(new KI("A 1", 1, 1, 1), $items));
        $this->assertTrue(in_array(new KI($name_default." 3", 1, 1, 1), $items));
        $this->assertTrue(in_array(new KI($name_default." 4", 1, 1, 1), $items));
        $this->assertTrue(in_array(new KI($name_default." 5", 1, 1, 1), $items));
        $this->assertTrue(in_array(new KI($name_default." 6", 1, 1, 1), $items));
    }
}

?>