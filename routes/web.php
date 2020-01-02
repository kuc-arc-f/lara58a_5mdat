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

Route::get('/', function () {
    return view('welcome');
});
//
//Route::get('/tasks/data1', 'TasksController@data1');
Route::resource('tasks', 'TasksController');
//
Route::get('/books/test3', 'BooksController@test3')->name('books.test3');
Route::post('/books/test2', 'BooksController@test2')->name('books.test2');;
Route::get('/books/test1', 'BooksController@test1')->name('books.test1');;
Route::resource('books', 'BooksController');
//
Route::resource('members', 'MembersController');
Route::resource('depts', 'DeptsController');
Route::resource('todos', 'TodosController');
//
Route::resource('plans', 'PlansController');
//
Route::get('/mdats/chart', 'MdatsController@chart')->name('mdats.chart');
Route::resource('mdats', 'MdatsController');
//
Auth::routes();
//
Route::get('/home', 'HomeController@index')->name('home');

