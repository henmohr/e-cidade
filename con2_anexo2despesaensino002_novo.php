<?php
/*
 *     E-cidade Software Publico para Gestao Municipal
 *  Copyright (C) 2014  DBSeller Servicos de Informatica
 *                            www.dbseller.com.br
 *                         e-cidade@dbseller.com.br
 *
 *  Este programa e software livre; voce pode redistribui-lo e/ou
 *  modifica-lo sob os termos da Licenca Publica Geral GNU, conforme
 *  publicada pela Free Software Foundation; tanto a versao 2 da
 *  Licenca como (a seu criterio) qualquer versao mais nova.
 *
 *  Este programa e distribuido na expectativa de ser util, mas SEM
 *  QUALQUER GARANTIA; sem mesmo a garantia implicita de
 *  COMERCIALIZACAO ou de ADEQUACAO A QUALQUER PROPOSITO EM
 *  PARTICULAR. Consulte a Licenca Publica Geral GNU para obter mais
 *  detalhes.
 *
 *  Voce deve ter recebido uma copia da Licenca Publica Geral GNU
 *  junto com este programa; se nao, escreva para a Free Software
 *  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA
 *  02111-1307, USA.
 *
 *  Copia da licenca no diretorio licenca/licenca_en.txt
 *                                licenca/licenca_pt.txt
 */

require_once("dbforms/db_funcoes.php");
require_once("libs/db_stdlib.php");
require_once("libs/db_conecta.php");
require_once("libs/db_sessoes.php");
require_once("libs/db_usuariosonline.php");
require_once("vendor/mpdf/mpdf/mpdf.php");
require_once("libs/db_liborcamento.php");
require_once("libs/db_libcontabilidade.php");
require_once("libs/db_sql.php");
require_once("classes/db_cgm_classe.php");
require_once("classes/db_slip_classe.php");
require_once("classes/db_infocomplementaresinstit_classe.php");
require_once("classes/db_empresto_classe.php");
require_once("classes/db_empempenho_classe.php");
require_once("model/contabilidade/relatorios/ensino/RelatorioReceitaeDespesaEnsino.model.php");


$clselorcdotacao = new cl_selorcdotacao();
$clrotulo = new rotulocampo;

db_postmemory($_POST);

$dtini = implode("-", array_reverse(explode("/", $DBtxt21)));
$dtfim = implode("-", array_reverse(explode("/", $DBtxt22)));

$clinfocomplementaresinstit = new cl_infocomplementaresinstit();
$clSlip = new cl_slip();

$instits = str_replace('-', ', ', $db_selinstit);
$aInstits = explode(",", $instits);

if (count($aInstits) > 1) {
    $oInstit = new Instituicao();
    $oInstit = $oInstit->getDadosPrefeitura();
} else {
    foreach ($aInstits as $iInstit) {
        $oInstit = new Instituicao($iInstit);
    }
}
db_inicio_transacao();

/**
 * pego todas as instituições;
 */
$rsInstits = $clinfocomplementaresinstit->sql_record($clinfocomplementaresinstit->sql_query(null, "si09_instit, si09_tipoinstit", null, null));

$ainstitunticoes = array();
for ($i = 0; $i < pg_num_rows($rsInstits); $i++) {
    $odadosInstint = db_utils::fieldsMemory($rsInstits, $i);
    $ainstitunticoes[] = $odadosInstint->si09_instit;
}
$iInstituicoes = implode(',', $ainstitunticoes);

$rsTipoinstit = $clinfocomplementaresinstit->sql_record($clinfocomplementaresinstit->sql_query(null, "si09_sequencial, si09_tipoinstit", null, "si09_instit in( {$instits})"));

/**
 * busco o tipo de instituicao
 */
$ainstitunticoes = array();
$aTipoistituicao = array();

for ($i = 0; $i < pg_num_rows($rsTipoinstit); $i++) {
    $odadosInstint = db_utils::fieldsMemory($rsTipoinstit, $i);
    $aTipoistituicao[] = $odadosInstint->si09_tipoinstit;
    $iCont = pg_num_rows($rsTipoinstit);
}


$sWhereDespesa      = " o58_instit in({$instits})";
db_query("drop table if exists work_dotacao");
criaWorkDotacao($sWhereDespesa,array($anousu), $dtini, $dtfim);

$sWhereReceita      = "o70_instit in ({$instits})";
$rsReceitas = db_receitasaldo(11, 1, 3, true, $sWhereReceita, $anousu, $dtini, $dtfim, false, ' * ', true, 0);
$aReceitas = db_utils::getColectionByRecord($rsReceitas);
db_query("drop table if exists work_receita");
criarWorkReceita($sWhereReceita, array($anousu), $dtini, $dtfim);


$nTotalReceitasRecebidasFundeb = 0;
$nContribuicaoFundeb = 0;

$nDevolucaoRecursoFundeb = 0;
$rsSlip = $clSlip->sql_record($clSlip->sql_query_fundeb($dtini, $dtfim, $aInstits));
$nDevolucaoRecursoFundeb = db_utils::fieldsMemory($rsSlip, 0)->k17_valor;

$nTransferenciaRecebidaFundeb = 0;

$aTransferenciasRecebidasFundeb = getSaldoReceita(null, "sum(saldo_arrecadado_acumulado) as saldo_arrecadado_acumulado", null, "o57_fonte like '417515001%'");

$nTransferenciaRecebidaFundeb = count($aTransferenciasRecebidasFundeb) > 0 ? $aTransferenciasRecebidasFundeb[0]->saldo_arrecadado_acumulado : 0;

$aTotalContribuicaoFundeb = getSaldoReceita(null, "sum(saldo_arrecadado_acumulado) as saldo_arrecadado_acumulado", null, "o57_fonte like '495%'");
$nTotalContribuicaoFundeb = count($aTotalContribuicaoFundeb) > 0 ? $aTotalContribuicaoFundeb[0]->saldo_arrecadado_acumulado : 0;

$nTotalReceitasRecebidasFundeb = abs($nDevolucaoRecursoFundeb) + abs($nTransferenciaRecebidaFundeb);
$nResulatadoLiquidoTransfFundeb = $nTotalReceitasRecebidasFundeb-abs($nTotalContribuicaoFundeb);


$fSubTotal    = 0;
$aSubFuncoes  = array(122,272,271,361,365,366,367,843);
$sFuncao      = "12, 28";
$aFonte       = array("'15000001','25000001'");
$aFonte2      = array("'17180000'");
$aFonteFundeb = array("'15020001','15400000'");
$oReceitaeDespesaEnsino = new RelatorioReceitaeDespesaEnsino();

/**
 * mPDF
 * @param string $mode | padrão: BLANK
 * @param mixed $format | padrão: A4
 * @param float $default_font_size | padrão: 0
 * @param string $default_font | padrão: ''
 * @param float $margin_left | padrão: 15
 * @param float $margin_right | padrão: 15
 * @param float $margin_top | padrão: 16
 * @param float $margin_bottom | padrão: 16
 * @param float $margin_header | padrão: 9
 * @param float $margin_footer | padrão: 9
 *
 * Nenhum dos parâmetros é obrigatório
 */

$mPDF = new mpdf('', '', 0, '', 10, 10, 20, 10, 5, 11);
// DEFINE O FUSO HORARIO COMO O HORARIO DE BRASILIA
date_default_timezone_set('America/Sao_Paulo');
// CRIA UMA VARIAVEL E ARMAZENA A HORA ATUAL DO FUSO-HORÀRIO DEFINIDO (BRASÍLIA)
$dataLocal = date('d/m/Y H:i:s', time());

