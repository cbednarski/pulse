<?php

require_once(__DIR__ . '/../../../vendor/autoload.php');

class PulseTest extends PHPUnit_Framework_TestCase
{
    public function testBasicUsage()
    {
        $file = __FILE__;

        $pulse = new cbednarski\Pulse\Pulse();
        $pulse->add(
            "Test that this file exists",
            function () use ($file) {
                return file_exists($file);
            }
        );
        $pulse->check();
    }
}