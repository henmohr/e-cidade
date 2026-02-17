<?php

namespace ECidade\RecursosHumanos\ESocial\Agendamento\Eventos;

use ECidade\RecursosHumanos\ESocial\Agendamento\Eventos\EventoBase;

/**
 * Classe responsável por montar as informações do evento S2200 Esocial
 *
 * @package  ECidade\RecursosHumanos\ESocial\Agendamento\Eventos
 * @author   Robson de Jesus
 */
class EventoS2205 extends EventoBase
{

    /**
     *
     * @param \stdClass $dados
     */
    function __construct($dados)
    {
        parent::__construct($dados);
    }

    /**
     * Retorna dados no formato necessario para envio
     * pela API sped-esocial
     * @return array stdClass
     */
    public function montarDados()
    {
        $aDadosAPI = array();
        $iSequencial = 1;
        foreach ($this->dados as $oDados) {

            $oDadosAPI                                   = new \stdClass;
            $oDadosAPI->evtAltCadastral                      = new \stdClass;
            $oDadosAPI->evtAltCadastral->sequencial          = $iSequencial;
            $oDadosAPI->evtAltCadastral->indRetif            = 1;
            $oDadosAPI->evtAltCadastral->nrRecibo            = null;
            //$oDadosAPI->evtAdmissao->cpfTrab             = $oDados->trabalhador->cpfTrab;

            $aDadosAPI[] = $oDadosAPI;
            $iSequencial++;
        }
        // echo '<pre>';
        // print_r($aDadosAPI);
        // exit;
        return $aDadosAPI;
    }

    /**
     * Retorna dados dos dependentes no formato necessario para envio
     * pela API sped-esocial
     * @return array stdClass
     */
    private function buscarDependentes($matricula)
    {

        $oDaorhdepend = \db_utils::getDao("rhdepend");
        $sqlDependentes = $oDaorhdepend->sql_query_file(null, "*", "rh31_codigo", "rh31_regist = {$matricula}");
        $rsDependentes = db_query($sqlDependentes);
        if (pg_num_rows($rsDependentes) == 0) {
            return null;
        }
        $aDependentes = array();
        for ($iCont = 0; $iCont < pg_num_rows($rsDependentes); $iCont++) {
            $oDependentes = \db_utils::fieldsMemory($rsDependentes, $iCont);
            $oDependFormatado = new \stdClass;
            switch ($oDependentes->rh31_gparen) {
                case 'C':
                    $oDependFormatado->tpdep = '01';
                    break;
                case 'F':
                    $oDependFormatado->tpdep = '03';
                    break;
                case 'P':
                case 'M':
                case 'A':
                    $oDependFormatado->tpdep = '09';
                    break;

                default:
                    $oDependFormatado->tpdep = '99';
                    break;
            }
            $oDependFormatado->nmdep = $oDependentes->rh31_nome;
            $oDependFormatado->dtnascto = $oDependentes->rh31_dtnasc;
            $oDependFormatado->cpfdep = empty($oDependentes->rh31_cpf) ? null : $oDependentes->rh31_cpf;
            $oDependFormatado->depirrf = ($oDependentes->rh31_depirrf == "0" ? "N" : "S");
            $oDependFormatado->depsf = ($oDependentes->rh31_depend == "N" ? "N" : "S");
            $oDependFormatado->inctrab = ($oDependentes->rh31_depirrf == "N" ? "N" : "S");

            $aDependentes[] = $oDependFormatado;
        }
        return $aDependentes;
    }


    /**
     * Retorna dados dos horario no formato necessario para envio
     * pela API sped-esocial
     * @return array stdClass
     */
    private function buscarHorarios($matricula)
    {

        $aHorarios = array();
        $oDaoJornada = \db_utils::getDao("jornada");
        $rsHorarios = db_query($oDaoJornada->sqlQueryHorario($matricula));
        if (pg_num_rows($rsHorarios) == 0) {
            return null;
        }
        for ($iCont = 0; $iCont < pg_num_rows($rsHorarios); $iCont++) {
            $oHorarioFormatado = new \stdClass;
            $oHorario = \db_utils::fieldsMemory($rsHorarios, $iCont);
            $oHorarioFormatado->codHorContrat = $oHorario->rh188_sequencial;
            $oHorarioFormatado->dia = date('w', strtotime($oHorario->diatrabalho));
            $aHorarios[] = $oHorarioFormatado;
        }
        return $aHorarios;
    }

    /**
     * Retorna dados dos afastamentos no formato necessario para envio
     * pela API sped-esocial
     * @return array stdClass
     */
    private function buscarAfastamentos($matricula)
    {


        $acodMotAfastEsocial = array(
            'O1' => '01',
            'O2' => '01',
            'O3' => '01',
            'P1' => '03',
            'P2' => '01',
            'Q1' => '17',
            'Q2' => '35',
            'Q3' => '19',
            'Q4' => '20',
            'Q5' => '20',
            'Q6' => '20',
            'R' => '29',
            'U3' => '06',
            'W' => '24',
            'X' => '21'
        );

        $acodMotAfastEcidade = array('O1', 'O2', 'O3', 'P1', 'P2', 'Q1', 'Q2', 'Q3', 'Q4', 'Q5', 'Q6', 'R', 'U3', 'W', 'X');
        $aAfastamentos = array();
        $oDaoAfasta = \db_utils::getDao("afasta");
        $rsAfastamentos = db_query($oDaoAfasta->sql_query_file(null, "*", null, "r45_regist = {$matricula} AND r45_codafa IN ('" . implode("','", $acodMotAfastEcidade) . "')"));
        if (pg_num_rows($rsAfastamentos) == 0) {
            return null;
        }
        for ($iCont = 0; $iCont < pg_num_rows($rsAfastamentos); $iCont++) {
            $oAfastamentoFormatado = new \stdClass;
            $oAfastamento = \db_utils::fieldsMemory($rsAfastamentos, $iCont);
            $oAfastamentoFormatado->dtIniAfast = $oAfastamento->r45_dtafas;
            $oAfastamentoFormatado->codMotAfast = $acodMotAfastEsocial[$oAfastamento->r45_codafa];
            $aAfastamentos[] = $oAfastamentoFormatado;
        }
        return $aAfastamentos;
    }
}
