<?php

use App\Http\Controllers\Api\V2\ExampleController;
use App\Http\Controllers\Financeiro\Planejamento\LdoController;
use App\Http\Controllers\Financeiro\Planejamento\LoaController;
use App\Http\Controllers\Financeiro\Planejamento\PpaController;
use App\Http\Controllers\HealthController;
use App\Http\Controllers\RedesimController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Modern API v2 Routes
Route::prefix('health')->group(function () {
    Route::get('/live', [HealthController::class, 'live'])->name('api.health.live');
    Route::get('/ready', [HealthController::class, 'ready'])->name('api.health.ready');
});

// Modern API v2 Routes
Route::prefix('v2')->group(function () {
    Route::get('/example', [ExampleController::class, 'index'])
        ->name('api.v2.example.index');
    
    Route::apiResource('examples', ExampleController::class)
        ->names('api.v2.examples');
    
    Route::get('/legacy-integration', [ExampleController::class, 'legacyIntegration'])
        ->name('api.v2.legacy.integration');
});

Route::group(['middleware' => ['redesimAuth']], function () {
    //redesim
    Route::group(['prefix' => 'redesim'], function () {
        Route::post('/companies', [RedesimController::class, 'index'])
            ->name('redesim.companies');
    });
});

Route::prefix('v1/financeiro/ppa')->group(function () {
    Route::get('/confronto', [PpaController::class, 'confrontarReceitaDespesa']);
    Route::get('/consolidacao-entidades', [PpaController::class, 'consolidarEntidades']);
    Route::get('/relatorios-gerenciais', [PpaController::class, 'relatorioGerencial']);
    Route::get('/relatorios-obrigatorios', [PpaController::class, 'relatorioObrigatorio']);
    Route::get('/compatibilizacao', [PpaController::class, 'compatibilizacao']);
    Route::get('/avaliacao-resultados', [PpaController::class, 'avaliacaoResultados']);
    Route::get('/indicadores-aplicacao', [PpaController::class, 'indicadoresAplicacao']);
    Route::post('/planos', [PpaController::class, 'storePlano']);
    Route::post('/planos/{planoId}/versoes', [PpaController::class, 'storeVersao']);
    Route::post('/versoes/{versaoId}/programas', [PpaController::class, 'storePrograma']);
    Route::post('/versoes/{versaoId}/receitas', [PpaController::class, 'storeReceita']);
    Route::post('/versoes/{versaoId}/receitas/rateio', [PpaController::class, 'ratearReceita']);
    Route::post('/versoes/{versaoId}/alteracoes-receita', [PpaController::class, 'storeAlteracaoReceita']);
    Route::get('/versoes/{versaoId}/alteracoes-receita', [PpaController::class, 'listAlteracoesReceita']);
    Route::post('/versoes/{versaoId}/transferencias-financeiras', [PpaController::class, 'storeTransferenciaFinanceira']);
    Route::get('/versoes/{versaoId}/transferencias-financeiras', [PpaController::class, 'listTransferenciasFinanceiras']);
    Route::post('/versoes/{versaoId}/metas', [PpaController::class, 'storeMeta']);
    Route::post('/versoes/{versaoId}/audiencias-publicas', [PpaController::class, 'storeAudienciaPublica']);
    Route::get('/versoes/{versaoId}/audiencias-publicas', [PpaController::class, 'listAudienciasPublicas']);
    Route::post('/audiencias-publicas/{audienciaId}/anexos', [PpaController::class, 'storeAudienciaPublicaAnexo']);
    Route::get('/audiencias-publicas/{audienciaId}/anexos', [PpaController::class, 'listAudienciaPublicaAnexos']);
    Route::get('/audiencias-publicas/anexos/{anexoId}/download', [PpaController::class, 'downloadAudienciaPublicaAnexo']);
    Route::post('/versoes/{versaoId}/importar', [PpaController::class, 'importarVersao']);
    Route::post('/versoes/{versaoId}/importar-loa', [PpaController::class, 'importarLoa']);
    Route::get('/versoes/{versaoId}/projecao', [PpaController::class, 'projetarVersao']);
    Route::get('/versoes/{versaoId}/orcamento', [PpaController::class, 'consultarOrcamento']);
    Route::post('/versoes/{versaoId}/publicar', [PpaController::class, 'publicarVersao']);
    Route::get('/versoes/{versaoId}', [PpaController::class, 'showVersao']);
});

