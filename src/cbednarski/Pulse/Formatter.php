<?php

namespace cbednarski\Pulse;

class Formatter
{
    public static function toJson(Pulse $pulse)
    {
        static::contentType('application/json');

        $temp = array('all-passing' => $pulse->getStatus(), 'healthchecks' => array());

        foreach ($pulse->getHealthchecks() as $healthcheck) {
            $temp['healthchecks'][] = array(
                'description' => $healthcheck->getDescription(),
                'type' => $healthcheck->getType(),
                'passing' => $healthcheck->getStatus()
            );
        }

        static::responseIsPassing($temp['all-passing']);

        return json_encode($temp);;
    }

    public static function getTemplate()
    {
        return file_get_contents(__DIR__ . '/template.html');
    }

    public static function toHtml(Pulse $pulse)
    {
        static::contentType('text/html');

        $output = static::getTemplate();
        $checks = '';

        // We'll do this first since it's less volatile
        $output = str_replace('%summary%', static::htmlSummary($pulse->getStatus()), $output);

        foreach ($pulse->getHealthchecks() as $healthcheck) {
            $checks .= static::htmlHealthcheck($healthcheck);
        }

        $output = str_replace('%healthchecks%', $checks, $output);

        static::responseIsPassing($pulse->getStatus());

        return $output;
    }

    public static function htmlHealthcheck(Healthcheck $healthcheck)
    {        
        return '            <li class="healthcheck '.static::statusToStr($healthcheck->getStatus()).'">'.$healthcheck->getDescription().': <b>'.static::statusToStr($healthcheck->getStatus()).'</b></li>'."\n";
    }

    public static function htmlSummary($status)
    {
        return '            <li class="summary '.static::statusToStr($status).'">Healthcheck summary: '.static::statusToStr($status).'</li>';
    }

    public static function toPlain(Pulse $pulse)
    {
        static::contentType("text/plain");

        $temp = '';

        foreach ($pulse->getHealthchecks() as $healthcheck) {
            $temp .= $healthcheck->getDescription() . ': ' . self::statusToStr($healthcheck->getStatus()) . PHP_EOL;
        }

        $temp .= PHP_EOL . 'Healthcheck summary: ' . self::statusToStr($pulse->getStatus());

        static::responseIsPassing($pulse->getStatus());

        return $temp;
    }

    public static function statusToStr($status)
    {
        if($status) {
            return 'pass';
        } else {
            return 'fail';
        }
    }

    public static function autoexec(Pulse $pulse)
    {
        if (static::acceptsJson()) {
            echo static::toJson($pulse);
        } elseif (static::isBrowser()) {
            echo static::toHtml($pulse);
        } else {
            echo static::toPlain($pulse);
        }
    }

    public static function acceptsJson()
    {
        // Guard if we're not running in a web server
        if (!isset($_SERVER['HTTP_ACCEPT'])) {
            return false;
        }

        return strpos(strtolower($_SERVER['HTTP_ACCEPT']), 'application/json') !== false;
    }

    public static function isBrowser()
    {
        if (isset($_SERVER['HTTP_USER_AGENT'])) {
            foreach (array('Mozilla', 'Opera', 'AppleWebKit') as $browser) {
                if (strpos($_SERVER['HTTP_USER_AGENT'], $browser) !== false) {
                    return true;
                }
            }
        }
        return false;
    }

    private static function contentType($type)
    {
        if (php_sapi_name() !== 'cli') {
            header('Content-Type: ' . $type);
        }
    }

    private static function responseIsPassing($isPassing)
    {
        if (php_sapi_name() !== 'cli') {
            if ($isPassing) {
                http_response_code(200);
            } else {
                http_response_code(503);
            }
        }
    }
}