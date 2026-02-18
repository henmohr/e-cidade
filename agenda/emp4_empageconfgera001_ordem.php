<?php
require("libs/db_stdlib.php");
require("libs/db_conecta.php");
include("libs/db_sessoes.php");
include("libs/db_usuariosonline.php");
include("dbforms/db_funcoes.php");

include("classes/db_pagordem_classe.php");
include("classes/db_pagordemconta_classe.php");
include("classes/db_empagetipo_classe.php");
include("classes/db_empord_classe.php");
include("classes/db_empagemov_classe.php");
include("classes/db_empagemovconta_classe.php");
include("classes/db_empagepag_classe.php");
include("classes/db_pcfornecon_classe.php");
include("classes/db_empageforma_classe.php");
include("classes/db_empagemovforma_classe.php");

$clempagetipo = new cl_empagetipo;
$clpagordem   = new cl_pagordem;
$clpagordemconta   = new cl_pagordemconta;
$clempord     = new cl_empord;
$clempagemov  = new cl_empagemov;
$clempagemovconta  = new cl_empagemovconta;
$clempagepag  = new cl_empagepag;
$clpcfornecon  = new cl_pcfornecon;
$clempageforma = new cl_empageforma;
$clempagemovforma = new cl_empagemovforma;

//echo ($_SERVER["QUERY_STRING"]);
$legacyQueryParams = [];
parse_str((string) ($_SERVER["QUERY_STRING"] ?? ""), $legacyQueryParams);
foreach ($legacyQueryParams as $legacyKey => $legacyValue) {
  if (!isset($$legacyKey)) {
    $$legacyKey = $legacyValue;
  }
}
//db_postmemory($_POST);
$db_opcao = 1;
$db_botao = false;

$clrotulo = new rotulocampo;
$clrotulo->label("e50_codord");
$clrotulo->label("o15_descr");
$clrotulo->label("o15_codigo");
$clrotulo->label("e60_numemp");
$clrotulo->label("e60_codemp");
$clrotulo->label("e60_emiss");
$clrotulo->label("z01_numcgm");
$clrotulo->label("z01_nome");
$clrotulo->label("e80_codage");
$clrotulo->label("e83_codtipo");
$clrotulo->label("e53_valor");
$clrotulo->label("e53_vlranu");
$clrotulo->label("e53_vlrpag");
$clrotulo->label("e96_codigo");


//$dbwhere = "(e53_valor-e53_vlranu-e53_vlrpag > 0 )";

$sql03="
       ";
/*
         select e82_codord from empage
		inner join empagemov   on e81_codage = e80_codage 
		inner join empord      on e82_codmov = e81_codmov
		inner join empageconf  on e86_codmov = e81_codmov 
*/

$dbwhere = "";
if(isset($db_banco) && trim($db_banco)!=""){
  $dbwhere = " and c63_banco='$db_banco'";
}

$sql    = $clempagemov->sql_query_txt(null,"pc63_conta,pc63_agencia,e80_codage,e50_codord,e50_data,e82_codord,o15_descr,e81_codmov,e83_codtipo,e83_descr,e60_emiss,e60_numemp,e60_codemp,e82_codord,z01_numcgm,z01_nome,e81_valor","","e90_codmov is null $dbwhere");

//echo($sql);

$result09 = $clpagordem->sql_record($sql); 
$numrows09= $clpagordem->numrows; 

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
<script>
function js_marca(obj){ 
   var OBJ = document.form1;
   for(i=0;i<OBJ.length;i++){
     if(OBJ.elements[i].type == 'checkbox' && OBJ.elements[i].disabled==false){
       OBJ.elements[i].checked = !(OBJ.elements[i].checked == true);
     }
   }
   return false;
}
function js_confere(campo){
	erro     = false;
	erro_msg = '';

	vlrgen= new Number(campo.value);
        
	
	if(isNaN(vlrgen)){
	    erro = true;
	}
	nome = campo.name.substring(6);
	
	vlrlimite = new Number(eval("document.form1.disponivel_"+nome+".value"));
	if(vlrgen > vlrlimite){
	  erro_msg = "Valor digitado é maior do que o disponível!";
	  erro=true;
	}  
        
        if(vlrgen == ''){
	   eval("document.form1."+campo.name+".value = '0.00';");
        }
        if(vlrgen == 0){
	  eval("document.form1.CHECK_"+nome+".checked=false");
	}else{
	  eval("document.form1.CHECK_"+nome+".checked=true");
	}
	
	if(erro==false){
	   eval("document.form1."+campo.name+".value = vlrgen.toFixed(2);");
	}else{  
	   if(erro_msg != ''){
	     //alert(erro_msg);
	   }
	   eval("document.form1."+campo.name+".focus()");
	   eval("document.form1."+campo.name+".value = vlrlimite.toFixed(2);");
	   return false;
	}  
}

function js_padrao(val){
   var OBJ = document.form1;
   for(i=0;i<OBJ.length;i++){
     nome = OBJ.elements[i].name;
     tipo = OBJ.elements[i].type;
     if( tipo.substr(0,6) == 'select' && nome.substr(0,11)=='e83_codtipo'){
       ord = nome.substr(12);
       checa = eval("document.form1.CHECK_"+ord+".checked");
       if(checa==false){
	 continue;
       } 
      for(q=0; q<OBJ.elements[i].options.length; q++){
	if(OBJ.elements[i].options[q].value==val){
	   OBJ.elements[i].options[q].selected=true;
	   break;
	 }
      }
    }
   }
}
</script>
</head>
<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onLoad="a=1" >
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr> 
    <td height="100%" align="left" valign="top" bgcolor="#CCCCCC"> 
