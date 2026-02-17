<?php
namespace App\Services\SicomAcodBasico;

use App\Models\Sicom\SicomAcodBasico;
use App\Repositories\Patrimonial\Licitacao\Sicom\HistoricoCgmEnvioSicomRepository;
use App\Repositories\Patrimonial\Licitacao\Sicom\SicomAcodBasicoRepository;
use App\Repositories\Patrimonial\Materiais\HistoricoMaterialRepository;
use App\Repositories\Patrimonial\Protocolo\CgmRepository;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Support\Facades\Log;

class SalvarGeracaoArquivosService{
    private SicomAcodBasicoRepository $sicomAcodBasicoRepository;
    private HistoricoMaterialRepository $historicoMaterialRepository;
    private CgmRepository $cgmRepository;
    private HistoricoCgmEnvioSicomRepository $historicoCgmEnvioSicomRepository;

    public function __construct()
    {
      $this->sicomAcodBasicoRepository = new SicomAcodBasicoRepository();
      $this->historicoMaterialRepository = new HistoricoMaterialRepository();
      $this->cgmRepository = new CgmRepository();
      $this->historicoCgmEnvioSicomRepository = new HistoricoCgmEnvioSicomRepository();
    }

    public function execute(object $data){
      if(empty($data->itens)){
        return [
          'status' => 500,
          'message' => 'Por favor informe o arquivos a serem gerados'
        ];
      }

      DB::beginTransaction();
      try{
        $remessa = $data->l228_seqremessa ?? null;
        if(empty($data->l228_seqremessa)){
          $remessa = $this->sicomAcodBasicoRepository->getNextRemessa($data->instit)->l228_seqremessa;
        }

        $aDataSicom = new SicomAcodBasico([
          'l228_sequencial'      => $this->sicomAcodBasicoRepository->getNextVal(),
          'l228_codremessa'      => null,
          'l228_seqremessa'      => $remessa,
          'l228_usuario'         => !empty($data->id_usuario)? $data->id_usuario : null,
          'l228_dataenvio'       => !empty($data->l228_dataenvio)? $data->l228_dataenvio : null,
          'l228_datainicial'     => !empty($data->l228_datainicial)? $data->l228_datainicial : null,
          'l228_datafim'         => !empty($data->l228_datafim)? $data->l228_datafim : null,
          'l228_instit'          => $data->instit
        ]);

        $oSicom = $this->sicomAcodBasicoRepository->save($aDataSicom);

        if(in_array('ITEM', $data->itens)){
          $this->historicoMaterialRepository->updateByConditions($data->instit, $data->l228_datainicial, $data->l228_datafim);
        }
        
        if(in_array('PESSOA', $data->itens)){
          $this->historicoCgmEnvioSicomRepository->saveInBath($data->instit, $remessa);
          // $this->cgmRepository->updateByConditions($data->instit, $data->l228_datainicial, $data->l228_datafim);
        }
        
        DB::commit();
        return [
          'status' => 200,
          'message' => 'Dados salvos com sucesso',
          'data' => []
        ];
      } catch (\Illuminate\Database\QueryException $e) {
        DB::rollBack();
        Log::error('Erro ao salvar geração de arquivos SICOM', [
            'message' => $e->getMessage(),
            'sql' => $e->getSql(),
            'bindings' => $e->getBindings(),
        ]);
        return [
            'status' => 500,
            'message' => 'Erro ao salvar dados da geração de arquivos',
            'data' => []
        ];
      }catch(\Throwable $e){
        DB::rollBack();
        return [
            'status' => 500,
            'message' => $e->getMessage(),
            'data' => []
        ];
      }
        
    }
}
