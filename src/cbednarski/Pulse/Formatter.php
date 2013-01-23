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
                'passing' => $healthcheck->getStatus()
            );
        }

        static::responsePassing($temp['all-passing']);

        return json_encode($temp);;
    }

    public static function toHtml(Pulse $pulse)
    {
        static::contentType('text/html');

        static::responsePassing($temp['all-passing']);
    }

    public static function toPlain(Pulse $pulse)
    {
        static::contentType("text/plain");

        $temp = '';

        foreach ($pulse->getHealthchecks() as $healthcheck) {
            $temp .= $healthcheck->getDescription() . ': ' . self::statusToStr($healthcheck->getStatus()) . PHP_EOL;
        }

        $temp .= PHP_EOL . 'Healthcheck summary: ' . self::statusToStr($pulse->getStatus());

        static::responsePassing($pulse->getStatus());

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

    private static function responsePassing($isPassing)
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