<form name="form1" method="post" action="">
    <center>
      <?php
      if($numrows09){
      ?>
      <table  class='bordas'>
        <tr>
          <td class='bordas02' align='center'><a  title='Inverte Marcação' href='' onclick='return js_marca(this);return false;'>M</a></td>
          <td class='bordas02' align='center'><b><?=$RLe60_codemp?></b></td>
          <td class='bordas02' align='center'><b><?=$RLe50_codord?></b></td>
          <td class='bordas02' align='center'><b>Conta pagadora</b></td>
          <td class='bordas02' align='center'><b>Recurso</b></td>
          <td class='bordas02' align='center'><b><?=$RLz01_nome?></b></td>
          <td class='bordas02' align='center'><b>Cód. Pgto.</b></td>
          <td class='bordas02' align='center' nowrap><b><?php/*=$RLe60_emiss*/?>Banco - Agência - Conta (credor)</b></td>
          <td class='bordas02' align='center' nowrap><b>Valor a pagar</b></td>
          <td class='bordas02' align='center'><b><?=$RLe80_codage?></b></td>
	</tr>
        <?php
	  $valortotal = 0;
	  $arr_valtipo = Array();
	  /*
	  for($i=0; $i<$numrows09; $i++){
	    db_fieldsmemory($result09,$i);
	    $valortotal += $e81_valor;
	    if(!isset($arr_valtipo[$e83_codtipo])){
	      $arr_valtipo[$e83_codtipo] = 0;
	    }
	    $arr_valtipo[$e83_codtipo] += $e81_valor;
	  }
	  */
	  for($i=0; $i<$numrows09; $i++){
	    db_fieldsmemory($result09,$i);
	    $result_movconta = $clempagemovconta->sql_record($clempagemovconta->sql_query_conta($e81_codmov,"pc63_banco as banco,pc63_agencia as agencia,pc63_agencia_dig as digito,pc63_conta as conta,pc63_conta_dig as digitoc "));
	    $numrows_movconta = $clempagemovconta->numrows;
	    if($numrows_movconta>0){
	      db_fieldsmemory($result_movconta,0);
              if(trim($digito)!=""){
		$digito = "/$digito";
	      }
              if(trim($digitoc)!=""){
		$digitoc = "/$digitoc";
	      }
	    }
	?>
        <tr>
          <td class='bordas' align='right' ><input value="<?=$e81_codmov?>" checked name="<?=$e81_codmov?>" type='checkbox' ></td>
          <td class='bordas' title='<?=($RLe60_codemp)?> - Data de emissão:<?=db_formatar($e60_emiss,"d")?>'><?=$e60_numemp?></td>
          <td class='bordas' title='<?=($RLe50_codord)?> - Data de emissão:<?=db_formatar($e50_data,"d")?>'><?=$e50_codord?></td>
          <td class='bordas' title='Conta pagadora' align='left' nowrap><?=($e83_descr)?></td>
          <td class='bordas' title='Recurso' align='right'><?=$o15_descr?></td>
          <td class='bordas' title='<?=($RLz01_nome)?> -  Numcgm:<?=$z01_numcgm?>'><?=$z01_nome?></td>
	  <?php
	  if(trim($db_banco)==trim($banco)){
	    $codigopagamento = "DEP";
	  }else if($e81_valor<5000){
	    $codigopagamento = "DOC";
	  }else{
	    $codigopagamento = "TED";
	  }
	  ?>
          <td class='bordas' title='Código de pagamento' align='center' nowrap><b><?=($codigopagamento)?></b></td>
          <td class='bordas' title='Banco - Agência - Conta (credor)' align='left' nowrap><?=($banco)?> - <?=($agencia.$digito)?> - <?=($conta.$digitoc)?></td>
          <td class='bordas' title='Valor a pagar' align='right'><?=db_formatar($e81_valor,"f")?> </td>
	  <?php
	  /*
	  $valores = "val_".$e81_codmov."_".$e83_codtipo;
	  $$valores = $e81_valor;
	  db_input($valores,5,0,true,'text',3);
	  */ 
	  ?>
          <td class='bordas' title='<?=($RLe80_codage)?>' align='center'><?=($e80_codage)?></td>
	</tr>
        <?php
	  }
	  ?>
      </table>
      <?php
      }else{
      ?>
      <BR><BR><BR><BR><BR><BR>
      <table>
	<tr>
	  <td nowrap align='center' width='100%' height='100%'>
	    <b>Nenhum registro encontrado</b>
	  </td>
	</tr>
      </table>
      <?php
      }
      ?>
    </center>
    </form>
    </td>
  </tr>
</table>
</body>
</html>
<script>
//onMouseOut='parent.js_valores(false);' onMouseOver="parent.js_valores(true,'<?=$arr_valtipo[$e83_codtipo]?>','<?=$valortotal?>','<?=($e83_descr)?>','<?=($e83_codtipo)?>')";
</script>
