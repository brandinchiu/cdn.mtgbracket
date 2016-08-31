<?php
/**
 * Created by PhpStorm.
 * User: brandin
 * Date: 4/30/2016
 * Time: 9:34 AM
 */

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception;

require_once __DIR__ . '/../vendor/autoload.php';

$app = new Silex\Application();
$app['debug'] = true;

/**
 * error handler.
 */
$app->error(function(Exception\HttpException $e) use ($app){
    $code = $e->getStatusCode();
    $response = new Response();

    switch($code){
        case Response::HTTP_NOT_FOUND:
            /**
             * TODO: provide default 404 image.
             */
            $message = 'not found';
            $response->setStatusCode(Response::HTTP_NOT_FOUND);

            break;
        default:
            $message = 'error';
            $response->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);

            break;
    }

    /**
     * build debug content.
     */
    $content = [
        'error' => [
            'code' => $code,
            'message' => ($app['debug']) ? $e->getMessage() : $message,
        ]
    ];

    if($app['debug']){
        $content['error']['trace'] =  [
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ];
    }

    $response->setContent(json_encode($content));

    return $response;
});

//$app->register(new Igorw\Silex\ConfigServiceProvider(__DIR__ . '/../config/parameters.yml'));
//$app->register(new Igorw\Silex\ConfigServiceProvider(__DIR__ . '/../config/config.yml', $app['parameters']));

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
    $compression = ($referrer == null) ? 'uncompressed' : 'compressed';

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