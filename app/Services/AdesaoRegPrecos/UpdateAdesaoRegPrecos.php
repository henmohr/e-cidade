<?php

namespace App\Services\AdesaoRegPrecos;

use App\Models\EmpEmpenho;
use App\Models\Empenho\EmpAutoriza;
use App\Models\Sicom\AdesaoRegPrecos;
use App\Repositories\Configuracao\Empenho\EmpAutorizaRepository;
use App\Repositories\Configuracao\Empenho\EmpempautRepository;
use App\Repositories\Contabilidade\CondataconfRepository;
use App\Repositories\Financeiro\EmpEmpenhoRepository;
use App\Repositories\Patrimonial\AcordoPosicaoRepository;
use App\Repositories\Patrimonial\AcordoRepository;
use App\Repositories\Patrimonial\Compras\PcprocRepository;
use App\Repositories\Sicom\AdesaoRegPrecosRepository;
use App\Repositories\Sicom\ItensRegPrecoRepository;
use App\Services\Financeiro\ExecucaoOrcamentaria\CicloDespesaService;
use Illuminate\Database\Capsule\Manager as DB;

class UpdateAdesaoRegPrecos {
  private AdesaoRegPrecosRepository $adesaoRegPrecosRepository;
  private PcprocRepository $pcprocRepository;
  private CondataconfRepository $condataconfRepository;
  private AcordoRepository $acordoRepository;
  private AcordoPosicaoRepository $acordoPosicaoRepository;
  private EmpAutorizaRepository $empAutorizaRepository;
  private EmpEmpenhoRepository $empEmpenhoRepository;
  private EmpempautRepository $empempautRepository;
  private ItensRegPrecoRepository $itensRegPrecoRepository;
  private CicloDespesaService $cicloDespesaService;

  public function __construct()
  {
    $this->adesaoRegPrecosRepository = new AdesaoRegPrecosRepository();
    $this->pcprocRepository = new PcprocRepository();
    $this->condataconfRepository = new CondataconfRepository();
    $this->acordoRepository = new AcordoRepository();
    $this->acordoPosicaoRepository = new AcordoPosicaoRepository();
    $this->empAutorizaRepository = new EmpAutorizaRepository();
    $this->empEmpenhoRepository = new EmpEmpenhoRepository();
    $this->empempautRepository = new EmpempautRepository();
    $this->itensRegPrecoRepository = new ItensRegPrecoRepository();
    $this->cicloDespesaService = new CicloDespesaService();
  }

