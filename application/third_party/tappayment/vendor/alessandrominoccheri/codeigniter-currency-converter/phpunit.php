<?php
error_reporting(E_ALL);
$autoloader = __DIR__ . '/vendor/autoload.php';
if (! file_exists($autoloader)) {
    echo "Composer autoloader not found: $autoloader" . PHP_EOL;
    echo "Please issue 'composer install' and try again." . PHP_EOL;
    exit(1);
}
require $autoloader;

// Fix argv for CodeIgniter
$_SERVER['argv'] = [
    'cli',
];
$_SERVER['argc'] = 1;

// Install libraries/Twig.php
copy('libraries/CurrencyConverter.php', 'vendor/codeigniter/framework/application/libraries/CurrencyConverter.php');
copy('tests/currency_converter.php', 'vendor/codeigniter/framework/application/config/currency_converter.php');
copy('tests/database.php', 'vendor/codeigniter/framework/application/config/database.php');

require __DIR__ . '/ci_instance.php';
