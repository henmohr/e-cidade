<?php
require("libs/db_stdlib.php");
require("libs/db_conecta.php");
include("dbforms/db_funcoes.php");

db_postmemory($_POST);
$clrotulo = new rotulocampo;

$data_inicial     = "";
$data_inicial_dia = "";
$data_inicial_mes = "";
$data_inicial_ano = "";
$data_final       = "";
$data_final_dia   = "";
$data_final_mes   = "";
$data_final_ano   = "";

$sDBHintMetadata  = "db-action='hint' db-hint-text='O filtro de período deve estar dentro do mesmo ano.'";

?>
<html>

<head>
    <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title> 
    <meta 
     http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta http-equiv="Expires" CONTENT="0">
    <?php
        db_app::load("estilos.css, grid.style.css");
    ?>
    <script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
    <link href="estilos.css" rel="stylesheet" type="text/css">
</head>


<body bgcolor=#CCCCCC leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onLoad="a=1" bgcolor="#cccccc">
    <table width="790" border="0" cellpadding="0" cellspacing="0" bgcolor="#5786B2">
        <tr>
            <td width="360" height="18">&nbsp;</td>
            <td width="263">&nbsp;</td>
            <td width="25">&nbsp;</td>
            <td width="140">&nbsp;</td>
        </tr>
    </table>

    <div style="margin: 2% 16%;">
        <fieldset>
            <legend>Convênios</legend>
            <table align="center">
                <form name="form1" method="post" action="">
                    <tr>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>

                    <tr>
                        <td>
                            <b style="padding-left: 2.1rem;">Data início Assinatura: </b> 
                            <?php
                                db_inputdata("data_inicial", $dia_inicial, $mes_inicial, $ano_inicial, true, "text", 1, $sDBHintMetadata);
                            ?>

                            <b style="padding-left: 2.04rem;">Data fim Assinatura: </b>
                            <?php
                                db_inputdata("data_final", $dia_final, $mes_final, $ano_final, true, "text", 1, $sDBHintMetadata);
                            ?>
                        </td>

                        <td></td>
                        <td></td>
                    </tr>

                    <tr>
                        <td>
                            <b style="padding-left: 2.04rem;"> Esfera do Concedente: </b>
                            <?php
                                $x = array(""=>"Todas", "1"=>"Federal","2"=>"Estadual","3"=>"Municipal","4"=>"Exterior","5"=> "Institução Privada");
                                db_select('c207_esferaconcedente',$x,true,$db_opcao,'');
                            ?>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <b style="padding-left: 6.9rem;">Formato: </b>
                            <?php  $aFormato = array ("pdf" => "&nbsp;PDF", "csv" => "&nbsp;CSV",);
                                db_select('formato', $aFormato, true, 1, "");
                            ?>                 
                        </td>
                    </tr>

                    <tr>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>

                    <tr>
                        <td align="center" style="padding-left: 13px;">
                            <input name="emite_convenios" id="emite_convenios" type="button" value="Gerar Relatório" onclick="js_emiteRelatorio()">
                        </td>
                    </tr>

                </form>
            </table>
        </fieldset>
    </div>
    <?php
    db_menu(db_getsession("DB_id_usuario"), db_getsession("DB_modulo"), db_getsession("DB_anousu"), db_getsession("DB_instit"));
    ?>
</body>

</html>
<script>

function js_emiteRelatorio() {

    if (js_validaForm()) {

        var sDataInicial     = $F('data_inicial');
        var sDataFinal       = $F('data_final');
        var esferaconcedente = $('c207_esferaconcedente').value;
        var formato          = $('formato').value;
        var sFiltros       = 'sDataInicial='+sDataInicial+'&sDataFinal='+sDataFinal+'&esferaconcedente='+esferaconcedente;

        if(formato == 'pdf') {
            return window.open('con2_convenios_002.php?'+sFiltros, '',
                                'width='+(screen.availWidth-5)+',height='+
                                (screen.availHeight-40)+',scrollbars=1,location=0 ');
        } 

        if(formato == 'csv') {
            return window.open('con2_convenios_003.php?'+sFiltros, '',
                                'width='+(screen.availWidth-5)+',height='+
                                (screen.availHeight-40)+',scrollbars=1,location=0 ');
        }
    }
}

function js_validaForm() {

    if ($('data_inicial').value.trim() !== "") {

        if ($('data_final').value.trim() !== "" && (js_comparadata($F('data_inicial'), $F('data_final'), ">"))) {

            alert("Data final da vigência deve ser posterior à Data de assinatura.");
            return false;
        } 
            
        $('data_final').value = $('data_inicial').value;
    }

    return true;
}

var dataInicial = document.getElementById('data_inicial');
var dataFinal = document.getElementById('data_final');
var esfera = document.getElementById('c207_esferaconcedente'); 
var formato = document.getElementById('formato'); 

dataInicial.size = '8';
dataFinal.size = '8';
esfera.style.width = '21.5rem';
formato.style.width = '21.5rem';

</script>