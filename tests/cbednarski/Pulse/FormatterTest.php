<?php

require_once(__DIR__ . '/../../../vendor/autoload.php');

use cbednarski\Pulse\Pulse as Pulse;
use cbednarski\Pulse\Formatter as Formatter;

class FormatterTest extends PHPUnit_Framework_TestCase
{
    private $pulse;

    public function setUp()
    {
        $pulse = new Pulse();

        $pulse->add('This test should pass', function(){
            return true;
        });
        $pulse->add('This test should fail', function(){
            return false;
        });

        $this->pulse = $pulse;
    }

    public function testToJson()
    {
        // Because I'm that lazy.
        $pulse = $this->pulse;

        $formatter = new Formatter($pulse->getHealthchecks());
        $expected = '{"status":false,"healthchecks":[{"description":"This test should pass","passing":true},{"description":"This test should fail","passing":false}]}';

        $this->assertEquals($expected, $formatter->toJson());
    }

    public function testToHtml()
    {
        $pulse = $this->pulse;
    }

    public function testToCli()
    {
        $pulse = $this->pulse;
    }

    public function testIsBrowser()
    {
        $formatter = new Formatter(array());

        $_SERVER['HTTP_USER_AGENT'] = 'Opera/9.80 (Android 4.0.4; Linux; Opera Mobi/ADR-1301080958) Presto/2.11.355 Version/12.10';
        $this->assertTrue($formatter->isBrowser());

        $_SERVER['HTTP_USER_AGENT'] = 'Java/1.6.0_22';
        $this->assertFalse($formatter->isBrowser());

        $_SERVER['HTTP_USER_AGENT'] = 'Mozilla/5.0 (Windows; U; nl-NL) AppleWebKit/533.19.4 (KHTML, like Gecko) AdobeAIR/3.1';
        $this->assertTrue($formatter->isBrowser());

        unset($_SERVER['HTTP_USER_AGENT']);
        $this->assertFalse($formatter->isBrowser());

    }

    public function testAcceptsJson()
    {
        $formatter = new Formatter(array());

        $_SERVER['HTTP_ACCEPT'] = 'text/html';
        $this->assertFalse($formatter->acceptsJson());

        $_SERVER['HTTP_ACCEPT'] = 'application/json';
        $this->assertTrue($formatter->acceptsJson());

        // Technically this is not compliant, but we'll accept it anyway.
        $_SERVER['HTTP_ACCEPT'] = 'text/html; Application/JSON';
        $this->assertTrue($formatter->acceptsJson());

        unset($_SERVER['HTTP_ACCEPT']);
        $this->assertFalse($formatter->acceptsJson());
    }
}