$header = " <header>
                <div style=\" height: 120px; font-family:Arial\">
                    <div style=\"width:33%; float:left; padding:5px; font-size:10px;\">
                        <b><i>{$oInstit->getDescricao()}</i></b><br/>
                        <i>{$oInstit->getLogradouro()}, {$oInstit->getNumero()}</i><br/>
                        <i>{$oInstit->getMunicipio()} - {$oInstit->getUf()}</i><br/>
                        <i>{$oInstit->getTelefone()} - CNPJ: " . db_formatar($oInstit->getCNPJ(), "cnpj") . "</i><br/>
                        <i>{$oInstit->getSite()}</i>
                    </div>
                    <div style=\"width:25%; float:right\" class=\"box\">
                        <b>Relatório Despesa Ensino - Anexo III</b><br/>
                        <b>INSTITUIÇÕES:</b> ";
                        foreach ($aInstits as $iInstit) {
                            $oInstituicao = new Instituicao($iInstit);
                            $header .= "(" . trim($oInstituicao->getCodigo()) . ") " . $oInstituicao->getDescricao() . " ";
                        }
                        $header .= "<br/> <b>PERÍODO:</b> {$DBtxt21} A {$DBtxt22} <br/>
                    </div>
                </div>
           </header>";

$footer = "<footer>
                <div style='border-top:1px solid #000;width:100%;font-family:sans-serif;font-size:10px;height:10px;'>
                    <div style='text-align:left;font-style:italic;width:90%;float:left;'>
                        Financeiro>Contabilidade>Relatórios de Acompanhamento>Receita Despesa Ensino - Anexo III
                        Emissor: " . db_getsession("DB_login") . " Exerc: " . db_getsession("DB_anousu") . " Data:" . $dataLocal  . "
                    <div style='text-align:right;float:right;width:10%;'>
                        {PAGENO}
                    </div>
                </div>
            </footer>";

$mPDF->WriteHTML(file_get_contents('estilos/tab_relatorio.css'), 1);
$mPDF->setHTMLHeader(utf8_encode($header), 'O', true);
$mPDF->setHTMLFooter(utf8_encode($footer), 'O', true);
$mPDF->shrink_tables_to_fit = 1;

