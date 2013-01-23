<?php

require_once(__DIR__ . '/../../../vendor/autoload.php');

use cbednarski\Pulse\Pulse as Pulse;
use cbednarski\Pulse\Formatter as Formatter;

class FormatterTest extends PHPUnit_Framework_TestCase
{
    private $fail_pulse;

    public function setUp()
    {
        $this->fail_pulse = new Pulse();

        $this->fail_pulse->add('This test should pass', function(){
            return true;
        });
        $this->fail_pulse->add('This test should fail', function(){
            return false;
        });

        $this->success_pulse = new Pulse();

        $this->success_pulse->add('This test should pass', function(){
            return true;
        });
        $this->success_pulse->add('This test should also pass', function(){
            return true;
        });
    }

    public function testToJsonFailure()
    {
        $expected = '{"all-passing":false,"healthchecks":[{"description":"This test should pass","passing":true},{"description":"This test should fail","passing":false}]}';

        $this->assertEquals($expected, Formatter::toJson($this->fail_pulse));
    }

    public function testToJsonSuccess()
    {
        $expected = '{"all-passing":true,"healthchecks":[{"description":"This test should pass","passing":true},{"description":"This test should also pass","passing":true}]}';

        $this->assertEquals($expected, Formatter::toJson($this->success_pulse));
    }

    public function testToHtml()
    {

    }

    public function testToPlainFailure()
    {
        $expected = <<<HEREDOC
This test should pass: pass
This test should fail: fail

Healthcheck summary: fail
HEREDOC;
    }

    public function testToPlainSuccess()
    {
        $expected = <<<HEREDOC
This test should pass: pass
This test should also pass: pass

Healthcheck summary: pass
HEREDOC;
    }

    public function testIsBrowser()
    {
        $_SERVER['HTTP_USER_AGENT'] = 'Opera/9.80 (Android 4.0.4; Linux; Opera Mobi/ADR-1301080958) Presto/2.11.355 Version/12.10';
        $this->assertTrue(Formatter::isBrowser());

        $_SERVER['HTTP_USER_AGENT'] = 'Java/1.6.0_22';
        $this->assertFalse(Formatter::isBrowser());

        $_SERVER['HTTP_USER_AGENT'] = 'Mozilla/5.0 (Windows; U; nl-NL) AppleWebKit/533.19.4 (KHTML, like Gecko) AdobeAIR/3.1';
        $this->assertTrue(Formatter::isBrowser());

        unset($_SERVER['HTTP_USER_AGENT']);
        $this->assertFalse(Formatter::isBrowser());
    }

    public function testAcceptsJson()
    {
        $_SERVER['HTTP_ACCEPT'] = 'text/html';
        $this->assertFalse(Formatter::acceptsJson());

        $_SERVER['HTTP_ACCEPT'] = 'application/json';
        $this->assertTrue(Formatter::acceptsJson());

        // Technically this is not compliant, but we'll accept it anyway.
        $_SERVER['HTTP_ACCEPT'] = 'text/html; Application/JSON';
        $this->assertTrue(Formatter::acceptsJson());

        unset($_SERVER['HTTP_ACCEPT']);
        $this->assertFalse(Formatter::acceptsJson());
    }
}