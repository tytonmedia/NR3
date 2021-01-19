<?php

Route::get('/', function () {
    return view('dashboard/home');
});

Route::group([], function () {
    Route::get('/home', 'DashboardController@home');
    Route::get('/backlinks', 'DashboardController@seo_backlinks')->name('backlinks');
    Route::get('/rankings', 'DashboardController@seo_rankings')->name('rankings');
    Route::get('/audit', 'DashboardController@seo_audit')->name('audit');
    Route::get('/analysis', 'DashboardController@seo_analysis')->name('analysis');
    Route::get('/destroy/{id}','DashboardController@destroy');
    Route::get('/subscription','DashboardController@subscription')->name('subscription');
    Route::get('/pdf_create_rankings','rankingsController@pdf_create_rankings')->name('pdf_create_rankings');


});

Route::group(['middleware' => ['auth']],function(){
    Route::get('/account', 'DashboardController@account');
    Route::get('/cancel','DashboardController@cancelSubscription')->name('cancel');
    Route::get('payment/{id}', 'PaymentController@payment')->name('payment');
    Route::post('/stripe/{id}', 'PaymentController@stripePost')->name('stripe.post');
    Route::get('/pricing', 'DashboardController@pricing');
});

Route::post('/seo', 'analysisController@get_seo_result')->name('seo');
Route::post('/seo_audit', 'analysisController@get_audit_result')->name('seo_audit');
Route::post('/seo_backlinks', 'backlinkController@get_backlink_results')->name('seo_backlinks');
Route::post('/seo_rankings', 'rankingsController@get_rankings_results')->name('seo_rankings');
Route::post('/add_website', 'projectController@add_website')->name('add_website');


Route::get('logout', 'LoginController@logout');


Route::group(['middleware' => ['web']], function () {
    // your routes here
    
Route::get('login/', 'LoginController@redirectToGoogle')->name('login');
Route::get('login/callback', 'LoginController@handleGoogleCallback');
});