ob_start();
?>
<html>
    <head>
        <style type="text/css">
            .ritz .waffle {
                color: inherit;
                font-family: 'Arial';
                font-size: 10px;
                width: 100%;
            }
            .title-relatorio {
                text-align: center;
                padding-top: 50px;
            }
            .tr-table{
                height:20px;
            }
            .body-relatorio {
                width: 100%;
                height: 80%;
            }
            .footer-row {
                height: 20px;
                background-color: #d8d8d8;
                width: 80%;
                border: 1px SOLID #000000;
                font-family: 'Arial';
                font-size: 10px;
                font-weight: bold;
                padding: 2px 3px 2px 3px;
                text-align: right;
                vertical-align: bottom;
                white-space: nowrap;
            }
            .footer-row-valor {
                height: 20px;
                background-color: #d8d8d8;
                width: 20%;
                border-right: 1px SOLID #000000;
                border-top: 1px SOLID #000000;
                border-bottom: 1px SOLID #000000;
                font-family: 'Arial', Calibre;
                font-size: 10px;
                font-weight: bold;
                padding: 2px 3px 2px 3px;
                text-align: right;
                vertical-align: bottom;
                white-space: nowrap;
            }
            .footer-total-row {
                height: 20px;
                background-color: #d8d8d8;
                width: 80%;
                border: 1px SOLID #000000;
                font-family: 'Arial';
                font-size: 10px;
                font-weight: bold;
                padding: 2px 3px 2px 3px;
                text-align: left;
                vertical-align: bottom;
                white-space: nowrap;
            }
            .footer-total-row-valor {
                height: 20px;
                background-color: #d8d8d8;
                width: 20%;
                border-right: 1px SOLID #000000;
                border-top: 1px SOLID #000000;
                border-bottom: 1px SOLID #000000;
                font-family: 'Arial', Calibre;
                font-size: 10px;
                font-weight: bold;
                padding: 2px 3px 2px 3px;
                text-align: right;
                vertical-align: bottom;
                white-space: nowrap;
            }
            .title-row {
                background-color: #ffffff;
                direction: ltr;
                padding: 2px 3px 2px 3px;
                font-size: 10px;
                font-weight: bold;
            }
            .subtitle-2-row {
                background-color: #d8d8d8;
                direction: ltr;
                padding: 2px 3px 2px 3px;
                font-size: 10px;
                border-left: 1px SOLID #000000;
                border-right: 1px SOLID #000000;
                font-weight: bold;
            }
            .subtitle-4-row {
                background-color: #d8d8d8;
                direction: ltr;
                padding: 2px 3px 2px 3px;
                font-size: 10px;
                font-weight: bold;
            }
            .subtitle-3-row {
                background-color: #d8d8d8;
                direction: ltr;
                padding: 2px 3px 2px 3px;
                font-size: 10px;
                border-left: 1px SOLID #000000;
                border-right: 1px SOLID #000000;
                font-weight: bold;
            }
            .subtitle-row {
                background-color: #d8d8d8;
                direction: ltr;
                border: 0.5px SOLID #000000;
                font-size: 10px;
                padding: 2px 3px 2px 3px;
                font-weight: bold;
            }
            .text-row {
                background-color: #ffffff;
                color: #000000;
                direction: ltr;
                font-size: 10px;
                vertical-align: bottom;
                white-space: nowrap;
                padding: 2px 2px 2px 2px;
            }
            .ritz .waffle .clear {
                background-color: #ffffff;
                color: #000000;
                direction: ltr;
                font-size: 10pt;
                padding: 2px 3px 2px 3px;
                white-space: nowrap;
            }
        </style>
    </head>

    <body>
        <div class="ritz ">
            <div class="title-relatorio">
                <strong>Anexo III</strong><br />
                <strong>Demonstrativo dos Gastos com Manutenção e Desenvolvimento do Ensino</strong><br />
                <strong> (Art. 212 da CR/88; EC nº53/06, Leis 9.394/96, 14.113/2020 e IN 05/2012)</strong><br /><br />
            </div>
            <div class="body-relatorio">
                <table class="waffle" width="600px" cellspacing="0" cellpadding="0" style="border: 1px #000" autosize="1">
                    <tbody>
                        <tr>
                            <td class="title-row" colspan="5">I - DESPESAS</td>
                        </tr>
                        <tr>
                            <td class="subtitle-row" style="width: 300px;">FUNÇÃO / SUBFUNÇÃO/ PROGRAMA</td>
                            <td class="subtitle-row" style="width: 100px; text-align: center;">VALOR PAGO</td>
                            <td class="subtitle-row" style="text-align: center;">VALOR EMPENHADO E NÃO LIQUIDADO</td>
                            <td class="subtitle-row" style="text-align: center;">VALOR LIQUIDADO A PAGAR</td>
                            <td class="subtitle-row" style="width: 100px; text-align: center;">TOTAL</td>
                        </tr>
                        <tr>
                            <td class="subtitle-2-row" colspan="5">1 - FUNÇÕES 12 E 28 - IMPOSTOS E TRANSFERÊNCIAS DE IMPOSTOS</td>   
                        </tr>
                        <?php
                        /**
                         * @todo loop de cada subfuncao
                         *
                         */
                        $nValorTotalPago = 0;
                        $nValorTotalEmpenhadoENaoLiquidado = 0;
                        $nValorTotalLiquidadoAPagar = 0;
                        $nValorTotalGeral = 0;
                        foreach ($aSubFuncoes as $iSubFuncao) {
                            $oReceitaeDespesaEnsino->setAnousu($anousu);
                            $oReceitaeDespesaEnsino->setSubFuncao($iSubFuncao);
                            $oReceitaeDespesaEnsino->setFuncao($sFuncao);
                            $oReceitaeDespesaEnsino->setFontes($aFonte);
                            $oReceitaeDespesaEnsino->setInstits($instits);
                            $dadosLinha1 = $oReceitaeDespesaEnsino->getLinha1FuncaoeSubfuncao();
                            $sDescrSubfuncao                       = $dadosLinha1['0']; 
                            $nValorPagoSubFuncao                   = $dadosLinha1['2'];
                            $nValorEmpenhadoENaoLiquidadoSubFuncao = $dadosLinha1['3'];
                            $nValorLiquidadoAPagarSubFuncao        = $dadosLinha1['4'];
                            $nValorTotalSubFuncao                  = $dadosLinha1['5'];
                            if (count($dadosLinha1['1']) > 0) {
                            ?>
                                <tr>
                                    <td class="text-row" style="text-align: left; border-left: 1px SOLID #000000; width: 300px;"><?php echo db_formatar($iSubFuncao, 'subfuncao')." ".$sDescrSubfuncao ?></td>
                                    <td class="text-row" style="text-align: right; "><?php echo db_formatar($nValorPagoSubFuncao, "f"); ?></td>
                                    <td class="text-row" style="text-align: right; "><?php echo db_formatar($nValorEmpenhadoENaoLiquidadoSubFuncao, "f"); ?></td>
                                    <td class="text-row" style="text-align: right; "><?php echo db_formatar($nValorLiquidadoAPagarSubFuncao, "f"); ?></td>
                                    <td class="text-row" style="text-align: right; border-right: 1px SOLID #000000;"><?php echo db_formatar($nValorTotalSubFuncao, "f"); ?></td>
                                </tr>
                            <?php
                            /**
                             * @todo para cada subfuncao lista os programas
                             */
                            
                            foreach ($dadosLinha1['1'] as $oDespesaPrograma) {
                                $oReceitaeDespesaEnsino->setDespesaPrograma($oDespesaPrograma);
                                $oReceitaeDespesaEnsino->setSubTotal($fSubTotal);
                                $oReceitaeDespesaEnsino->setValorTotalPago($nValorTotalPago);
                                $oReceitaeDespesaEnsino->setValorTotalEmpenhadoENaoLiquidado($nValorTotalEmpenhadoENaoLiquidado);
                                $oReceitaeDespesaEnsino->setValorTotalLiquidadoAPagar($nValorTotalLiquidadoAPagar);
                                $oReceitaeDespesaEnsino->setValorTotalGeral($nValorTotalGeral);
                                $dadoslinha2                  = $oReceitaeDespesaEnsino->getLinha2FuncaoeSubfuncao();
                                $oPrograma                    = $dadoslinha2['0'];
                                $fSubTotal                   += $dadoslinha2['1'];
                                $nValorPago                   = $dadoslinha2['2'];
                                $nValorEmpenhadoENaoLiquidado = $dadoslinha2['3'];
                                $nValorLiquidadoAPagar        = $dadoslinha2['4'];
                                $nValorTotal                  = $dadoslinha2['5'];
                                $nValorTotalPago             += $dadoslinha2['6'];
                                $nValorTotalEmpenhadoENaoLiquidado += $dadoslinha2['7'];
                                $nValorTotalLiquidadoAPagar  += $dadoslinha2['8'];
                                $nValorTotalGeral            += $dadoslinha2['9'];
                               ?>
                                <tr>
                                    <td class="text-row" style="text-align: left; border-left: 1px SOLID #000000; width: 300px;"><?php echo db_formatar($oPrograma->getCodigoPrograma(), "programa")." ".$oPrograma->getDescricao(); ?></td>
                                    <td class="text-row" style="text-align: right; "><?php echo db_formatar($nValorPago, "f"); ?></td>
                                    <td class="text-row" style="text-align: right; "><?php echo db_formatar($nValorEmpenhadoENaoLiquidado, "f"); ?></td>
                                    <td class="text-row" style="text-align: right; "><?php echo db_formatar($nValorLiquidadoAPagar, "f"); ?></td>
                                    <td class="text-row" style="text-align: right; border-right: 1px SOLID #000000;"><?php echo db_formatar($nValorTotal, "f"); ?></td>
                                </tr>
                            <?php }
                            ?>
                            <tr>
                                <td class="text-row" style="text-align: left; border-left: 1px SOLID #000000; width: 300px;">&nbsp;</td>
                                <td class="text-row"></td>
                                <td class="text-row"></td>
                                <td class="text-row"></td>
                                <td class="text-row" style="text-align: right; border-right: 1px SOLID #000000;"></td>
                            </tr>
                            <?php
                            }
                        }
                        ?>
                         <tr>
                            <td class="subtitle-2-row" colspan="5">2 - FUNÇÕES 12 E 28  - AUXÍLIO FINANCEIRO - OUTORGA CRÉDITO TRIBUTÁRIO ICMS - ART. 5º, INCISO V, EC Nº 123/2022  </td>
                        </tr>
                        <?php
                        foreach ($aSubFuncoes as $iSubFuncao) {
                            $oReceitaeDespesaEnsino->setAnousu($anousu);
                            $oReceitaeDespesaEnsino->setSubFuncao($iSubFuncao);
                            $oReceitaeDespesaEnsino->setFuncao($sFuncao);
                            $oReceitaeDespesaEnsino->setFontes($aFonte2);
                            $oReceitaeDespesaEnsino->setInstits($instits);
                            $dadosLinha1 = $oReceitaeDespesaEnsino->getLinha1FuncaoeSubfuncao();
                            $nValorPagoSubFuncao                   = $dadosLinha1['2'];
                            $nValorEmpenhadoENaoLiquidadoSubFuncao = $dadosLinha1['3'];
                            $nValorLiquidadoAPagarSubFuncao        = $dadosLinha1['4'];
                            $nValorTotalSubFuncao                  = $dadosLinha1['5'];
                            if (count($dadosLinha1['1']) > 0) {
                            ?>
                                <tr>
                                    <td class="text-row" style="text-align: left; border-left: 1px SOLID #000000; width: 300px;"><?php echo db_formatar($iSubFuncao, 'subfuncao')." ".$sDescrSubfuncao ?></td>
                                    <td class="text-row" style="text-align: right; "><?php echo db_formatar($nValorPagoSubFuncao, "f"); ?></td>
                                    <td class="text-row" style="text-align: right; "><?php echo db_formatar($nValorEmpenhadoENaoLiquidadoSubFuncao, "f"); ?></td>
                                    <td class="text-row" style="text-align: right; "><?php echo db_formatar($nValorLiquidadoAPagarSubFuncao, "f"); ?></td>
                                    <td class="text-row" style="text-align: right; border-right: 1px SOLID #000000;"><?php echo db_formatar($nValorTotalSubFuncao, "f"); ?></td>
                                </tr>
                            <?php
                            /**
                             * @todo para cada subfuncao lista os programas
                             */
                            foreach ($dadosLinha1['1'] as $oDespesaPrograma) {
                                $oReceitaeDespesaEnsino->setDespesaPrograma($oDespesaPrograma);
                                $oReceitaeDespesaEnsino->setSubTotal($fSubTotal);
                                $oReceitaeDespesaEnsino->setValorTotalPago($nValorTotalPago);
                                $oReceitaeDespesaEnsino->setValorTotalEmpenhadoENaoLiquidado($nValorTotalEmpenhadoENaoLiquidado);
                                $oReceitaeDespesaEnsino->setValorTotalLiquidadoAPagar($nValorTotalLiquidadoAPagar);
                                $oReceitaeDespesaEnsino->setValorTotalGeral($nValorTotalGeral);
                                $dadoslinha2                  = $oReceitaeDespesaEnsino->getLinha2FuncaoeSubfuncao();
                                $oPrograma                    = $dadoslinha2['0'];
                                $fSubTotal                   += $dadoslinha2['1'];
                                $nValorPago                   = $dadoslinha2['2'];
                                $nValorEmpenhadoENaoLiquidado = $dadoslinha2['3'];
                                $nValorLiquidadoAPagar        = $dadoslinha2['4'];
                                $nValorTotal                  = $dadoslinha2['5'];
                                $nValorTotalPago             += $dadoslinha2['6'];
                                $nValorTotalEmpenhadoENaoLiquidado += $dadoslinha2['7'];
                                $nValorTotalLiquidadoAPagar  += $dadoslinha2['8'];
                                $nValorTotalGeral            += $dadoslinha2['9'];
                                ?>
                                <tr>
                                    <td class="text-row" style="text-align: left; border-left: 1px SOLID #000000; width: 300px;"><?php echo db_formatar($dadoslinha2['0']->getCodigoPrograma(), "programa")." ".$dadoslinha2['0']->getDescricao(); ?></td>
                                    <td class="text-row" style="text-align: right; "><?php echo db_formatar($dadoslinha2['2'], "f"); ?></td>
                                    <td class="text-row" style="text-align: right; "><?php echo db_formatar($dadoslinha2['3'], "f"); ?></td>
                                    <td class="text-row" style="text-align: right; "><?php echo db_formatar($dadoslinha2['4'], "f"); ?></td>
                                    <td class="text-row" style="text-align: right; border-right: 1px SOLID #000000;"><?php echo db_formatar($dadoslinha2['5'], "f"); ?></td>
                                </tr>
                            <?php }
                            ?>
                            <tr>
                                <td class="text-row" style="text-align: left; border-left: 1px SOLID #000000; width: 300px;">&nbsp;</td>
                                <td class="text-row"></td>
                                <td class="text-row"></td>
                                <td class="text-row"></td>
                                <td class="text-row" style="text-align: right; border-right: 1px SOLID #000000;"></td>
                            </tr>
                            <?php
                            }
                        }
                        ?>
                        <tr>
                            <td class="subtitle-2-row" colspan="5">3 - FUNÇÕES 12 E 28  - RECURSOS NÃO VINCULADOS DA COMPENSAÇÃO DE IMPOSTOS - LC Nº 194/2022 </td>                        
                        </tr>
                        <?php
                        $nTotalDespesaLiquidaFundeb = 0;
                        foreach ($aSubFuncoes as $iSubFuncao) {
                            $oReceitaeDespesaEnsino->setSubFuncao($iSubFuncao);
                            $oReceitaeDespesaEnsino->setFuncao($sFuncao);
                            $oReceitaeDespesaEnsino->setFonteFundeb(array("'15020001'"));
                            $oReceitaeDespesaEnsino->setInstits($instits);
                            $dadoslinha3 =  $oReceitaeDespesaEnsino->getLinha1FuncaoFundeb();
                            $aDespesasProgramas = $dadoslinha3['1'];
                            if (count($aDespesasProgramas) > 0) {
                                $nValorPagoSubFuncao                    = $dadoslinha3['2'];
                                $nValorEmpenhadoENaoLiquidadoSubFuncao  = $dadoslinha3['3'];
                                $nValorLiquidadoAPagarSubFuncao         = $dadoslinha3['4'];
                                $nValorTotalSubFuncao                   = $dadoslinha3['5'];                             
                            ?>
                                <tr>
                                    <td class="text-row" style="text-align: left; border-left: 1px SOLID #000000; width: 300px;"><?php echo db_formatar($iSubFuncao, 'subfuncao')." ".$dadoslinha3['0'] ?></td>
                                    <td class="text-row" style="text-align: right; "><?php echo db_formatar($nValorPagoSubFuncao, "f"); ?></td>
                                    <td class="text-row" style="text-align: right; "><?php echo db_formatar($nValorEmpenhadoENaoLiquidadoSubFuncao, "f"); ?></td>
                                    <td class="text-row" style="text-align: right; "><?php echo db_formatar($nValorLiquidadoAPagarSubFuncao, "f"); ?></td>
                                    <td class="text-row" style="text-align: right; border-right: 1px SOLID #000000;"><?php echo db_formatar($nValorTotalSubFuncao, "f"); ?></td>
                                </tr>
                            <?php
                            foreach ($aDespesasProgramas as $oDespesaPrograma) {
                                $oReceitaeDespesaEnsino->setDespesaPrograma($oDespesaPrograma);
                                $oReceitaeDespesaEnsino->setValorTotalPago($nValorTotalPago);
                                $oReceitaeDespesaEnsino->setValorTotalEmpenhadoENaoLiquidado($nValorTotalEmpenhadoENaoLiquidado);
                                $oReceitaeDespesaEnsino->setValorTotalLiquidadoAPagar($nValorTotalLiquidadoAPagar);
                                $oReceitaeDespesaEnsino->setValorTotalGeral($nValorTotalGeral);
                                $dadoslinha3 =  $oReceitaeDespesaEnsino->getLinha2FuncaoFundeb();

                                $oPrograma                    = $dadoslinha3['0'];
                                $nValorPago                   = $dadoslinha3['1'];
                                $nValorEmpenhadoENaoLiquidado = $dadoslinha3['2'];
                                $nValorLiquidadoAPagar        = $dadoslinha3['3'];
                                $nValorTotal                  = $dadoslinha3['4'];
                                $nValorTotalPago             += $dadoslinha3['5'];
                                $nValorTotalEmpenhadoENaoLiquidado += $dadoslinha3['6']; 
                                $nValorTotalLiquidadoAPagar  += $dadoslinha3['7'];
                                $nValorTotalGeral            += $dadoslinha3['8'];
                                ?>
                                <tr>
                                    <td class="text-row" style="text-align: left; border-left: 1px SOLID #000000; width: 300px;"><?php echo db_formatar($oPrograma->getCodigoPrograma(), "programa")." ".$oPrograma->getDescricao(); ?></td>
                                    <td class="text-row" style="text-align: right; "><?php echo db_formatar($nValorPago, "f"); ?></td>
                                    <td class="text-row" style="text-align: right; "><?php echo db_formatar($nValorEmpenhadoENaoLiquidado, "f"); ?></td>
                                    <td class="text-row" style="text-align: right; "><?php echo db_formatar($nValorLiquidadoAPagar, "f"); ?></td>
                                    <td class="text-row" style="text-align: right; border-right: 1px SOLID #000000;"><?php echo db_formatar($nValorTotal, "f"); ?></td>
                                </tr>
                            <?php }
                            ?>
                                <tr>
                                    <td class="text-row" style="text-align: left; border-left: 1px SOLID #000000; width: 300px;">&nbsp;</td>
                                    <td class="text-row"></td>
                                    <td class="text-row"></td>
                                    <td class="text-row"></td>
                                    <td class="text-row" style="text-align: right; border-right: 1px SOLID #000000;"></td>
                                </tr>
                            <?php
                            }
                        }
                        ?>
                        <tr>
                            <td class="subtitle-row" style="width: 300px;">4 - TOTAL DESPESAS (1 + 2 + 3)</td>
                            <td class="subtitle-row" style="text-align: right;"><?php echo db_formatar($nValorTotalPago, "f"); ?></td>
                            <td class="subtitle-row" style="text-align: right;"><?php echo db_formatar($nValorTotalEmpenhadoENaoLiquidado, "f"); ?></td>
                            <td class="subtitle-row" style="text-align: right;"><?php echo db_formatar($nValorTotalLiquidadoAPagar, "f"); ?></td>
                            <td class="subtitle-row" style="width: 100px; text-align: right;"><?php echo db_formatar($nValorTotalGeral, "f"); ?></td>
                        </tr>
                    </tbody>
                </table>

            </div>
            <div class="body-relatorio" style="padding-top: 10px;">
            <table class="waffle" width="600px" cellspacing="0" cellpadding="0" style="border: 1px #000; margin-top: 50px;" autosize="1">
                    <tbody>
                        <tr>
                            <td class="title-row" >II - TOTAL DA APLICAÇÃO NO ENSINO</td>
                        </tr>
                        <tr>
                            <td class="subtitle-row" style="width: 300px; text-align: center;">DESCRIÇÃO</td>
                            <td class="subtitle-row" style="width: 100px; text-align: center;">VALOR</td>
                        </tr>
                        <tr>
                            <td class="text-row" style="text-align: left; border-left: 1px SOLID #000000;">5 - VALOR PAGO </td>
                            <td class="text-row" style="text-align: right; border-right: 1px SOLID #000000;">
                                <?php
                                    $nTotalAplicadoEntrada = 0;
                                    $nTotalAplicadoEntrada = $nValorTotalPago;
                                    echo db_formatar($nValorTotalPago, "f");
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-row" style="text-align: left; border-left: 1px SOLID #000000;">6 - TOTAL DAS RECEITAS TRANSFERIDAS AO FUNDEB</td>
                            <td class="text-row" style="text-align: right; border-right: 1px SOLID #000000;">
                                <?php
                                $nTotalAplicadoEntrada = $nTotalAplicadoEntrada + abs($nTotalContribuicaoFundeb);
                                echo db_formatar(abs($nTotalContribuicaoFundeb), "f");
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-row" style="text-align: left; border-left: 1px SOLID #000000;">7 - RESTOS A PAGAR INSCRITOS NO EXERCÍCIO</td>
                            <td class="text-row" style="text-align: right; border-right: 1px SOLID #000000;">
                            <?php
                                $oReceitaeDespesaEnsino->setDataInicial($dtini);
                                $oReceitaeDespesaEnsino->setDataFinal($dtfim);
                                $oReceitaeDespesaEnsino->setInstits($instits);
                                $dadosLinha7 = $oReceitaeDespesaEnsino->getLinha8RestosaPagarInscritosFonte101();
                                $nTotalAplicadoEntrada = $nTotalAplicadoEntrada + $dadosLinha7;
                                echo db_formatar($dadosLinha7, "f");
                        ?>
                        </td>
                        </tr>
                        <tr>
                            <td class="text-row" style="text-align: left; border-left: 1px SOLID #000000; padding-left: 20px;">7.1 - RECURSOS DE IMPOSTOS</td>
                            <td class="text-row" style="text-align: right; border-right: 1px SOLID #000000;">
                            <?php
                            $nLiqAPagar101 = 0;
                            $dtfimExercicio = db_getsession("DB_anousu")."-12-31";
                            $aFonte3 = array("'15000001','25000001'");
                            if($dtfim == $dtfimExercicio){
                                $oReceitaeDespesaEnsino->setFontes($aFonte3);
                                $oReceitaeDespesaEnsino->setDataInicial($dtini);
                                $oReceitaeDespesaEnsino->setDataFinal($dtfim);
                                $oReceitaeDespesaEnsino->setInstits($instits);
                                $oReceitaeDespesaEnsino->setTipo('ambos');
                                $nLiqAPagar101 = $oReceitaeDespesaEnsino->getEmpenhosApagar();
                            }
                            echo db_formatar($nLiqAPagar101, "f");
                            ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-row" style="text-align: left; border-left: 1px SOLID #000000; padding-left: 20px;">7.2 - RECUSOS DO AUXÍLIO FINANCEIRO - OUTORGA CRÉDITO TRIBUTÁRIO ICMS - ART. 5º, INCISO V, EC Nº 123/2022 </td>
                            <td class="text-row" style="text-align: right; border-right: 1px SOLID #000000;">
                            <?php
                            $nLiqAPagar136 = 0;
                            $dtfimExercicio = db_getsession("DB_anousu")."-12-31";
                            if($dtfim == $dtfimExercicio){
                                $oReceitaeDespesaEnsino->setFontes(array("'17180000'"));
                                $oReceitaeDespesaEnsino->setDataInicial($dtini);
                                $oReceitaeDespesaEnsino->setDataFinal($dtfim);
                                $oReceitaeDespesaEnsino->setInstits($instits);
                                $oReceitaeDespesaEnsino->setTipo('ambos');
                                $nLiqAPagar136 = $oReceitaeDespesaEnsino->getEmpenhosApagar();
                            }
                            echo db_formatar($nLiqAPagar136, "f");
                            ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-row" style="text-align: left; border-left: 1px SOLID #000000; padding-left: 20px;">7.3 - RECURSOS NÃO VINCULADOS DA COMPENSAÇÃO DE IMPOSTOS - ARTIGO 9º DA LC 141/2012</td>
                            <td class="text-row" style="text-align: right; border-right: 1px SOLID #000000;">
                            <?php
                            $nLiqAPagar118_119 = 0;
                            $dtfimExercicio = db_getsession("DB_anousu")."-12-31";
                            if($dtfim == $dtfimExercicio){
                                $oReceitaeDespesaEnsino->setFontes(array("'15020001','25020001'"));
                                $oReceitaeDespesaEnsino->setDataInicial($dtini);
                                $oReceitaeDespesaEnsino->setDataFinal($dtfim);
                                $oReceitaeDespesaEnsino->setInstits($instits);
                                $oReceitaeDespesaEnsino->setTipo('ambos');
                                $nLiqAPagar118_119 = $oReceitaeDespesaEnsino->getEmpenhosApagar();
                            }
                            echo db_formatar($nLiqAPagar118_119, "f");
                            ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-row" style="text-align: left; border-left: 1px SOLID #000000;">8 - VALOR APLICADO ATÉ O PRIMEIRO QUADRIMESTRE QUE INTEGRARÁ O LIMITE CONSTITUCIONAL</td>
                            <td class="text-row" style="text-align: right; border-right: 1px SOLID #000000;">
                                <?php
                                    $oReceitaeDespesaEnsino->setDataInicial($dtini);
                                    $oReceitaeDespesaEnsino->setDataFinal($dtfim);
                                    $oReceitaeDespesaEnsino->setInstits($instits);
                                    $nValorCusteadoSuperavit = $oReceitaeDespesaEnsino->getLinha7DespesasCusteadaSuperavitDoFundeb();
                                    $nTotalAplicadoEntrada = $nTotalAplicadoEntrada + $nValorCusteadoSuperavit;
                                    echo db_formatar($nValorCusteadoSuperavit, "f");
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-row" style="text-align: left; border-left: 1px SOLID #000000;">9 - RESTOS A PAGAR DE EXERCÍCIOS ANTERIORES SEM DISPONIBILIDADE FINANCEIRA PAGOS NO EXERCÍCIO ATUAL (CONSULTA 932.736)</td>
                            <td class="text-row" style="text-align: right; border-right: 1px SOLID #000000;">
                                <?php
                                $sFonte = ("'101','201','15000001','25000001'");
                                $where = " c61_instit in ({$instits}) and c61_codigo in ($sFonte) ";
                                $result = db_planocontassaldo_matriz(db_getsession("DB_anousu"), $dtini, $dtfim, false, $where,'',0);

                                for ($x = 0; $x < pg_numrows($result); $x++) {
                                    db_fieldsmemory($result, $x);
                                    if(substr($estrutural,1,14) == '00000000000000'){
                                        if($sinal_anterior == "C")
                                            $total_anterior -= $saldo_anterior;
                                        else {
                                            $total_anterior += $saldo_anterior;
                                        }
                                    }
                                }

                                $nValorRecursoImposto = $total_anterior;
                                $sele_work = " e60_instit in ({$instits}) ";
                                $sele_work1       = " and e91_recurso in ($sFonte)";
                                $sql_where_externo = " where $sele_work ";
                                $sql_order = " order by e91_recurso, e91_numemp ";
                                
                                $clempresto = new cl_empresto;
                                $sqlempresto = $clempresto->sql_rp_novo(db_getsession("DB_anousu"), $sele_work, $dtini, $dtfim, $sele_work1, $sql_where_externo, $sql_order);
                                $res = $clempresto->sql_record($sqlempresto);
                             
                                if ($clempresto->numrows == 0) {
                                    db_redireciona("db_erros.php?fechar=true&db_erro=Sem movimentação de restos a pagar.");
                                    exit;
                                }
                                $rows = $clempresto->numrows;
                                $total_rp_n_proc = 0;
                                $total_rp_proc = 0;
                                for ($x = 0; $x < $rows; $x++) {
                                    db_fieldsmemory($res, $x);
                                    $total_mov_liqui += $e91_vlremp;
                                    $total_mov_pagmento += $vlrpag;
                                    $total_mov_pagnproc += $vlrpagnproc;
                                }
                                $nValorRecursoFundeb101 = $total_mov_pagmento + $total_mov_pagnproc - $nValorRecursoImposto;
                                $oReceitaeDespesaEnsino->setFontes(array('136','236','17180000','27180000'));
                                $oReceitaeDespesaEnsino->setDataInicial($dtini);
                                $oReceitaeDespesaEnsino->setDataFinal($dtfim);
                                $oReceitaeDespesaEnsino->setInstits($instits);
                                $nValorRecursoFundeb136 = $oReceitaeDespesaEnsino->getRestosSemDisponilibidadeFundeb(array('136','236','17180000','27180000'), $dtini, $dtfim, $instits);
                                 
                                $oReceitaeDespesaEnsino->setFontes(array("'15020001','25020001'"));
                                $oReceitaeDespesaEnsino->setDataInicial($dtini);
                                $oReceitaeDespesaEnsino->setDataFinal($dtfim);
                                $oReceitaeDespesaEnsino->setInstits($instits);
                                $nValorRecursoFundeb118_119 = $oReceitaeDespesaEnsino->getRestosSemDisponilibidadeFundeb();
                                $nValorRecursoTotal     = $nValorRecursoFundeb101 + $nValorRecursoFundeb136 + $nValorRecursoFundeb118_119;
                                $nTotalAplicadoEntrada  = $nTotalAplicadoEntrada + $nValorRecursoTotal;
                                echo db_formatar($nValorRecursoTotal, "f");
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-row" style="text-align: left; border-left: 1px SOLID #000000; padding-left: 20px;">9.1 - RECURSOS DE IMPOSTOS</td>
                            <td class="text-row" style="text-align: right; border-right: 1px SOLID #000000;">
                                <?php
                                    echo db_formatar($nValorRecursoFundeb101, "f");
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-row" style="text-align: left; border-left: 1px SOLID #000000; padding-left: 20px;">9.2 - RECUSOS DO AUXÍLIO FINANCEIRO - OUTORGA CRÉDITO TRIBUTÁRIO ICMS - ART. 5º, INCISO V, EC Nº 123/2022</td>
                            <td class="text-row" style="text-align: right; border-right: 1px SOLID #000000;">
                                <?php
                                    echo db_formatar($nValorRecursoFundeb136, "f");
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-row" style="text-align: left; border-left: 1px SOLID #000000; padding-left: 20px;">9.3 - RECURSOS NÃO VINCULADOS DA COMPENSAÇÃO DE IMPOSTOS - ARTIGO 9º DA LC 141/2012</td>
                            <td class="text-row" style="text-align: right; border-right: 1px SOLID #000000;">
                                <?php
                                    echo db_formatar($nValorRecursoFundeb118_119, "f");
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-row" style="text-align: left; border-left: 1px SOLID #000000;">10 - RESTOS A PAGAR INSCRITOS NO EXERCÍCIO SEM DISPONIBILIDADE FINANCEIRA</td>
                            <td class="text-row" style="text-align: right; border-right: 1px SOLID #000000;">
                            <?php
                                $oReceitaeDespesaEnsino->setDataInicial($dtini);
                                $oReceitaeDespesaEnsino->setDataFinal($dtfim);
                                $oReceitaeDespesaEnsino->setInstits($instits);
                                $dadosLinha9 = $oReceitaeDespesaEnsino->getLinha9RestosaPagarInscritoSemDis();
                                $nRPIncritosSemDesponibilidade101     = $dadosLinha9['0'];
                                $nRPIncritosSemDesponibilidade136     = $dadosLinha9['1'];
                                $nRPIncritosSemDesponibilidade118_119 = $dadosLinha9['2'];
                                $nTotalAplicadoSaida = $nTotalAplicadoSaida + $nRPIncritosSemDesponibilidade101 + $nRPIncritosSemDesponibilidade136 + $nRPIncritosSemDesponibilidade118_119;      
                                echo db_formatar($nRPIncritosSemDesponibilidade101 + $nRPIncritosSemDesponibilidade136 + $nRPIncritosSemDesponibilidade118_119, "f");
                        ?>
                        </td>
                        </tr>
                        <tr>
                            <td class="text-row" style="text-align: left; border-left: 1px SOLID #000000; padding-left: 20px;">10.1 - RECURSOS DE IMPOSTOS (8.1 - 17.1)</td>
                            <td class="text-row" style="text-align: right; border-right: 1px SOLID #000000;">
                            <?php
                            $nRPIncritosSemDesponibilidade101 = 0;
                            $oReceitaeDespesaEnsino->setFontes($aFonte);
                            $oReceitaeDespesaEnsino->setDataInicial($dtini);
                            $oReceitaeDespesaEnsino->setDataFinal($dtfim);
                            $oReceitaeDespesaEnsino->setInstits($instits);
                            $oReceitaeDespesaEnsino->setTipo('lqd');
                            $nLiqAPagar101 = $oReceitaeDespesaEnsino->getEmpenhosApagar();
                            $oReceitaeDespesaEnsino->setTipo('');
                            $nNaoLiqAPagar101 = $oReceitaeDespesaEnsino->getEmpenhosApagar();
                            $aTotalPago101 = $nLiqAPagar101 + $nNaoLiqAPagar101;
                             
                            $dtfimExercicio = db_getsession("DB_anousu")."-12-31";
                            if($dtfim == $dtfimExercicio){
                                $nRPSemDesponibilidade101 = $dadosLinha9['3'];
                                    
                                if($nRPSemDesponibilidade101 <= 0){
                                    $nRPIncritosSemDesponibilidade101 = $aTotalPago101;
                                }
                                if($nRPSemDesponibilidade101 > 0){
                                    $nRPIncritosSemDesponibilidade101 = $nRPSemDesponibilidade101 - $aTotalPago101;
                                    if($nRPIncritosSemDesponibilidade101 >=0){
                                        $nRPIncritosSemDesponibilidade101 = 0;
                                    }
                                    $nRPIncritosSemDesponibilidade101 = abs($nRPIncritosSemDesponibilidade101);
                                }
                            }
                        ?>
                             <?php echo db_formatar($nRPIncritosSemDesponibilidade101,"f"); ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-row" style="text-align: left; border-left: 1px SOLID #000000; padding-left: 20px;">10.2 - RECUSOS DO AUXÍLIO FINANCEIRO - OUTORGA CRÉDITO TRIBUTÁRIO ICMS - ART. 5º, INCISO V, EC Nº 123/2022 (8.2 - 17.2)</td>
                            <td class="text-row" style="text-align: right; border-right: 1px SOLID #000000;">
                            <?php
                            $nRPIncritosSemDesponibilidade136 = 0;
                            $oReceitaeDespesaEnsino->setFontes($aFonte2);
                            $oReceitaeDespesaEnsino->setDataInicial($dtini);
                            $oReceitaeDespesaEnsino->setDataFinal($dtfim);
                            $oReceitaeDespesaEnsino->setInstits($instits);
                            $oReceitaeDespesaEnsino->setTipo('lqd');
                            $nLiqAPagar136 = $oReceitaeDespesaEnsino->getEmpenhosApagar();
                            $oReceitaeDespesaEnsino->setTipo('');
                            $nNaoLiqAPagar136 = $oReceitaeDespesaEnsino->getEmpenhosApagar($aFonte2, $dtini, $dtfim, $instits, '');
                            $aTotalPago136 = $nLiqAPagar136 + $nNaoLiqAPagar136;
                            
                        if($dtfim == $dtfimExercicio){
                            $dtfimExercicio = db_getsession("DB_anousu")."-12-31";
                            $nRPSemDesponibilidade136 = $dadosLinha9['4'];                             
                            if($nRPSemDesponibilidade136 <= 0){
                                $nRPIncritosSemDesponibilidade136 = $aTotalPago136;
                            }
                            if($nRPSemDesponibilidade136 > 0){
                                $nRPIncritosSemDesponibilidade136 = $nRPSemDesponibilidade136 - $aTotalPago136;
                                if($nRPIncritosSemDesponibilidade136 >=0){
                                    $nRPIncritosSemDesponibilidade136 = 0;
                                }
                                $nRPIncritosSemDesponibilidade136 = abs($nRPIncritosSemDesponibilidade136);
                            }
                         }
                            ?>
                             <?php echo db_formatar($nRPIncritosSemDesponibilidade136,"f"); ?>
                        </td>
                        </tr>
                        <tr>
                            <td class="text-row" style="text-align: left; border-left: 1px SOLID #000000; padding-left: 20px;">10.3 - RECURSOS NÃO VINCULADOS DA COMPENSAÇÃO DE IMPOSTOS - ARTIGO 9º DA LC 141/2012</td>
                            <td class="text-row" style="text-align: right; border-right: 1px SOLID #000000;">
                            <?php
                            $nRPIncritosSemDesponibilidade118_119 = 0;
                            $oReceitaeDespesaEnsino->setFontes(array("'15020001','25020001'"));
                            $oReceitaeDespesaEnsino->setDataInicial($dtini);
                            $oReceitaeDespesaEnsino->setDataFinal($dtfim);
                            $oReceitaeDespesaEnsino->setInstits($instits);
                            $oReceitaeDespesaEnsino->setTipo('lqd');
                            $nLiqAPagar118_119 = $oReceitaeDespesaEnsino->getEmpenhosApagar();
                            $oReceitaeDespesaEnsino->setTipo('');
                            $nNaoLiqAPagar118_119 = $oReceitaeDespesaEnsino->getEmpenhosApagar();
                            $aTotalPago118_119= $nLiqAPagar118_119 + $nNaoLiqAPagar118_119;

                            
                        if($dtfim == $dtfimExercicio){                       
                            $dtfimExercicio = db_getsession("DB_anousu")."-12-31";
                            $nRPSemDesponibilidade118_119 =  $dadosLinha9['5']; 
                                                  
                            if($nRPSemDesponibilidade118_119 <= 0){
                                $nRPIncritosSemDesponibilidade118_119 = $aTotalPago118_119;
                            }
                            if($nRPSemDesponibilidade118_119 > 0){
                                $nRPIncritosSemDesponibilidade118_119 = $nRPSemDesponibilidade118_119 - $aTotalPago118_119;
                                if($nRPIncritosSemDesponibilidade118_119 >=0){
                                    $nRPIncritosSemDesponibilidade118_119 = 0;
                                }
                                $nRPIncritosSemDesponibilidade118_119 = abs($nRPIncritosSemDesponibilidade118_119);
                            }

                         }
                            ?>
                             <?php echo db_formatar($nRPIncritosSemDesponibilidade118_119,"f"); ?>
                        </td>
                        </tr>
                        <tr>
                            <td class="text-row" style="text-align: left; border-left: 1px SOLID #000000;">11 - RECEITAS DO FUNDEB NÃO UTILIZADAS NO EXERCÍCIO, EM VALOR SUPERIOR A 10 %</td>
                            <td class="text-row" style="text-align: right; border-right: 1px SOLID #000000;">
                                <?php
                                    $nTotalDespesaLiquidaFundeb = 0;
                                    foreach ($aSubFuncoes as $iSubFuncao) {
                                        $oReceitaeDespesaEnsino->setSubFuncao($iSubFuncao);
                                        $oReceitaeDespesaEnsino->setFuncao($sFuncao);
                                        $oReceitaeDespesaEnsino->setFonteFundeb(array("'15400007', '15400000'"));
                                        $oReceitaeDespesaEnsino->setInstits($instits);
                                        $dadoslinha3 =  $oReceitaeDespesaEnsino->getLinha1FuncaoFundeb();
                                        $aDespesasProgramas = $dadoslinha3['1'];
                                        if (count($aDespesasProgramas) > 0) {
                                            $nTotalDespesaLiquidaFundeb += $dadoslinha3['5']; 
                                        }
                                    }
                                    $dadosLinha11 = ($nTotalReceitasRecebidasFundeb * 0.1) - ($nTotalReceitasRecebidasFundeb - $nTotalDespesaLiquidaFundeb);
                                    if ($dadosLinha11 < 0 ) {
                                        $dadosLinha11 = abs($dadosLinha11);
                                    } else {
                                        $dadosLinha11 = 0;
                                    }        
                                    $nTotalAplicadoSaida = $nTotalAplicadoSaida + $dadosLinha11;                            
                                    echo db_formatar($dadosLinha11, "f");                                 
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-row" style="text-align: left; border-left: 1px SOLID #000000;">12 - CANCELAMENTO, NO EXERCÍCIO, DE RESTOS A PAGAR INSCRITOS COM DISPONIBILIDADE FINANCEIRA</td>
                            <td class="text-row" style="text-align: right; border-right: 1px SOLID #000000;">
                                <?php
                                    $oReceitaeDespesaEnsino->setDataInicial($dtini);
                                    $oReceitaeDespesaEnsino->setDataFinal($dtfim);
                                    $oReceitaeDespesaEnsino->setInstits($instits);
                                    $dadosLinha12 = $oReceitaeDespesaEnsino->getLinha11CancelamentodeRestoaPagar();
                                    $nValorRecursoTotal101  = $dadosLinha12['0'];
                                    $nValorRecursoTotal136  = $dadosLinha12['1'];
                                    $nValorRecursoTotal118  = $dadosLinha12['2'];
                                    $nValorRecursoTotal1502 = $dadosLinha12['3'];
                                    $nTotalAplicadoSaida = $nTotalAplicadoSaida + $nValorRecursoTotal101 + $nValorRecursoTotal136 + $nValorRecursoTotal118 + $nValorRecursoTotal1502;
                                    echo db_formatar($nValorRecursoTotal101 + $nValorRecursoTotal136 + $nValorRecursoTotal118 + $nValorRecursoTotal1502, "f");                                 
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-row" style="text-align: left; border-left: 1px SOLID #000000; padding-left: 20px;">12.1 - RECURSOS DE IMPOSTOS</td>
                            <td class="text-row" style="text-align: right; border-right: 1px SOLID #000000;">
                                <?php
                                    echo db_formatar($nValorRecursoTotal101, "f");
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-row" style="text-align: left; border-left: 1px SOLID #000000; padding-left: 20px;">12.2 - RECUSOS DO AUXÍLIO FINANCEIRO - OUTORGA CRÉDITO TRIBUTÁRIO ICMS - ART. 5º, INCISO V, EC Nº 123/2022 </td>
                            <td class="text-row" style="text-align: right; border-right: 1px SOLID #000000;">
                                <?php
                                    echo db_formatar($nValorRecursoTotal136, "f");
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-row" style="text-align: left; border-left: 1px SOLID #000000; padding-left: 20px;">12.3 - RECURSOS NÃO VINCULADOS DA COMPENSAÇÃO DE IMPOSTOS - ARTIGO 9º DA LC 141/2012</td>
                            <td class="text-row" style="text-align: right; border-right: 1px SOLID #000000;">
                                <?php
                                    echo db_formatar($nValorRecursoTotal123, "f");
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-row" style="text-align: left; border-left: 1px SOLID #000000; padding-left: 20px;">12.4 - RECURSOS DO FUNDEB</td>
                            <td class="text-row" style="text-align: right; border-right: 1px SOLID #000000;">
                                <?php
                                    echo db_formatar($nValorRecursoTotal118, "f");
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="subtitle-row" style="width: 300px;">13 - TOTAL APLICADO ((5 + 6 + 7 + 8 + 9) - ( 10 + 11 + 12))</td>
                            <td class="subtitle-row" style="width: 100px; text-align: right;">
                                <?php
                                    echo db_formatar($nTotalAplicadoEntrada - $nTotalAplicadoSaida, "f");
                                ?>
                            </td>
                        </tr>
                    </tbody>
                </table>
            <table class="waffle" width="600px" cellspacing="0" cellpadding="0" style="border: 1px #000; margin-top: 50px;" autosize="1">
                    <tbody>
                        <tr>
                            <td class="title-row" colspan="5">IV - RESULTADO LÍQUIDO DAS TRANSFERÊNCIAS DO FUNDEB</td>
                        </tr>
                        <tr>
                            <td class="subtitle-row" style="width: 300px; text-align: center;">DESCRIÇÃO</td>
                            <td class="subtitle-row" style="width: 100px; text-align: center;">VALOR</td>
                        </tr>
                        <tr>
                            <td class="subtitle-4-row" style="text-align: lefth; border-left: 1px SOLID #000000; width: 300px;">14 - RECEITAS RECEBIDAS DO FUNDEB NO EXERCÍCIO</td>
                            <td class="subtitle-4-row" style="text-align: right; border-right: 1px SOLID #000000;">
                                <?php echo db_formatar(abs($nTotalReceitasRecebidasFundeb), "f");?>
                            </td>
                        </tr>
                        <tr>
                           <td class='text-row' style='text-align: lefth; border-left: 1px SOLID #000000; width: 300px; padding-left: 20px;'>14.1 - TRANSFERÊNCIAS DE RECURSOS DO FUNDEB (NR 1.7.5.1.50.0.1)</td>
                           <td class="text-row" style="text-align: right; border-right: 1px SOLID #000000;"><?php echo db_formatar($nTransferenciaRecebidaFundeb, "f"); ?></td>
                        </tr>
                        <tr>
                            <td class="text-row" style="text-align: lefth; border-left: 1px SOLID #000000; width: 300px; padding-left: 20px;">14.2 - DEVOLUÇÃO DE RECURSOS DO FUNDEB, RECEBIDOS EM ATRASOS, PARA AS CONTAS DE ORIGEM DOS RECURSOS (CONSULTA 1.047.710)</td>
                            <td class="text-row" style="text-align: right; border-right: 1px SOLID #000000;"><?php echo db_formatar($nDevolucaoRecursoFundeb, "f"); ?></td>
                        </tr>
                        <tr>
                            <td class="subtitle-4-row" style="text-align: lefth; border-left: 1px SOLID #000000; width: 300px;">15 - CONTRIBUIÇÃO AO FUNDEB (LEI Nº 14.113/2020) </td>
                            <td class="subtitle-4-row" style="text-align: right; border-right: 1px SOLID #000000;"><?php
                                echo db_formatar(abs($nTotalContribuicaoFundeb), "f");
                            ?></td>
                        </tr>
                        <tr>
                            <td class="text-row" style="text-align: lefth; border-left: 1px SOLID #000000; width: 300px; padding-left: 20px;">15.1 - COTA-PARTE FPM</td>
                            <td class="text-row" style="text-align: right; border-right: 1px SOLID #000000;"><?php
                                $aReceitas = getSaldoReceita(null, "sum(saldo_arrecadado_acumulado) as saldo_arrecadado_acumulado", null, "o57_fonte like '495171151%'");
                                $nReceita = count($aReceitas) > 0 ? $aReceitas[0]->saldo_arrecadado_acumulado : 0;
                                $nContribuicaoFundeb += abs($nReceita);
                                echo db_formatar(abs($nReceita), "f"); ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-row" style="text-align: lefth; border-left: 1px SOLID #000000; width: 300px; padding-left: 20px;">15.2 - COTA-PARTE ICMS</td>
                            <td class="text-row" style="text-align: right; border-right: 1px SOLID #000000;"><?php
                                $aReceitas = getSaldoReceita(null, "sum(saldo_arrecadado_acumulado) as saldo_arrecadado_acumulado", null, "o57_fonte like '495172150%'");
                                $nReceita = count($aReceitas) > 0 ? $aReceitas[0]->saldo_arrecadado_acumulado : 0;
                                $nContribuicaoFundeb += abs($nReceita);
                                echo db_formatar(abs($nReceita), "f"); ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-row" style="text-align: lefth; border-left: 1px SOLID #000000; width: 300px; padding-left: 20px;">15.3 - COTA-PARTE IPI - EXPORTAÇÃO</td>
                            <td class="text-row" style="text-align: right; border-right: 1px SOLID #000000;"><?php
                                $aReceitas = getSaldoReceita(null, "sum(saldo_arrecadado_acumulado) as saldo_arrecadado_acumulado", null, "o57_fonte like '495172152%'");
                                $nReceita = count($aReceitas) > 0 ? $aReceitas[0]->saldo_arrecadado_acumulado : 0;
                                $nContribuicaoFundeb += abs($nReceita);
                                echo db_formatar(abs($nReceita), "f"); ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-row" style="text-align: lefth; border-left: 1px SOLID #000000; width: 300px; padding-left: 20px;">15.4 - COTA-PARTE ITR</td>
                            <td class="text-row" style="text-align: right; border-right: 1px SOLID #000000;"><?php
                                $aReceitas = getSaldoReceita(null, "sum(saldo_arrecadado_acumulado) as saldo_arrecadado_acumulado", null, "o57_fonte like '495171152%'");
                                $nReceita = count($aReceitas) > 0 ? $aReceitas[0]->saldo_arrecadado_acumulado : 0;
                                $nContribuicaoFundeb += abs($nReceita);
                                echo db_formatar(abs($nReceita), "f"); ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-row" style="text-align: lefth; border-left: 1px SOLID #000000; width: 300px; padding-left: 20px;">15.5 - COTA-PARTE IPVA</td>
                            <td class="text-row" style="text-align: right; border-right: 1px SOLID #000000;"><?php
                                $aReceitas = getSaldoReceita(null, "sum(saldo_arrecadado_acumulado) as saldo_arrecadado_acumulado", null, "o57_fonte like '495172151%'");
                                $nReceita = count($aReceitas) > 0 ? $aReceitas[0]->saldo_arrecadado_acumulado : 0;
                                $nContribuicaoFundeb += abs($nReceita);
                                echo db_formatar(abs($nReceita), "f"); ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-row" style="text-align: lefth; border-left: 1px SOLID #000000; width: 300px; padding-left: 20px;">15.6 - AUXÍLIO FINANCEIRO - OUTORGA CRÉDITO TRIBUTÁRIO ICMS - ART. 5º, INCISO V, EC Nº123/2022 - PRINCIPAL</td>
                            <td class="text-row" style="text-align: right; border-right: 1px SOLID #000000;"><?php
                                $aReceitas = getSaldoReceita(null, "sum(saldo_arrecadado_acumulado) as saldo_arrecadado_acumulado", null, "o57_fonte like '49517196101%'");
                                $nReceita = count($aReceitas) > 0 ? $aReceitas[0]->saldo_arrecadado_acumulado : 0;
                                $nContribuicaoFundeb += abs($nReceita);
                                echo db_formatar(abs($nReceita), "f"); ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="subtitle-row" style="width: 300px;">16 - TOTAL DO RESULTADO LÍQUIDO DAS TRANSFERÊNCIAS DO FUNDEB ( 14 - 15 )</td>
                            <td class="subtitle-row" style="width: 100px; text-align: right;">
                                <?php
                                    $resultadoLiquido =  $nTotalReceitasRecebidasFundeb - $nContribuicaoFundeb;
                                    if($resultadoLiquido > 0) {
                                        echo db_formatar(abs($resultadoLiquido), "f");
                                    }else{
                                        echo "(".db_formatar(abs($resultadoLiquido), "f")." )";
                                    }

                                ?>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </body>
</html>

<?php

$html = ob_get_contents();
ob_end_clean();

$mPDF->WriteHTML(utf8_encode($html));
$mPDF->Output();

/* ---- */


db_query("drop table if exists work_dotacao");
db_query("drop table if exists work_receita");

db_fim_transacao();
