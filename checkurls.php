<?php
require 'core.php';

$argv = $_SERVER['argv'];
foreach ($argv as $key=>$value) {
    switch ($value) {
        case '-u':
            $url = !empty($argv[$key+1]) ? $argv[$key+1] : null;
            (new CheckUrls($url))->run();
            break;
    }
}

//Show help
if (empty($_SERVER['argv'][1])) {
    echo "Example for run: php checkurls.php -u 'http://site.com/' > result.csv\r\n";
}