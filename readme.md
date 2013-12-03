# Pulse

Pulse allows you to easily write healthchecks for your application and display a simple, aggregated report so you can quickly diagnose whether and why your app is having trouble (or whether you can blame someone else). You can also monitor your healthchecks with [nagios](http://www.nagios.org/), [zabbix](http://www.zabbix.com/), etc.

[![Build Status](https://travis-ci.org/cbednarski/pulse.png)]
(https://travis-ci.org/cbednarski/pulse)

#### Wait, what's a healthcheck?

Healthchecks are a great way to test system health and connectivity to other services. For example, you can verify connectivity to memcache or mysql, that your app can read / write to certain files, or that your API key for a third-party service is still working.

## Installation

You can install this into your project using [composer](http://getcomposer.org/doc/00-intro.md#installation-nix). Create a `composer.json` file in the root of your project and add the following:

```json
{
    "require": {
        "php": ">=5.3.0",
        "cbednarski/Pulse": "1.0.*"
    }
}
```

Run `composer install`, include `vendor/autoload.php`, and you're off to the races!

## Example Usage

#### Critical Checks

Healthchecks are critical by default, which means that the healthcheck page will return a 503 status code. Use these to see when there is a critical failure in your system. Critical checks must return boolean `true` or `false`. Add them with `add()`, or more explicitly with `addCritical()`.

Here's an example implementation of `healthcheck.php` that checks connectivity to memcache:

```php
$pulse = new cbednarski\Pulse\Pulse();

$pulse->add("Check that config file is readable", function(){
	return is_readable('/path/to/your/config/file');
});

include '/path/to/your/config/file';

$pulse->addCritical("Check memcache connectivity", function() use ($config) {
	$memcache = new Memcache();
	if(!$memcache->connect($config['memcache_host'], $config['memcache_port'])){
		return false;
	}
	$key = 'healthcheck_test_key'
	$msg = 'memcache is working';
	$memcache->set($key, $msg);
	return $memcache->get($key) === $msg;
});
```

#### Warnings

For non-critical checks you can use a warning and you'll get status 200 even if these fail. Use these to see when your app is experiencing service degredation but is still available. Warning checks must return boolean `true` or `false`.

```php
$pulse->addWarning("Verify connectivity to youtube", function() {
	$youtube = new YoutubeClient();
	return $youtube->isUp();
});
```

#### Information

If you want to pass back non-boolean, informational data, you can use `addInfo()`.

```php
$pulse->addInfo("Today is", function() {
	return date('l');
});

$pulse->check();
```

## Response Specification

Pulse can be run via command-line, accessed via the browser, or used with tools like CURL.

Pulse automatically detects whether you're running from a browser, commandline, or CURLy interface and responds with color-blind-friendly html, json, or plaintext as appropriate.

#### Status Codes

Pulse responds with `200` status codes when all tests pass. If a test fails, pulse responds with `503`. You can see these via curl:

	$ curl -i http://example.com/healthcheck.php

#### JSON Format

To enable json-y goodness, you'll need to send `Accept: application/json`. E.g:

	$ curl -H "Accept: application/json" http://example.com/healthcheck.php

## Examples

You can see some very basic example healthchecks in `healthcheck-sample.php`. If you have php 5.4 or above, running `make dev` will load this so you can see it in action and play around with it.

## Does Pulse Work With X?

Yep. Pulse is designed to be self-contained and is very simple, so it doesn't require you to use any particular framework. You are free to include other things like yml parsers, etc. if you choose, but we recommend NOT including a full framework stack on top of it. If the framework fails to load for some reason, your healthchecks won't be displayed, meaning they're not useful for diagnosing whatever problem you've encountered.

## Won't This Expose Information About My App?

Potentially. You *probably* don't want to display the healthcheck results to the public. Instead, you could [whitelist certain IPs](http://httpd.apache.org/docs/2.2/howto/access.html).
