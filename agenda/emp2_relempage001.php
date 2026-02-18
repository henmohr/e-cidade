<?php
require("libs/db_stdlib.php");
require("libs/db_conecta.php");
include("libs/db_sessoes.php");
include("libs/db_usuariosonline.php");
include("dbforms/db_funcoes.php");

include("classes/db_empage_classe.php");
$clempage = new cl_empage;

$db_opcao = 1;
$db_botao = false;

$clrotulo = new rotulocampo;
$clrotulo->label("e80_data");
$clrotulo->label("e60_numemp");
$clrotulo->label("e81_codmov");
$clrotulo->label("z01_numcgm");
$clrotulo->label("z01_nome");
$clrotulo->label("e60_codemp");
$clrotulo->label("e50_codord");
$clrotulo->label("e83_codtipo");


?>
<html>
<head>
<title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta http-equiv="Expires" CONTENT="0">
<script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
<link href="estilos.css" rel="stylesheet" type="text/css">
<script>
function js_consultar(){
    query = '1=1';
    obj = document.form1;
    if(obj.e80_codage.value != "" ){
      query +=  "&e80_codage="+obj.e80_codage.value;
      query +=  "&tipo="+obj.tipo.value;
      query +=  "&form="+obj.form.value;
      jan = window.open('emp2_relempage002.php?query='+query,'','width='+(screen.availWidth-5)+',height='+(screen.availHeight-40)+',scrollbars=1,location=0 ');
      jan.moveTo(0,0);
    }else{
      alert("Indique a agenda!");
    }



}
</script>
</head>
<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onLoad="a=1" >
  <table width="790" border="0" cellpadding="0" cellspacing="0" bgcolor="#5786B2">
    <tr>
      <td width="360" height="18">&nbsp;</td>
      <td width="263">&nbsp;</td>
      <td width="25">&nbsp;</td>
      <td width="140">&nbsp;</td>
    </tr>
  </table>
<table width="790" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td height="430" align="left" valign="top" bgcolor="#CCCCCC">
    <center>
      <table>
        <tr>
	  <td>
        <form name="form1" method="post" action="">
	      <br>
         <table>
            <tr>
	       <td class='bordas' align='right'>
	              <b> <?php db_ancora("Agendas","js_empage(true);",$db_opcao);  ?></b>
	       </td>
	       <td><?=db_input('e80_codage',8,@$e80_codage,true,'text',1,"onchange='js_empage(false);'")?></td>
	   </tr>
	   <tr>
	     <td align='right'><b>Tipo : </b>
	     </td>
	        <td align="left" title="<?=$TDBtxt29?>">
	        <strong><?=$LDBtxt29?></strong>
	       <?php
	        $x = array("c"=>"Dados da Conta","e"=>"Dados do Empenho");
	        db_select('tipo',$x,true,4,"");
	       ?>
	        </td>
	   </tr>
	   <tr>
	     <td align='right'>
	        <strong>Impressão por:</strong>
	     </td>
	        <td align="left" title="Forma de impressão">
	       <?php
	        $x = array("t"=>"Conta pagadora","r"=>"Recurso");
	        db_select('form',$x,true,4,"");
	       ?>
	        </td>
	   </tr>
	   <tr>
              <td colspan="2" align="center">
	      <br>
	 	<input name="consultar" type="button" value="Gerar Relatório" onclick="js_consultar();">
	      </td>
            </tr>

	 </table>
       </form>
       </td>
     </tr>
   </table>
    </center>
    </td>
  </tr>
</table>
<?php
db_menu(db_getsession("DB_id_usuario"),db_getsession("DB_modulo"),db_getsession("DB_anousu"),db_getsession("DB_instit"));
?>
</body>
</html>
<script>
function js_empage(mostra){
  if(mostra==true){
    js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_empage','func_empage.php?funcao_js=parent.js_mostra|e80_codage','Pesquisa',true);
  }else{
    codage =  document.form1.e80_codage.value;
    if(codage != ''){
       js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_empage','func_empage.php?pesquisa_chave='+codage+'&funcao_js=parent.js_mostra02','Pesquisa',false);
    }
  }
}
function js_mostra(codage){
  db_iframe_empage.hide();
  document.form1.e80_codage.value =  codage;
}

function js_mostra02(chave,erro){
  if(erro==true){
    document.form1.e80_codage.focus();
    document.form1.e80_codage.value = '';
  }
}


</script>


