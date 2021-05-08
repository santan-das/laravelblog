<?php

 if (isset($_SERVER['HTTP_ORIGIN'])) {
        // Decide if the origin in $_SERVER['HTTP_ORIGIN'] is one
        // you want to allow, and if so:
        header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Max-Age: 86400');    // cache for 1 day
    }

    // Access-Control headers are received during OPTIONS requests
    if(isset($_SERVER['REQUEST_METHOD'])){
    if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {

        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
            // may also be using PUT, PATCH, HEAD etc
            header("Access-Control-Allow-Methods: GET, POST, OPTIONS");         

        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
            header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

        exit(0);
    }
}

    //echo "You have CORS!";
// array holding allowed Origin domains
/*
$allowedOrigins = array(
  '(http(s)://)?(www\.)?my\-domain\.com'
);
 
if (isset($_SERVER['HTTP_ORIGIN']) && $_SERVER['HTTP_ORIGIN'] != '') {
  foreach ($allowedOrigins as $allowedOrigin) {
    if (preg_match('#' . $allowedOrigin . '#', $_SERVER['HTTP_ORIGIN'])) {
      header('Access-Control-Allow-Origin: ' . $_SERVER['HTTP_ORIGIN']);
      header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
      header('Access-Control-Max-Age: 1000');
      header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
      break;
    }
  }
}*/



 //if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
 /*   if (isset($_SERVER['REQUEST_METHOD']) === 'OPTIONS') {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: POST, GET, DELETE, PUT, PATCH, OPTIONS');
        header('Access-Control-Allow-Headers: token, Content-Type');
        header('Access-Control-Max-Age: 1728000');
        header('Content-Length: 0');
        header('Content-Type: text/plain');
        die();
    }

    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/json');

    $ret = [
        'result' => 'OK',
    ];*/
 /*   print json_encode($ret);*/

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
    return view('welcome');
});

Route::get('api/getusers','CRUDController@getAllUsers');

Route::post('api/getalldata','CRUDController@getTotalUsers');

Route::post('api/getsingleusers','CRUDController@singleUser');

Route::post('api/submit-data','CRUDController@submitData');
Route::post('api/update-data','CRUDController@updateRecord');

Route::get('api/pdfview/{orderId}/{customer_id}',array('as'=>'pdfview','uses'=>'CRUDController@getViewdata'));

Route::post('api/testdata', 'CRUDController@testingcode');
Route::post('api/pincodes-data', 'CRUDController@pincodeData');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
Route::get('delete-seller/{id}', 'HomeController@deleteSeller');
