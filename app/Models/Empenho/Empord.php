<?php

namespace App\Models\Empenho;

use App\Models\LegacyModel;
use App\Services\Financeiro\ExecucaoOrcamentaria\CicloDespesaService;
use Throwable;

class Empord extends LegacyModel
{
    protected static function booted(): void
    {
        static::saving(static function (Empord $model): void {
            if (!self::shouldEnforceCycleGuard()) {
                return;
            }

            $codigoOrdem = (int) ($model->e82_codord ?? 0);
            if ($codigoOrdem <= 0) {
                return;
            }

            /** @var CicloDespesaService $service */
            $service = app(CicloDespesaService::class);
            $service->assertPodeRegistrarPagamentoPorOrdem($codigoOrdem);
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
    protected $table = 'empenho.empord';

    /**
     * @var string
     */
    protected $primaryKey = 'e82_codmov';

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
        'e82_codmov',
        'e82_codord',
        'e82_id_documento_assinado',
        'e82_node_id_libresing',
    ];
}
