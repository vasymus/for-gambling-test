<?php

use App\Support\Affiliate\Facade\AffiliateFacade;
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
    dump($affiliates = AffiliateFacade::parse(
        new SplFileInfo(storage_path('app/tests/affiliates.txt'))
    ));
    $closest = AffiliateFacade::getClosest(
        new \App\DTOs\GeoPositionDTO(
            53.3340285,
            -6.2535495
        ),
        100,
        $affiliates
    );
    dd(collect($closest)->sortBy('id')->values()->all());
    return view('welcome');
});
