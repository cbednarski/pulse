<?php

namespace cbednarski\Pulse;

class Formatter
{
    private $healthchecks;

    public function __construct(Array $healthchecks)
    {
        $this->healthchecks = $healthchecks;
    }

    public function toJson()
    {
        $this->contentType('application/json');

        $temp = array('status' => false, 'healthchecks' => array());

        foreach ($this->healthchecks as $healthcheck) {
            $temp['healthchecks'][] = array(
                'description' => $healthcheck->getDescription(),
                'passing' => $healthcheck->getStatus()
            );
        }

        return json_encode($temp);;
    }

    public function toHtml()
    {
        $this->contentType('text/html');
    }

    public function toCli()
    {
        $this->contentType("text/plain");
    }

    public function autoexec()
    {
        if ($this->acceptsJson()) {
            echo $this->toJson();
        } elseif ($this->isBrowser()) {
            echo $this->toHtml();
        } else {
            echo $this->toCli();
        }
    }

    public function acceptsJson()
    {
        // Guard if we're not running in a web server
        if (!isset($_SERVER['HTTP_ACCEPT'])) {
            return false;
        }

        return strpos(strtolower($_SERVER['HTTP_ACCEPT']), 'application/json') !== false;
    }

    public function isBrowser()
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

    private function contentType($type)
    {
        if (php_sapi_name() !== 'cli') {
            header('Content-Type: ' . $type);
        }
    }
}