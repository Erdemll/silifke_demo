<?php

use App\Http\Controllers\AnaSayfaController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Kullanici\KayitController;
use App\Http\Controllers\Kullanici\OturumController;
use App\Http\Controllers\Magaza\SepetController;
use App\Http\Controllers\Magaza\SiparislerimController;
use App\Http\Controllers\Magaza\UrunDetayController;
use App\Http\Controllers\Magaza\YorumController;
use App\Http\Controllers\SiparisController;
use App\Http\Controllers\UrunController;
use Illuminate\Support\Facades\Route;

Route::get('/', AnaSayfaController::class)->name('home');
Route::get('urunler/{urun}', UrunDetayController::class)->name('magaza.urunler.show');
Route::get('sepet', [SepetController::class, 'index'])->name('magaza.sepet.index');
Route::post('urunler/{urun}/sepete-ekle', [SepetController::class, 'store'])->name('magaza.sepet.store');
Route::post('urunler/{urun}/yorumlar', [YorumController::class, 'store'])
    ->middleware('auth:musteri')
    ->name('magaza.yorumlar.store');

Route::middleware('guest:musteri')->group(function () {
    Route::get('giris', [OturumController::class, 'create'])->name('kullanici.giris');
    Route::post('giris', [OturumController::class, 'store'])
        ->middleware('throttle:musteri-giris')
        ->name('kullanici.giris.store');
    Route::get('kayit', [KayitController::class, 'create'])->name('kullanici.kayit');
    Route::post('kayit', [KayitController::class, 'store'])->name('kullanici.kayit.store');
});

Route::post('cikis', [OturumController::class, 'destroy'])
    ->middleware('auth:musteri')
    ->name('kullanici.cikis');

Route::post('sepet/satin-al', [SepetController::class, 'satinAl'])
    ->middleware('auth:musteri')
    ->name('magaza.sepet.satin_al');

Route::get('siparislerim', [SiparislerimController::class, 'index'])
    ->middleware('auth:musteri')
    ->name('magaza.siparislerim.index');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', DashboardController::class)->name('dashboard');
    Route::resource('urunler', UrunController::class)
        ->only(['index', 'store', 'update', 'destroy'])
        ->parameters(['urunler' => 'urun']);
    Route::get('siparisler', [SiparisController::class, 'index'])->name('siparisler.index');
});

require __DIR__.'/settings.php';
