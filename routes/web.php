<?php

Route::get('/', function () {
    return view('dashboard/home');
});

Route::group([], function () {
    Route::get('/home', 'DashboardController@home');
    Route::get('/backlinks', 'DashboardController@seo_backlinks')->name('backlinks');
    Route::get('/backlinks/{id}', 'backlinkController@backlink_details')->name('backlink_details')->middleware('auth');
    Route::get('/rankings', 'DashboardController@seo_rankings')->name('rankings');
    Route::get('/rankings/{id}', 'rankingsController@ranking_details')->name('ranking_details')->middleware('auth');
    Route::get('/audit', 'DashboardController@seo_audit')->name('audit');
    Route::get('/audit/{id}', 'analysisController@seo_audit_details')->name('seo_audit_details')->middleware('auth');
    Route::get('/analysis', 'DashboardController@seo_analysis')->name('analysis');
    Route::get('/analysis/{id}', 'analysisController@seo_analysis_details')->name('analysis_details')->middleware('auth');
    Route::get('/destroy/{id}','DashboardController@destroy');
    Route::get('/subscription','DashboardController@subscription')->name('subscription');
    Route::get('/pdf_create_rankings','rankingsController@pdf_create_rankings')->name('pdf_create_rankings');
    Route::get('/download_seo_report/{id}', 'analysisController@download_seo_report')->name('download_seo_report');
    Route::post('/delete_seo_report/{id}', 'analysisController@delete_seo_report')->name('delete_seo_report');
    Route::post('/delete_backlink_report/{id}', 'backlinkController@delete_backlink_report')->name('delete_backlink_report');
    Route::post('/delete_ranking_report/{id}', 'rankingsController@delete_ranking_report')->name('delete_ranking_report');
    Route::get('/download_audit_report/{id}', 'analysisController@download_audit_report')->name('download_audit_report');
    Route::post('/delete_audit_report/{id}', 'analysisController@delete_audit_report')->name('delete_audit_report');
    Route::post('/email_seo_report', 'analysisController@email_seo_report')->name('email_seo_report');
    Route::post('/email_audit_report', 'analysisController@email_audit_report')->name('email_audit_report');
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
Route::post('image-upload', 'DashboardController@imageUploadPost');


Route::get('logout', 'LoginController@logout');


Route::group(['middleware' => ['web']], function () {
    // your routes here
    
Route::get('login/', 'LoginController@redirectToGoogle')->name('login');
Route::get('login/callback', 'LoginController@handleGoogleCallback');
});