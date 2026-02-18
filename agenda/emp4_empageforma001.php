<?php
require("libs/db_stdlib.php");
require("libs/db_conecta.php");
include("libs/db_sessoes.php");
include("libs/db_usuariosonline.php");
include("dbforms/db_funcoes.php");

include("classes/db_empage_classe.php");
include("classes/db_empagetipo_classe.php");
include("classes/db_empagemov_classe.php");
include("classes/db_empagemovconta_classe.php");
include("classes/db_empord_classe.php");
include("classes/db_empagepag_classe.php");
include("classes/db_empageslip_classe.php");
include("classes/db_empagemovforma_classe.php");
include("classes/db_empagegera_classe.php");
include("classes/db_empageconf_classe.php");
include("classes/db_empageconfgera_classe.php");
include("classes/db_conplanoconta_classe.php");
include("classes/db_empagemod_classe.php");

include("classes/db_pagordem_classe.php");
include("classes/db_pagordemconta_classe.php");
include("classes/db_pcfornecon_classe.php");
include("classes/db_empageforma_classe.php");

$empagetipo = new cl_empagetipo;
$clpagordem   = new cl_pagordem;
$clpagordemconta   = new cl_pagordemconta;
$clempord     = new cl_empord;
$clempagemov  = new cl_empagemov;
$clempagemovconta  = new cl_empagemovconta;
$clempagepag  = new cl_empagepag;
$clpcfornecon  = new cl_pcfornecon;
$clempageforma = new cl_empageforma;
$clempagemovforma = new cl_empagemovforma;
$clempage = new cl_empage;
$clempagetipo = new cl_empagetipo;
$clempageslip = new cl_empageslip;
$clempagemovforma = new cl_empagemovforma;
$clempagegera = new cl_empagegera;
$clempageconf = new cl_empageconf;
$clempageconfgera = new cl_empageconfgera;
$clempagemod  = new cl_empagemod;

$legacyQueryParams = [];
parse_str((string) ($_SERVER["QUERY_STRING"] ?? ""), $legacyQueryParams);
foreach ($legacyQueryParams as $legacyKey => $legacyValue) {
  if (!isset($$legacyKey)) {
    $$legacyKey = $legacyValue;
  }
}
db_postmemory($_POST);

$db_opcao = 1;
$db_botao = false;

if(isset($e80_data_ano)){
  $data = "$e80_data_ano-$e80_data_mes-$e80_data_dia";
}

