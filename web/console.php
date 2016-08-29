<?php
/**
 * Project: cdn.mtgbracket.
 * User: Brandin
 * Date: 8/28/2016
 * Time: 7:02 PM
 */

require_once __DIR__ . '/../vendor/autoload.php';

$app = new Silex\Application();
//$app->register(new Igorw\Silex\ConfigServiceProvider(__DIR__ . '/../config/parameters.yml'));
//$app->register(new Igorw\Silex\ConfigServiceProvider(__DIR__ . '/../config/config.yml', $app['parameters']));
$app->register(new Knp\Provider\ConsoleServiceProvider(), [
    'console.name' => 'cdn.mtgbracket.console',
    'console.version' => '1.0.0',
    'console.project_directory' => __DIR__ . '/..'
]);

$console = $app['console'];

$console->add(new MisfitPixel\Command\CardCompressionCommand());

$console->run();