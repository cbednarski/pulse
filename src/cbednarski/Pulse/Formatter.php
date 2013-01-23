<?php

namespace cbednarski\Pulse;

class Formatter
{
    private $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function toJson()
    {
        $this->contentType('application/json');
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

    private function contentType($type)
    {
        if (php_sapi_name() !== 'cli') {
            header('Content-Type: ' . $type);
        }
    }

    private function acceptsJson()
    {
        // Guard if we're not running in a web server
        if (!isset($_SERVER['HTTP_ACCEPT'])) {
            return false;
        }

        return strpos(strtolower($_SERVER['HTTP_ACCEPT']), 'application/json') !== false;
    }

    private function isBrowser()
    {
        if (isset($_SERVER['HTTP_USER_AGENT'])) {
            foreach (array('Chrome', 'Firefox', 'Safari', 'Opera') as $browser) {
                if (strpos($_SERVER['HTTP_USER_AGENT'], $browser) !== false) {
                    return true;
                }
            }
        }
        return false;
    }
}