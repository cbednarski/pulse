<?php

require_once(__DIR__ . '/../../../vendor/autoload.php');

use cbednarski\Pulse\Pulse as Pulse;
use cbednarski\Pulse\Healthcheck as Healthcheck;

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
}