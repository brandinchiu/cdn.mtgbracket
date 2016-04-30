<?php
/**
 * Created by PhpStorm.
 * User: brandin
 * Date: 4/30/2016
 * Time: 9:34 AM
 */

require_once __DIR__ . '/../vendor/autoload.php';

use Symfony\Component\HttpFoundation\Request;

$app = new Silex\Application();

$app['debug'] = true;

/**
 * GET root
 */
$app->get('/', function(Silex\Application $app, Request $request){

    return print_r($_SERVER, 1);
});

$app->run();