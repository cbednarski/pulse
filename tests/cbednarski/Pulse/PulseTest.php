<?php

require_once(__DIR__ . '/../../../vendor/autoload.php');

use cbednarski\Pulse\Pulse;
use cbednarski\Pulse\Healthcheck;

class PulseTest extends PHPUnit_Framework_TestCase
{
    public function testBasicUsage()
    {
        $file = __FILE__;

        $pulse = new Pulse();

        // Dynamically add a new healthcheck
        $pulse->add("Test that this file exists", function () use ($file) {
            return file_exists($file);
        });

        // Add a healtcheck we created manually
        $healthcheck = new Healthcheck('description', function () {
            return true;
        });
        $pulse->addHealthcheck($healthcheck);

        // Verify healthcheck aggregate is passing up to this point
        $this->assertTrue($pulse->getStatus());

        // Add a failing healthcheck
        $pulse->add('falsy', function () {
            return false;
        });

        // Verify healthcheck aggregate is failing now
        $this->assertFalse($pulse->getStatus());
    }

    public function testTypes()
    {
        $pulse = new Pulse();

        $pulse->addWarning("Test explicit warning", function() {
            return false;
        });

        $pulse->addInfo("Output some info", function() {
            return "Testing!";
        });

        $this->assertEquals(true, $pulse->getStatus(),
            "No critical failures, summary should pass");

        $pulse->add("Test default (warning)", function() {
            return false;
        });

        $pulse->addCritical("Test critical failure", function() {
            return false;
        });

        // At this point we have one critical failure so the check should fail
        $this->assertEquals(false, $pulse->getStatus(),
            "One critical failure, summary should fail");


        $array = $pulse->getHealthchecks();

        $this->assertEquals(Healthcheck::WARNING,  $array[0]->getType());
        $this->assertEquals(Healthcheck::INFO,     $array[1]->getType());
        $this->assertEquals(Healthcheck::CRITICAL, $array[2]->getType());
        $this->assertEquals(Healthcheck::CRITICAL, $array[3]->getType());
    }
}