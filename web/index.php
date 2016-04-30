<?php
/**
 * Created by PhpStorm.
 * User: brandin
 * Date: 4/30/2016
 * Time: 9:34 AM
 */

require_once __DIR__ . '/../vendor/autoload.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception;


$app = new Silex\Application();

$app['debug'] = true;

/**
 * error handler
 */
$app->error(function(\Exception $e, $code){
    $response = new Response();

    switch($code){
        case Response::HTTP_NOT_FOUND:
            $message = 'not found';
            $response->setStatusCode(Response::HTTP_NOT_FOUND);

            break;
        default:
            $message = 'error';

            break;
    }

    $response->setContent($message);

    return $response;
});

/**
 * GET root
 */
$app->get('/', function(Silex\Application $app, Request $request){
    return print_r($_SERVER, 1);
});

$app->get('/images', function(Silex\Application $app, Request $request){
    return 'images root';
});

/**
 * load cards
 */
$app->get('/images/cards/{expansion}/{card}', function(Silex\Application $app, Request $request, $expansion, $card){
    /**
     * TODO: compression logic.
     */
    $compression = 'uncompressed'; // | compressed
    
    $path = sprintf('%s/../images/cards/%s/%s/%s', __DIR__, $compression, $expansion, $card);

    if(!file_exists($path)){
        $app->abort(404);
    }

    return $app->sendFile($path);
});

$app->run();