if(isset($atualizar)){
  $sqlerro = false;
  db_inicio_transacao();
  $arr_valores = explode("XX",$ords);
  $movimentoss = "";
  $virgulamovi = "";
  for($i=0;$i<sizeof($arr_valores);$i++){
    $arr_dados = explode("-",$arr_valores[$i]);
    $agenda = $arr_dados[0];
    $aordem = $arr_dados[1];
    $numemp = $arr_dados[2];
    $avalor = $arr_dados[3];
    $agtipo = $arr_dados[4];
    $aforma = $arr_dados[5];
    $aconta = $arr_dados[6];


    $result_movimentos = $clempagemov->sql_record($clempagemov->sql_query_ord(null,"distinct e81_codmov",""," e81_codage=$agenda and e82_codord=$aordem "));
    $numrows_movimentos = $clempagemov->numrows;
    if($numrows_movimentos>0){
      db_fieldsmemory($result_movimentos,0);
      if($sqlerro==false){
	$clempagemov->e81_codmov = $e81_codmov;
	$clempagemov->e81_valor  = $avalor;
	$clempagemov->alterar($e81_codmov);
	if($clempagemov->erro_status==0){
	  $erro_msg = $clempagemov->erro_msg;
	  $sqlerro=true;
	}
      }

      if($sqlerro==false){
	$clempagepag->excluir($e81_codmov);
	if($clempagepag->erro_status==0){
	  $erro_msg = $clempagepag->erro_msg;
	  $sqlerro=true;
	}
      }

      if($agtipo!=0){
	if($sqlerro==false){
	  $clempagepag->incluir($e81_codmov,$agtipo);
	  if($clempagepag->erro_status==0){
	    $erro_msg = $clempagepag->erro_msg;
	    $sqlerro=true;
	  }
	}
      }

      if($sqlerro==false){
	$clempagemovforma->excluir($e81_codmov);
	if($clempagemovforma->erro_status==0){
	  $erro_msg = $clempagemovforma->erro_msg;
	  $sqlerro=true;
	}
      }
      if($aforma!=0){
	if($sqlerro==false){
	  $clempagemovforma->e97_codmov   = $e81_codmov;
	  $clempagemovforma->e97_codforma = $aforma;
	  $clempagemovforma->incluir($e81_codmov);
	  if($clempagemovforma->erro_status==0){
	    $erro_msg = $clempagemovforma->erro_msg;
	    $sqlerro=true;
	  }
	}
      }

      if($sqlerro==false){
	$clempagemovconta->excluir($e81_codmov);
	if($clempagemovconta->erro_status==0){
	  $erro_msg = $clempagemovconta->erro_msg;
	  $sqlerro=true;
	}
      }

      if($aconta!=0 && $aconta!="n"){
	if($sqlerro==false){
	  $clempagemovconta->e98_contabanco = $aconta;
	  $clempagemovconta->incluir($e81_codmov);
	  if($clempagemovconta->erro_status==0){
	    $erro_msg = $clempagemovconta->erro_msg;
	    $sqlerro=true;
	  }
	}
      }
      if($aforma == 3 && $agtipo !=0 && $aconta != 0 && $aconta!="n"){
//	db_msgbox($aforma.' ---- '.$agtipo.' ---- '.$aconta);
        $result = $clempagetipo->sql_record($clempagetipo->sql_query_file($agtipo,'e83_sequencia as tipsequencia'));
        if($clempagetipo->numrows>0){
          db_fieldsmemory($result,0);
	  if($sqlerro==false){
	    $clempageconf->e86_codmov = $e81_codmov;
	    $clempageconf->e86_data   = date("Y-m-d",db_getsession("DB_datausu"));
	    $clempageconf->e86_cheque = $tipsequencia;
	    $clempageconf->incluir($e81_codmov);
	    if($clempageconf->erro_status==0){
	      $erro_msg = $clempageconf->erro_msg;
	      $sqlerro = true;
	    }
	  }
        }
      }
    }
  }

  if(isset($geraragenda) && 1==2){
    if($sqlerro==false){
      $result_facilita  = $clempagemod->sql_record($clempagemod->sql_query_modforma(null,"distinct e81_codmov as codmovimento,e83_sequencia as tipsequencia,e83_conta,e83_codmod,e83_codtipo,c63_banco ","c63_banco","e84_codmod <> 1 and e97_codforma=3 and e86_codmov is null "));
      $numrows_facilita = $clempagemod->numrows;
      $passargera = true;
      $antigobanco = "";
      for($i=0;$i<$numrows_facilita;$i++){
	db_fieldsmemory($result_facilita,$i);
      }
    }
  }
  //$sqlerro = true;
  db_fim_transacao($sqlerro);
}

//quando entra pela primeira vez
if(empty($e80_data_ano)){
  $e80_data_ano = date("Y",db_getsession("DB_datausu"));
  $e80_data_mes = date("m",db_getsession("DB_datausu"));
  $e80_data_dia = date("d",db_getsession("DB_datausu"));
  $data = "$e80_data_ano-$e80_data_mes-$e80_data_dia";
}

if(isset($data)){
    $result01 = $clempage->sql_record($clempage->sql_query_file(null,'e80_codage','',"e80_data='$data'"));
    $numrows01 = $clempage->numrows;
}


?>
<html>
<head>
<title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta http-equiv="Expires" CONTENT="0">
<script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
<link href="estilos.css" rel="stylesheet" type="text/css">
<style>
<?php$cor="#999999"?>
.bordas02{
         border: 2px solid #cccccc;
         border-top-color: <?=$cor?>;
         border-right-color: <?=$cor?>;
         border-left-color: <?=$cor?>;
         border-bottom-color: <?=$cor?>;
         background-color: #999999;
}
.bordas{
         border: 1px solid #cccccc;
         border-top-color: <?=$cor?>;
         border-right-color: <?=$cor?>;
         border-left-color: <?=$cor?>;
         border-bottom-color: <?=$cor?>;
         background-color: #cccccc;
}
</style>
</head>
<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onLoad="a=1" >
<table width="790" border="0" cellspacing="0" cellpadding="0">
  <tr> 
    <td height="450" align="left" valign="top" bgcolor="#CCCCCC"> 
   <?php
	include("forms/db_frmempageforma.php");
   ?>
    </td>
  </tr>
</table>
<?php
db_menu(db_getsession("DB_id_usuario"),db_getsession("DB_modulo"),db_getsession("DB_anousu"),db_getsession("DB_instit"));
?>
</body>
</html>
<?php
if(isset($atualizar) && $sqlerro==true){
  db_msgbox($erro_msg);
}
?>