Route::prefix('v1/financeiro/ldo')->group(function () {
    Route::get('/confronto', [LdoController::class, 'confrontarReceitaDespesa']);
    Route::get('/consolidacao-entidades', [LdoController::class, 'consolidarEntidades']);
    Route::get('/obras/demonstrativo', [LdoController::class, 'relatorioObrasConservacao']);
    Route::get('/relatorios-aplicacao', [LdoController::class, 'relatorioAplicacao']);
    Route::get('/relatorios-memoria-calculo', [LdoController::class, 'relatorioMemoriaCalculo']);
    Route::get('/indicadores-aplicacao', [LdoController::class, 'indicadoresAplicacao']);
    Route::get('/relatorios-gerenciais', [LdoController::class, 'relatorioGerencial']);
    Route::post('/planos', [LdoController::class, 'storePlano']);
    Route::post('/planos/{planoId}/versoes', [LdoController::class, 'storeVersao']);
    Route::post('/versoes/{versaoId}/programas', [LdoController::class, 'storePrograma']);
    Route::post('/versoes/{versaoId}/despesas', [LdoController::class, 'storeDespesa']);
    Route::post('/versoes/{versaoId}/obras', [LdoController::class, 'storeObra']);
    Route::post('/versoes/{versaoId}/memorias-calculo', [LdoController::class, 'storeMemoriaCalculo']);
    Route::patch('/versoes/{versaoId}/despesas/{despesaId}/metas', [LdoController::class, 'updateMetaDespesa']);
    Route::post('/versoes/{versaoId}/importar', [LdoController::class, 'importar']);
    Route::get('/versoes/{versaoId}', [LdoController::class, 'showVersao']);
    Route::post('/versoes/{versaoId}/alteracoes-receita', [LdoController::class, 'storeAlteracaoReceita']);
    Route::get('/versoes/{versaoId}/alteracoes-receita', [LdoController::class, 'listAlteracoesReceita']);
    Route::get('/versoes/{versaoId}/orcamento', [LdoController::class, 'consultarOrcamento']);
});

