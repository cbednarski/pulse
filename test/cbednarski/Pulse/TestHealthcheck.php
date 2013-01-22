<?php

require_once(__DIR__ . '/../../../vendor/autoload.php');

class TestHealthcheck extends PHPUnit_Framework_TestCase
{
    public function testGetDescription()
    {
        $description = 'My description';
        $check = new cbednarski\Pulse\Healthcheck($description, function(){});
        $this->assertEquals($description, $check->getDescription());
    }

    public function testGetStatus()
    {
        $check = new cbednarski\Pulse\Healthcheck('testing!', function(){return true;});
        $this->assertTrue($check->getStatus(), 'Verify truthy return value');

        $test = new StdClass();
        $test->blah = 1;

        $check2 = new cbednarski\Pulse\Healthcheck('testing!', function() use ($test) {
            $test->blah++;
            return false;
        });

        $check2->getStatus();
        $this->assertEquals(2, $test->blah, 'Test using closure and `use`');

        $check2->getStatus();
        $this->assertEquals(2, $test->blah, 'Test is only executed once');
    }
}