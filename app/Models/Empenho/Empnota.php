<?php

namespace App\Models\Empenho;

use App\Models\LegacyModel;
use App\Services\Financeiro\ExecucaoOrcamentaria\CicloDespesaService;
use Throwable;

class Empnota extends LegacyModel
{
    protected static function booted(): void
    {
        static::saving(static function (Empnota $model): void {
            if (!self::shouldEnforceCycleGuard()) {
                return;
            }

            $numeroEmpenho = (int) ($model->e69_numemp ?? 0);
            if ($numeroEmpenho <= 0) {
                return;
            }

            /** @var CicloDespesaService $service */
            $service = app(CicloDespesaService::class);
            $service->assertPodeLiquidar($numeroEmpenho);
        });
    }

    private static function shouldEnforceCycleGuard(): bool
    {
        try {
            return function_exists('config')
                && (bool) config('execucao_orcamentaria.enforce_model_guards', true);
        } catch (Throwable $exception) {
            return false;
        }
    }

    /**
     * @var string
     */
    protected $table = 'empenho.empnota';

    /**
     * @var string
     */
    protected $primaryKey = 'e69_codnota';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    public $timestamps = false;

    /**
     * @var array
     */
    protected $fillable = [
        'e69_codnota',
        'e69_numero',
        'e69_numemp',
        'e69_id_usuario',
        'e69_dtnota',
        'e69_dtrecebe',
        'e69_anousu',
        'e69_tipodocumentosfiscal',
        'e69_dtservidor',
        'e69_dtinclusao',
        'e69_notafiscaleletronica',
        'e69_chaveacesso',
        'e69_nfserie',
        'e69_cgmemitente',
        'e69_id_documento_assinado',
        'e69_node_id_libresing',
    ];
}
