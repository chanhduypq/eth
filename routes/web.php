<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', [
    'as' => 'boot', 'uses' => 'ExampleController@boot',
]);

$router->get('/admin', [
    'as' => 'admin.index', 'uses' => 'AdminController@index',
]);

$router->post('/admin/group/add', [
    'as' => 'addgroup', 'uses' => 'ExampleController@addGroup',
]);

$router->post('/admin/wallet/add', [
    'as' => 'addwallet', 'uses' => 'ExampleController@addWallet',
]);

$router->get('/group/get/{group}', [
    'as' => 'group', 'uses' => 'ExampleController@group',
]);

$router->post('/group/edit/{group}', [
    'as' => 'group.edit', 'uses' => 'ExampleController@groupEdit',
]);

$router->post('/admin/wallet/edit/{wallet}', [
    'as' => 'wallet.edit', 'uses' => 'ExampleController@walletEdit',
]);

$router->get('/payments/{wallet}', [
    'as' => 'payments', 'uses' => 'ExampleController@payments',
]);

$router->post('/admin/wallet/delete', [
    'as' => 'deleteWallet', 'uses' => 'AdminController@deleteWallet',
]);

$router->post('/admin/group/delete', [
    'as' => 'deleteGroup', 'uses' => 'AdminController@deleteGroup',
]);

$router->post('/admin/group/addrelation', [
    'as' => 'saveGroupRelation', 'uses' => 'ExampleController@saveGroupRelation',
]);

$router->post('/getHashratechartForMachine', [
    'as' => 'getHashratechartForMachine', 'uses' => 'ExampleController@getHashratechartForMachine',
]);


$router->get('/history/{wallet}/{id}/{time}', [
    'as' => 'workerHistory', 'uses' => 'ExampleController@workerHistory',
]);
$router->get('/multipleHistory/{group}/{time}', [
    'as' => 'multipleWorkerHistory', 'uses' => 'ExampleController@multipleWorkerHistory',
]);

$router->get('/payment/history/{wallet}/{time}', [
    'as' => 'paymentHistory', 'uses' => 'ExampleController@paymentHistory',
]);