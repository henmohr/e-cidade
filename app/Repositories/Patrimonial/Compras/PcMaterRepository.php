<?php
namespace App\Repositories\Patrimonial\Compras;

use App\Models\PcMater;
use App\Repositories\Contracts\Patrimonial\Compras\PcMaterRepositoryInterface;
use Illuminate\Support\Facades\DB;

class PcMaterRepository implements PcMaterRepositoryInterface{
    private PcMater $model;

    public function __construct()
    {
        $this->model = new PcMater();
    }

    public function search(?string $search, int $anousu)
    {

        $result = $this->model
            ->select(
                'pc01_codmater as cod',
                DB::raw('pc01_descrmater || case when pc01_complmater <> \'\' then \' - \' || pc01_complmater else \'\' end as label')
            )
            ->join(
                'pcsubgrupo',
                'pcsubgrupo.pc04_codsubgrupo',
                '=',
                'pcmater.pc01_codsubgrupo'
            )
            ->join(
                'pcgrupo',
                'pcgrupo.pc03_codgrupo',
                '=',
                'pcsubgrupo.pc04_codgrupo'
            )
            ->join(
                'pctipo',
                'pctipo.pc05_codtipo',
                '=',
                'pcsubgrupo.pc04_codtipo'
            )
            ->join(
                'pcmaterele',
                'pcmaterele.pc07_codmater',
                '=',
                'pcmater.pc01_codmater'
            )
            ->join(
                'orcelemento',
                function($join) use ($anousu){
                    $join->on(
                        'orcelemento.o56_codele',
                        '=',
                        'pcmaterele.pc07_codele'
                    )
                    ->where('orcelemento.o56_anousu', '=', $anousu);
                }
            )
            ->leftJoin(
                'db_usuarios',
                'db_usuarios.id_usuario',
                '=',
                'pcmater.pc01_id_usuario'
            )
            ->whereRaw('pc01_descrmater ILIKE ?', [$search.'%'])
            ->whereNotNull('pc07_codele')
            ->where('pc01_ativo', '=', 'false')
            ->get();

        return $result->toArray();
    }

    public function salvarPcmater(array $pcMater)
    {
        return $this->model->insert($pcMater);
    }

    public function getNextval(): int
    {
        return $this->model->getNextval();
    }

    public function getPcmaterAnterior($pc01_codmaterant)
    {
        $sql = 'select * from pcmater where pc01_codmaterant = ? and pc01_instit = ?';
        return DB::select($sql, [
            (int) $pc01_codmaterant,
            (int) db_getsession('DB_instit')
        ]);
    }

}