  public function execute(object $data){
    $oAdesao = $this->adesaoRegPrecosRepository->getAdesaoRegPrecoByCodigo($data->si06_sequencial);
    
    if($oAdesao->si06_statusenviosicom == 5){
      throw new \Exception("Não é possível alterar a adesão, pois ela já foi enviada ao SICOM.");
    }

    $oAcordo = $this->acordoRepository->getAcordosByAdesao($oAdesao->si06_sequencial);
    if(!$oAcordo->isEmpty()){
      throw new \Exception("A alteração não pode ser realizada, pois esta adesão já está vinculada a um acordo");
    }

    $sDataAta = date('Y-m-d', strtotime($data->si06_dataata));
    $sDataAbertura = date('Y-m-d', strtotime($data->si06_dataabertura));

    if($sDataAta > $sDataAbertura){
      throw new \Exception('Data da Ata é maior que a Data de Abertura!');
    }

    $oProcessoCompra = $this->pcprocRepository->getByCod($data->si06_processocompra);
    if(empty($oProcessoCompra)){
      throw new \Exception('Processo de compra não encontrado');
    }

    if($oProcessoCompra->pc80_data > $sDataAbertura){
      throw new \Exception('Data da Cotação é maior que a Data de Abertura!');
    }

    // if(!empty($data->si06_dataadesao)){
    //   $aDataEnceramento = $this->condataconfRepository->getEncerramentoPatrimonial($data->anousu, $data->instit);
    //   $dataEncerramentoPartrimonial = \DateTime::createFromFormat('Y-m-d', $aDataEnceramento->c99_datapat);
    //   if($dataEncerramentoPartrimonial >= $data->si06_dataadesao){
    //       throw new \Exception("O período já foi encerrado para envio do SICOM. Verifique os dados do lançamento e entre em contato com o suporte");
    //   }
    // }

    $aData = new AdesaoRegPrecos([
      'si06_sequencial'             => $data->si06_sequencial,
      'si06_orgaogerenciador'       => !empty($data->si06_orgaogerenciador)? $data->si06_orgaogerenciador : null,
      'si06_modalidade'             => !empty($data->si06_modalidade)? $data->si06_modalidade : null,
      'si06_numeroprc'              => !empty($data->si06_numeroprc)? $data->si06_numeroprc : null,
      'si06_numlicitacao'           => !empty($data->si06_numlicitacao)? $data->si06_numlicitacao : null,
      'si06_dataadesao'             => !empty($data->si06_dataadesao)? date('Y-m-d', strtotime($data->si06_dataadesao)) : null,
      'si06_dataata'                => !empty($data->si06_dataata)? date('Y-m-d', strtotime($data->si06_dataata)) : null,
      'si06_datavalidade'           => !empty($data->si06_datavalidade)? date('Y-m-d', strtotime($data->si06_datavalidade)) : null,
      'si06_publicacaoaviso'        => !empty($data->si06_publicacaoaviso)? date('Y-m-d', strtotime($data->si06_publicacaoaviso)) : null,
      'si06_objetoadesao'           => !empty($data->si06_objetoadesao)? $data->si06_objetoadesao : '',
      'si06_orgarparticipante'      => !empty($data->si06_orgarparticipante)? $data->si06_orgarparticipante : 2,
      'si06_cgm'                    => !empty($data->si06_cgm)? $data->si06_cgm : null,
      'si06_descontotabela'         => null,
      'si06_numeroadm'              => !empty($data->si06_numeroadm)? $data->si06_numeroadm : null,
      'si06_dataabertura'           => !empty($data->si06_dataabertura)? date('Y-m-d', strtotime($data->si06_dataabertura)) : null,
      'si06_processocompra'         => !empty($data->si06_processocompra)? $data->si06_processocompra : null,
      'si06_fornecedor'             => null,
      'si06_tipodocumento'          => null,
      'si06_numerodocumento'        => null,
      'si06_processoporlote'        => !empty($data->si06_processoporlote)? $data->si06_processoporlote : null,
      'si06_instit'                 => !empty($data->instit)? $data->instit : null,
      'si06_anoproc'                => !empty($data->si06_anoproc)? $data->si06_anoproc : null,
      'si06_edital'                 => !empty($data->si06_edital)? $data->si06_edital : null,
      'si06_cadinicial'             => null,
      'si06_exercicioedital'        => null,
      'si06_anocadastro'            => $data->anousu,
      'si06_leidalicitacao'         => !empty($data->si06_leidalicitacao)? $data->si06_leidalicitacao : null,
      'si06_anomodadm'              => !empty($data->si06_anomodadm)? $data->si06_anomodadm : null,
      'si06_nummodadm'              => !empty($data->si06_nummodadm)? $data->si06_nummodadm : null,
      'si06_departamento'           => !empty($data->si06_departamento)? $data->si06_departamento : null,
      'si06_codunidadesubant'       => null,
      'si06_regimecontratacao'      => !empty($data->si06_regimecontratacao)? $data->si06_regimecontratacao : null,
      'si06_criterioadjudicacao'    => !empty($data->si06_criterioadjudicacao)? $data->si06_criterioadjudicacao : null,
      'si06_descrcriterioutilizado' => !empty($data->si06_descrcriterioutilizado)? $data->si06_descrcriterioutilizado : null,
      'si06_statusenviosicom'       => null
    ]);

    if($data->anousu >= 2020){
      $aData->si06_cadinicial = 1;
      $aData->si06_exercicioedital = $data->anousu;
    }

    $oAdesaoValidacao = $this->adesaoRegPrecosRepository->validaAdesao(
      $aData->si06_sequencial,
      $aData->si06_numeroadm,
      $aData->si06_anomodadm
    );

    if(!empty($oAdesaoValidacao)){
      throw new \Exception('Erro, o número do processo de adesão informado já está sendo utilizado no exercício de ' . $data->si06_anomodadm);
    }

    DB::beginTransaction();
    try{
      $oAdesaoPreco = $this->adesaoRegPrecosRepository->update(
        $data->si06_sequencial,
        $aData->toArray()
      );

      $aAcordos = $this->acordoRepository->getAcordosByAdesao($oAdesaoPreco->si06_sequencial);
      if(!$aAcordos->isEmpty()){
        foreach($aAcordos as $acordo){
          $acordoPosicoes = $this->acordoPosicaoRepository->getAcordoPosicoes($acordo['ac16_sequencial']);
          if($acordoPosicoes->isEmpty()){
            continue;
          }

          foreach($acordoPosicoes as $posicao){
            $empAutoriza = new EmpAutoriza([
              'e54_autori' => $posicao->e54_autori,
              'e54_nummodalidade' => $oAdesaoPreco->si06_nummodadm,
              'e54_numerl' => $oAdesaoPreco->si06_numeroadm . "/" . $oAdesaoPreco->si06_anomodadm,
            ]);

            $this->empAutorizaRepository->update(
              $posicao->e54_autori,
              $empAutoriza->toArray()
            );

            if(!empty($posicao->e60_numemp)){
              $this->cicloDespesaService->assertPodeLiquidar((int) $posicao->e60_numemp);

              $aEmpEmpenho = new EmpEmpenho([
                'e60_numerol' => $oAdesaoPreco->si06_numeroadm . "/" . $oAdesaoPreco->si06_anomodadm,
                'e60_numemp' => $posicao->e60_numemp
              ]);
              
              $this->empEmpenhoRepository->update(
                $posicao->e60_numemp,
                $aEmpEmpenho->toArray()
              );
            }

          }
        }
      } else {
        $aEmpPauta = $this->empempautRepository->getEmpPautByAdesaoPreco($oAdesaoPreco->si06_sequencial, $oAdesaoPreco->si06_anoproc);
        if(!$aEmpPauta->isEmpty()){
          foreach($aEmpPauta as $pauta){
            $empAutoriza = new EmpAutoriza([
              'e54_autori' => $pauta->e61_autori,
              'e54_nummodalidade' => $oAdesaoPreco->si06_nummodadm,
              'e54_numerl' => $oAdesaoPreco->si06_numeroadm . "/" . $oAdesaoPreco->si06_anomodadm,
            ]);

            $this->empAutorizaRepository->update(
              $pauta->e61_autori,
              $empAutoriza->toArray()
            );

            if(!empty($pauta->e61_numemp)){
              $this->cicloDespesaService->assertPodeLiquidar((int) $pauta->e61_numemp);

              $aEmpEmpenho = new EmpEmpenho([
                'e60_numerol' => $oAdesaoPreco->si06_numeroadm . "/" . $oAdesaoPreco->si06_anomodadm,
                'e60_numemp' => $pauta->e61_numemp
              ]);
              
              $this->empEmpenhoRepository->update(
                $pauta->e61_numemp,
                $aEmpEmpenho->toArray()
              );
            }
          }
        }
      }

      if($oAdesaoPreco->si06_processoporlote == 2){
        $itensReg = $this->itensRegPrecoRepository->getItensReg($oAdesaoPreco->si06_sequencial);
        if(!$itensReg->isEmpty()){
          foreach($itensReg as $item){
            $item->si07_descricaolote = '';
            $item->si07_numerolote = null;
            $item->si07_sequencialadesao = $oAdesaoPreco->si06_sequencial;
            $this->itensRegPrecoRepository->update(
              $item->si07_sequencial,
              $item->toArray()
            );
          }
        }
      }
     
      DB::commit();
      return [
        'status' => 200,
        'message' => 'Adesão de Registro de Preço atualizada com sucesso!',
        'data' => [
          'adesao' => $oAdesaoPreco->toArray()
        ]
      ];
    } catch(\Throwable $e){
      DB::rollBack();
      return [
          'status' => 500,
          'message' => $e->getMessage()
      ];
    }
  }

}