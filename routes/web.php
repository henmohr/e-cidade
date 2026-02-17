<?php

use App\Http\Controllers\AuditController;
use App\Http\Controllers\Auth\ExternalIdentityController;
use App\Http\Controllers\Auth\MfaController;
use App\Http\Controllers\Auth\SessionController;
use App\Http\Controllers\BackupAccessController;
use App\Http\Controllers\IptuFotosController;
use App\Http\Controllers\RedesimController;
use App\Http\Controllers\Modules\Patrimonial\Licitacoes\Procedimentos\JulgamentoPorLance\FaseDeLances\FaseDeLancesController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['legacySession', 'authEcidadeUser'], 'prefix' => 'web/mfa'], function () {
    Route::get('/challenge', [MfaController::class, 'show'])->name('mfa.challenge');
    Route::post('/verify', [MfaController::class, 'verify'])->name('mfa.verify');
    Route::post('/resend', [MfaController::class, 'resend'])->name('mfa.resend');
});

Route::prefix('web/idp')->group(function () {
    Route::get('/providers', [ExternalIdentityController::class, 'providers'])->name('idp.providers');
    Route::post('/callback', [ExternalIdentityController::class, 'callback'])->name('idp.callback');
});

Route::group(['middleware' => ['legacySession', 'authEcidadeUser', 'auth.basic', 'webAuditTrail'], 'prefix' => 'web'], function () {
    Route::get('/welcome', function () {
        return view('modelo');
    })->name('welcome');
    Route::post('iptufoto/upload', [IptuFotosController::class, 'upload'])->name('iptufotos-upload');
    Route::get('iptufoto/list/{matric}', [IptuFotosController::class, 'list'])->name('iptufotos-list');
    Route::post('iptufoto/update', [IptuFotosController::class, 'update'])->name('iptufotos-update');
    Route::delete('iptufoto/delete/{id}/{matric}', [IptuFotosController::class, 'delete'])->name('iptufotos-delete');
    Route::get('iptufoto/show/{id}', [IptuFotosController::class, 'show'])->name('iptufotos-show');

    //audit
    Route::get('/audits', [AuditController::class, 'index'])->name('audits.index');

    //redesim
    Route::group(['prefix' => 'redesim'], function () {
        Route::post('/companiesReport', [RedesimController::class, 'companiesReport'])
            ->name('redesim.companies.report');
        Route::get('/alvara', [RedesimController::class, 'alvara'])
            ->name('redesim.alvara');
        Route::post('/alvara/create', [RedesimController::class, 'create'])
            ->name('redesim.alvara.create');
    });
    require base_path('routes/modules/patrimonial/patrimonial.php');
    require base_path('routes/modules/configuracao/configuracao.php');

    Route::prefix('datagrid')->group(function () {
        Route::get('/get-liclicita', [FaseDeLancesController::class, 'getLiclicita'])->name('datagrid.getLiclicita');
        Route::get('/get-liclicita-item', [FaseDeLancesController::class, 'getLiclicitaItens'])->name('datagrid.getLiclicitaItens');
    });

    Route::prefix('sessions')->group(function () {
        Route::get('/', [SessionController::class, 'index'])->name('sessions.index');
        Route::post('/revoke', [SessionController::class, 'revoke'])->name('sessions.revoke');
    });

    Route::prefix('backup')->middleware(['requireA3Certificate'])->group(function () {
        Route::get('/', [BackupAccessController::class, 'index'])->name('backup.index');
        Route::post('/link', [BackupAccessController::class, 'generateDownloadLink'])->name('backup.link');
        Route::get('/download/{tier}/{file}', [BackupAccessController::class, 'download'])
            ->middleware(['signed'])
            ->name('backup.download');
    });
});

// Test route for modern/legacy routing
Route::get('/test-modern', function() {
    return response()->json([
        'status' => 'working',
        'type' => 'modern',
        'message' => 'Sistema de roteamento moderno/legado funcionando!'
    ]);
});

Route::fallback(function () {
    abort(404);
});
