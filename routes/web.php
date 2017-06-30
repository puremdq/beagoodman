<?php

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

//Route::any('/test', 'TestController@test');
Route::get('/forteacherwu', function (){


    return view('test');
});
Route::post('/forteacherwulike', 'TestController@foTeacher');



Route::get('/', 'IndexController@index');


//for auth
Route::post('/register', 'AuthController@register');
Route::post('/login', 'AuthController@login');
Route::get('/logout', 'AuthController@logout');
//end for login


//做验证
Route::get('/validator', 'ValidatorController@index');
Route::get('/captcha', 'ValidatorController@captcha');


//for upload


Route::get('/u/{id}', 'UserController@index');

Route::get('/u', function () {

    if (!empty(session('user'))) {

        return redirect('/u/' . session('user')->user_id);
    } else {

        abort(404, '请登录');
    }


});


Route::get('/getfile', 'AssistController@getFile');

Route::get('/dynamic', 'DynamicController@index');    //动态 dynamic

Route::get('/dynamic/{id}', 'DynamicController@show');    //动态 dynamic

Route::get('/comment', 'CommentController@index');

Route::post('/dynamic/{id}/addviews', 'DynamicController@addViews');    //添加浏览次数


Route::group(['middleware' => 'auth'], function () {

    Route::get('/setting/basic', ['as' => 'basicSetting', function () {

        return view('app.setting.basic')->with('data', session('user'));
    }]);

    Route::get('/setting', function () {

        return redirect(route('basicSetting'));
    });

    Route::post('/setting', 'SettingController@index');


    Route::post('/comment', 'CommentController@store');
    Route::post('/comment/{id}', 'CommentController@handleComment');

    Route::post('/dolike', 'LikeRecordController@likeToggle');


    Route::get('/write', function () {

        return view('app.dynamic.write');
    });


    Route::post('/upload', 'UploadController@index');

    Route::post('/dynamic', 'DynamicController@store');    //存储动态
    Route::post('/dynamic/{id}/delete', 'DynamicController@delete');    //操作动态(如删除)

    Route::post('/u/toggle_follow', 'UserRelationController@toggleFollow'); //关注 或取消关注

    Route::get('/notification','NotificationController@index');
    Route::get('/getnotification','NotificationController@getNotificationNum');
    Route::post('/delnotification','NotificationController@delNotificationNum');


    Route::get('/message','MessageController@index');

    Route::post('/sendmessage','MessageController@sendMessage');



});