Route::prefix('v1/financeiro/loa')->group(function () {
    Route::post('/planos', [LoaController::class, 'storePlano']);
    Route::post('/planos/{planoId}/versoes', [LoaController::class, 'storeVersao']);
    Route::get('/versoes/{versaoId}', [LoaController::class, 'showVersao']);
    Route::post('/versoes/{versaoId}/disponibilizar-execucao', [LoaController::class, 'disponibilizarExecucao']);
    Route::post('/versoes/{versaoId}/implantar-orcamento', [LoaController::class, 'implantarOrcamento']);
    Route::post('/versoes/{versaoId}/codigos-reduzidos', [LoaController::class, 'storeCodigoReduzido']);
    Route::get('/versoes/{versaoId}/codigos-reduzidos', [LoaController::class, 'listCodigosReduzidos']);
    Route::post('/versoes/{versaoId}/solicitacoes-alteracao', [LoaController::class, 'storeSolicitacaoAlteracao']);
    Route::get('/versoes/{versaoId}/solicitacoes-alteracao', [LoaController::class, 'listSolicitacoesAlteracao']);
    Route::post('/versoes/{versaoId}/solicitacoes-alteracao/{solicitacaoId}/efetivar', [LoaController::class, 'efetivarSolicitacaoAlteracao']);
    Route::post('/versoes/{versaoId}/cronograma-desembolso', [LoaController::class, 'storeCronogramaDesembolso']);
    Route::get('/versoes/{versaoId}/cronograma-desembolso', [LoaController::class, 'listCronogramaDesembolso']);
    Route::get('/versoes/{versaoId}/cronograma-desembolso/relatorio', [LoaController::class, 'relatorioCronogramaDesembolso']);
    Route::post('/versoes/{versaoId}/metas-arrecadacao', [LoaController::class, 'storeMetaArrecadacao']);
    Route::get('/versoes/{versaoId}/metas-arrecadacao', [LoaController::class, 'listMetasArrecadacao']);
    Route::get('/versoes/{versaoId}/metas-arrecadacao/relatorio', [LoaController::class, 'relatorioMetasArrecadacao']);
    Route::get('/versoes/{versaoId}/decretos/suplementacao', [LoaController::class, 'decretoSuplementacao']);
    Route::get('/versoes/{versaoId}/relatorios-lei-4320', [LoaController::class, 'relatorioLei4320']);
    Route::post('/versoes/{versaoId}/cotas-despesa', [LoaController::class, 'storeCotasDespesa']);
    Route::get('/versoes/{versaoId}/cotas-despesa', [LoaController::class, 'listCotasDespesa']);
    Route::post('/versoes/{versaoId}/cotas-despesa/contingenciamento', [LoaController::class, 'contingenciarOrcamento']);
    Route::post('/versoes/{versaoId}/cotas-despesa/liberacao', [LoaController::class, 'liberarContingenciamento']);
    Route::post('/versoes/{versaoId}/cotas-despesa/redistribuicao', [LoaController::class, 'redistribuirCotasFechadas']);
    Route::post('/versoes/{versaoId}/compatibilizacao-planejamento', [LoaController::class, 'compatibilizarPlanejamento']);
    Route::post('/versoes/{versaoId}/projetos-conservacao', [LoaController::class, 'storeProjetoConservacao']);
    Route::get('/versoes/{versaoId}/projetos-conservacao', [LoaController::class, 'listProjetosConservacao']);
    Route::post('/versoes/{versaoId}/renuncias-receita', [LoaController::class, 'storeRenunciaReceita']);
    Route::get('/versoes/{versaoId}/renuncias-receita', [LoaController::class, 'listRenunciasReceita']);
    Route::get('/versoes/{versaoId}/renuncias-receita/relatorio', [LoaController::class, 'relatorioRenunciasReceita']);
    Route::post('/versoes/{versaoId}/receitas', [LoaController::class, 'storeReceita']);
    Route::post('/versoes/{versaoId}/receitas/rateio', [LoaController::class, 'ratearReceita']);
    Route::post('/versoes/{versaoId}/naturezas-receita', [LoaController::class, 'storeNaturezaReceita']);
    Route::get('/versoes/{versaoId}/naturezas-receita', [LoaController::class, 'listNaturezasReceita']);
    Route::post('/versoes/{versaoId}/alteracoes-receita', [LoaController::class, 'storeAlteracaoReceita']);
    Route::get('/versoes/{versaoId}/alteracoes-receita', [LoaController::class, 'listAlteracoesReceita']);
    Route::post('/versoes/{versaoId}/alteracoes-receita/lotes', [LoaController::class, 'storeLoteAlteracoesReceita']);
    Route::get('/versoes/{versaoId}/alteracoes-receita/lotes', [LoaController::class, 'listLotesAlteracoesReceita']);
    Route::post('/versoes/{versaoId}/alteracoes-despesa/lotes', [LoaController::class, 'storeLoteAlteracoesDespesa']);
    Route::get('/versoes/{versaoId}/alteracoes-despesa/lotes', [LoaController::class, 'listLotesAlteracoesDespesa']);
    Route::get('/versoes/{versaoId}/alteracoes-despesa/lotes/{loteId}/lancamentos', [LoaController::class, 'listLancamentosAlteracoesDespesa']);
    Route::get('/versoes/{versaoId}/creditos-adicionais/despesas', [LoaController::class, 'listCreditosAdicionaisDespesa']);
    Route::get('/versoes/{versaoId}/alteracoes-orcamentarias/relatorio', [LoaController::class, 'relatorioAlteracoesOrcamentarias']);
    Route::get('/versoes/{versaoId}/orcamento', [LoaController::class, 'consultarOrcamento']);
    Route::get('/versoes/{versaoId}/consistencia-planejamento', [LoaController::class, 'consistenciaPlanejamento']);
    Route::post('/versoes/{versaoId}/despesas', [LoaController::class, 'storeDespesa']);
    Route::get('/versoes/{versaoId}/despesas', [LoaController::class, 'listDespesas']);
    Route::post('/versoes/{versaoId}/importar', [LoaController::class, 'importar']);
});
