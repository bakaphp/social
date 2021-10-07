<?php

/**
 * Setup autoload.
 */

use function Baka\appPath;
use Dotenv\Dotenv;
use Phalcon\Loader;

if (!defined('ROOT_DIR')) {
    define('ROOT_DIR', dirname(__DIR__) . '/');
}

//load classes
$loader = new Loader();
$loader->registerNamespaces([
    'Kanvas\Social' => appPath('src/'),
    'Kanvas\Social\Test' => appPath('tests/'),
    'Kanvas\Social\Test\Support' => appPath('tests/_support'),
]);

$loader->register();

require appPath('vendor/autoload.php');

$dotEnv = Dotenv::createImmutable(__DIR__ . '/../');
$dotEnv->load();
