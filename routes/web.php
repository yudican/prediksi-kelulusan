<?php

use App\Http\Controllers\AuthController;
use App\Http\Livewire\CrudGenerator;
use App\Http\Livewire\Dashboard;
use App\Http\Livewire\HasilPerhitungan;
use App\Http\Livewire\Master\DataMahasiswaController;
use App\Http\Livewire\Master\DataProdiController;
use App\Http\Livewire\Master\DataSetController;
use App\Http\Livewire\PerhitunganController;
use App\Http\Livewire\Settings\Menu;
use App\Http\Livewire\UserManagement\Permission;
use App\Http\Livewire\UserManagement\PermissionRole;
use App\Http\Livewire\UserManagement\Role;
use App\Http\Livewire\UserManagement\User;
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
    return redirect('login');
});


Route::post('login', [AuthController::class, 'login'])->name('admin.login');
Route::group(['middleware' => ['auth:sanctum', 'verified', 'user.authorization']], function () {
    // Crud Generator Route
    Route::get('/crud-generator', CrudGenerator::class)->name('crud.generator');

    // user management
    Route::get('/permission', Permission::class)->name('permission');
    Route::get('/permission-role/{role_id}', PermissionRole::class)->name('permission.role');
    Route::get('/role', Role::class)->name('role');
    Route::get('/user', User::class)->name('user');
    Route::get('/menu', Menu::class)->name('menu');

    // App Route
    Route::get('/dashboard', Dashboard::class)->name('dashboard');

    // Master data
    Route::get('/data-mahasiswa', DataMahasiswaController::class)->name('data.mahasiswa');
    Route::get('/data-prodi', DataProdiController::class)->name('data.prodi');
    Route::get('/data-latih', DataSetController::class)->name('data.set');
    Route::get('/perhitungan', PerhitunganController::class)->name('perhitungan');
    Route::get('/hasil-perhitungan', HasilPerhitungan::class)->name('hasil.perhitungan');
});
