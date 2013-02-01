<?php

require_once(__DIR__ . '/../../../vendor/autoload.php');

use cbednarski\Pulse\Healthcheck;

class HealthcheckTest extends PHPUnit_Framework_TestCase
{
    public function testGetDescription()
    {
        $description = 'My description';
        $check = new Healthcheck($description, function () {});
        $this->assertEquals($description, $check->getDescription());
    }

    public function testGetStatus()
    {
        $check = new Healthcheck('testing!', function () {
            return true;
        });
        $this->assertTrue($check->getStatus(), 'Verify truthy return value');
        $this->assertEquals(Healthcheck::CRITICAL, $check->getType());

        $test = new StdClass();
        $test->blah = 1;

        $check2 = new Healthcheck('testing!', function () use ($test) {
            $test->blah++;
            return false;
        });

        $check2->getStatus();
        $this->assertEquals(2, $test->blah, 'Test using closure and `use`');

        $check2->getStatus();
        $this->assertEquals(2, $test->blah, 'Test is only executed once');
    }

    public function testWarning()
    {
        $check = new Healthcheck("This is a warning", function(){
            return true;
        }, Healthcheck::WARNING);

        $this->assertEquals(Healthcheck::WARNING, $check->getType());
    }

    public function testInfo()
    {
        $check = new Healthcheck("This is a info-level check", function(){
            return "This is a message!";
        }, Healthcheck::INFO);

        $this->assertEquals(Healthcheck::INFO, $check->getType());
        $this->assertEquals("This is a message!", $check->getStatus());
    }

    public function testCritical()
    {
        $check = new Healthcheck("This is a critical check", function(){
            return true;
        }, Healthcheck::CRITICAL);

        $this->assertEquals(Healthcheck::CRITICAL, $check->getType());
    }
}