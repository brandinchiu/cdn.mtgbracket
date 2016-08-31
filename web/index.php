<?php
/**
 * Created by PhpStorm.
 * User: brandin
 * Date: 4/30/2016
 * Time: 9:34 AM
 */

use MisfitPixel\Exception;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

require_once __DIR__ . '/../vendor/autoload.php';

$app = new Silex\Application();
$app['debug'] = true;

/**
 * error handler.
 */
$app->error(function(HttpException $e) use ($app){
    $code = $e->getStatusCode();
    $response = new Response();
    $response->headers->set('Content-Type', 'application/json');

    switch(true){
        case $e instanceof Exception\CardNotFoundException:
            break;

        case $e instanceof Exception\FileNotFoundException:
            break;

        default:
            break;
    }

    /**
     * build debug content.
     */
    $content = [
        'error' => [
            'code' => $code,
            'message' => $e->getMessage()
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
        return $app->sendFile(sprintf('%s/../images/cards/back.png', __DIR__));
    }

    return $app->sendFile($path);
});

/**
 * image path for all other assets.
 */
$app->get('/images/{image}', function(Silex\Application $app, Request $request, $image){
    $path = sprintf('%s/../images/%s', __DIR__, $image);

    if(!file_exists($path)){
        throw new Exception\FileNotFoundException(404, 'File not found.');
    }

    return $app->sendFile($path);
})->assert('image', '.*');

$app->run();