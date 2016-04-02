<?php

// Thanks to http://tech.vg.no/2013/07/19/using-phps-built-in-web-server-in-your-test-suites/

// Command that starts the built-in web server
$command = sprintf(
    'php -S %s:%d -file ./tests/server.php >/dev/null 2>&1 & echo $!',
    WEB_SERVER_HOST,
    WEB_SERVER_PORT
);

// Execute the command and store the process ID
$output = array();
exec($command, $output);
$pid = (int) $output[0];

echo sprintf(
        '%s - Web server started on %s:%d with PID %d',
        date('r'),
        WEB_SERVER_HOST,
        WEB_SERVER_PORT,
        $pid
    ) . PHP_EOL;

// Kill the web server when the process ends
register_shutdown_function(function() use ($pid) {
    echo sprintf('%s - Killing process with ID %d', date('r'), $pid) . PHP_EOL;
    exec('kill ' . $pid);
});

// quick fix to wait until the built-in php server is up
sleep(1);

// Get composer dependencies
require __DIR__.'/../vendor/autoload.php';