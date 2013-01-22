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

    }

    public function toHtml()
    {

    }

    public function toCli()
    {

    }

    public function autoexec()
    {
        if(php_sapi_name() === 'cli') {
            echo $this->toCli();
        } elseif($this->acceptsJson()) {
            echo $this->toJson();
        } elseif($this->isBrowser()) {
            echo $this->toHtml();
        } else {
            $this->contentType("text/plain");
            echo $this->toCli();
        }
        // if cli, do cli
        // if browser, do html
        // if accepts = application/json
        // else do plaintext
    }

    private function contentType($type)
    {
        if(php_sapi_name() !== 'cli') {
            header('Content-Type: ' . $type);
        }
    }

    private function acceptsJson()
    {
        // Guard if we're not running in a web server
        if(!isset($_SERVER['HTTP_ACCEPT'])) {
            return false;
        }

        return strpos(strtolower($_SERVER['HTTP_ACCEPT']), 'application/json') !== false;
    }

    private function isBrowser()
    {
        if(isset($_SERVER['HTTP_USER_AGENT'])) {
            foreach(array('Chrome', 'Firefox', 'Safari', 'Opera') as $browser){
                if(strpos($_SERVER['HTTP_USER_AGENT'], $browser) !== false){
                    return true;
                }
            }
        }
        return false;
    }
}