<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SeeProfileController;
use App\Http\Controllers\VerifyUserController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
# cuando se carga la vista principal, se carga welcome
Route::get('/', function () {
    return view('welcome');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/searchusers', [UserController::class, 'searchUsers'])->name('search.users');
    Route::get('/showuser/{name}', [UserController::class, 'show'])->name('user.show');
    Route::post('/showuser/{name}', [UserController::class, 'followuser'])->name('user.follow');

    // Dashboard Routes
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/store-post', [DashboardController::class, 'storePost'])->name('store.post');
    Route::get('/show-posts', [DashboardController::class, 'showPosts'])->name('show.posts');
    Route::post('/like-post', [DashboardController::class, 'likePost'])->name('like.post');
    Route::post('/dislike-post', [DashboardController::class, 'dislikePost'])->name('dislike.post');
    //agregacion para borrar post
    Route::delete('/publications/{id}', [DashboardController::class, 'destroy'])->name('publications.destroy');
    
    // SeeProfile Route
    Route::get('/seeprofile/{id}', [SeeProfileController::class, 'show'])->name('seeprofile');

    //Route::get('/banking-net', [VerifyUserController::class, 'verifyUserCertificate'])->name('banking.net');
    Route::get('/verify-user', [VerifyUserController::class, 'displayView'])->name('verify.user');

    Route::post('/verify-user', [VerifyUserController::class, 'verifyPassword'])->name('verify.user.submit');

    Route::get('/banking-net', function () {
        return view('banking_net');
    })->name('bankingnet');
});

require __DIR__.'/auth.php';
