<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('padlet');
});

Route::get('/{any}', function ($any) {
    if ($any === 'api' || Str::startsWith($any, 'api/')) {
        // Get the routes defined in api.php
        $routes = collect(Route::getRoutes())->filter(function ($route) {
            return Str::startsWith($route->uri, 'api/');
        })->map(function ($route) {
            return [
                'method' => $route->methods()[0],
                'uri' => $route->uri,
                'name' => $route->getName(),
                'action' => $route->getActionName(),
            ];
        });
        return view('api', ['routes' => $routes]);
    } else {
        return redirect('https://app.s2010456012.student.kwmhgb.at/');
    }
})->where('any', '^(?!api\/).*$');
