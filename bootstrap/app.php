<?php

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

$php84Class = __DIR__ . '/../vendor/symfony/polyfill-php84/Php84.php';
$php84Polyfill = __DIR__ . '/../vendor/symfony/polyfill-php84/bootstrap.php';
$php85Class = __DIR__ . '/../vendor/symfony/polyfill-php85/Php85.php';
$php85Polyfill = __DIR__ . '/../vendor/symfony/polyfill-php85/bootstrap.php';

if (file_exists($php84Class)) {
    require_once $php84Class;
}

if (file_exists($php84Polyfill)) {
    require_once $php84Polyfill;
}

if (file_exists($php85Class)) {
    require_once $php85Class;
}

if (file_exists($php85Polyfill)) {
    require_once $php85Polyfill;
}

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'webLang' => \App\Http\Middleware\WebLang::class,
            'lang' => \App\Http\Middleware\Lang::class,
            'CheckDeliveryman' => \App\Http\Middleware\CheckDeliveryman::class,
        ]);
    })

    ->withExceptions(function (Exceptions $exceptions) {
        // $exceptions->render(function (NotFoundHttpException $e, Request $request) {
        //     if ($request->is('api/*')) {
        //         return Response::api(__('message.Not Found'), 404, false, 404);
        //     }
        // });

        $exceptions->render(function (AuthenticationException $e, Request $request) {
            if ($request->is('api/*')) {
                return Response::api(__('message.Unauthorized'), 401, false, 401);
            }
        });

        // $exceptions->render(function (Throwable $e, Request $request) {
        //     if ($request->is('api/*')) {
        //         return Response::api(__('message.Internal Server Error'), 500, false, 500);
        //     }
        // });
    })->create();
