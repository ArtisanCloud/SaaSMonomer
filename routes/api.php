<?php
declare(strict_types=1);


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


$_methodAll = config('artisancloud.framework.router.methodAll');
$_methodGet = config('artisancloud.framework.router.methodGet');
$_methodPost = config('artisancloud.framework.router.methodPost');
$_methodPut = config('artisancloud.framework.router.methodPut');
$_methodDelete = config('artisancloud.framework.router.methodDelete');
$_api_version = config('artisancloud.framework.api_version');
$_namespaceAPI = 'ArtisanCloud\SaaSMonomer\Http\Controllers\API';


/** Tenant **/
Route::group(
    [
        'namespace' => $_namespaceAPI,
        'prefix' => "api/{$_api_version}",
        'domain' => $_tenant_domain,
        'middleware' => ['checkHeader', 'checkUser']
    ], function () use ($_methodGet, $_methodPost, $_methodPut, $_methodDelete) {

    Route::match($_methodPost, 'user/register', 'UserAPIController@apiRegister')->name('user.write.register');
});


Route::group(
    [
        'namespace' => $_namespaceAPI,
        'prefix' => "api/{$_api_version}",
        'domain' => $_tenant_domain,
        'middleware' => ['checkHeader', 'auth:api', 'checkUser']
    ], function () use ($_methodGet, $_methodPost, $_methodPut, $_methodDelete) {

//    Route::match($_methodPost, 'invitation/generate', 'InvitationCodeAPIController@apiBatchGenerateCode')->name('code.write.invitation.generate');
});