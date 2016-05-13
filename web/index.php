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
    return 'hello cdn';
});

/**
 * load cards.
 */
$app->get('/images/cards/{expansion}/{card}', function(Silex\Application $app, Request $request, $expansion, $card){
    /**
     * manage compression based on request referrer.
     */
    $referrer = str_replace(array('http://', 'https://'), '', $request->server->get('HTTP_REFERER'));
    $compression = (false === strpos('localhost', $referrer)) ? 'uncompressed' : 'compressed';

    file_put_contents(__DIR__ . "../../test.log", $referrer, FILE_APPEND);

    $path = sprintf('%s/../images/cards/%s/%s/%s', __DIR__, $compression, $expansion, $card);

    if(!file_exists($path)){
        $app->abort(404);
    }

    return $app->sendFile($path);
});

/**
 * image path for all other assets.
 */
$app->get('/images/{image}', function(Silex\Application $app, Request $request, $image){
    $path = sprintf('%s/../images/%s', __DIR__, $image);

    if(!file_exists($path)){
        $app->abort(404);
    }

    return $app->sendFile($path);
})->assert('image', '.*');

$app->run();