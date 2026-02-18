<?php
require("libs/db_stdlib.php");
require("libs/db_conecta.php");
include("libs/db_sessoes.php");
include("libs/db_usuariosonline.php");
include("dbforms/db_funcoes.php");
include("classes/db_empagegera_classe.php");
include("classes/db_empagetipo_classe.php");
$clempagegera = new cl_empagegera;
$clempagetipo = new cl_empagetipo;
$clrotulo = new rotulocampo;
$clempagegera->rotulo->label();
$clempagetipo->rotulo->label();

db_postmemory($_POST);
?>
<html>
<head>
<title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta http-equiv="Expires" CONTENT="0">
<script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
<link href="estilos.css" rel="stylesheet" type="text/css">
  </head>
  <body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onLoad="document.form1.e87_codgera.focus();" bgcolor="#cccccc">
    <table width="790" border="0" cellpadding="0" cellspacing="0" bgcolor="#5786B2">
      <tr>
	<td width="360" height="18">&nbsp;</td>
	<td width="263">&nbsp;</td>
	<td width="25">&nbsp;</td>
	<td width="140">&nbsp;</td>
      </tr>
    </table>
<center>
<form name="form1" method="post">
<table border='0'>
  <tr height="20px">
    <td ></td>
    <td ></td>
  </tr>
  <tr>
    <td  align="left" nowrap title="<?=$Te87_codgera?>"> <?php db_ancora(@$Le87_codgera,"js_pesquisa_gera(true);",1);?>  </td>
    <td align="left" nowrap>
  <?php
   db_input("e87_codgera",8,$Ie87_codgera,true,"text",4,"onchange='js_pesquisa_gera(false);'");
   db_input("e87_descgera",40,$Ie87_descgera,true,"text",3);
  ?>
    </td>
  </tr>
  <tr>
    <td  align="left" nowrap title="Conta pagadora"> <?php db_ancora("<strong>Conta pagadora:</strong>","",3);?>  </td>
    <td align="left" nowrap>
  <?php
   //die($clempagetipo->sql_query_file(null,"distinct e83_codtipo,e83_descr"));
   $result_empagetipo = $clempagetipo->sql_record($clempagetipo->sql_query_file(null,"distinct e83_codtipo,e83_descr"));
   $db_passapar = "true";
   if($clempagetipo->numrows == 0){
     $db_passapar = "false";
   }

   db_selectrecord("e83_codtipo",$result_empagetipo,true,1,"","","","0");
  ?>
    </td>
  </tr>
  <tr>
    <td colspan="2" align="center"><br>
      <input name="rel" type="button" <?=("onclick='js_gerarel($db_passapar);'")?>  value="Gerar relatório">
      <input name="pes" type="button" onclick='js_OpenJanelaIframe("CurrentWindow.corpo","db_iframe_empagegera","func_empagegera.php?funcao_js=parent.js_mostragera1|e87_codgera|e87_descgera","Pesquisa",true);'  value="Pesquisar arquivos">
    </td>
  </tr>
</table>
</form>
</center>
<?php db_menu(db_getsession("DB_id_usuario"),db_getsession("DB_modulo"),db_getsession("DB_anousu"),db_getsession("DB_instit"));?>
<script>
//--------------------------------
function js_gerarel(x){
  if(x==true){
    obj  = document.form1.e83_codtipodescr;
    e83_codtipodescr = "";
    for(i=0; i<obj.options.length; i++){
      if(obj.options[i].selected == true){
	e83_codtipodescr = obj.options[i].text;
      }
    }
  }

  if(document.form1.e87_codgera.value!="" || document.form1.e83_codtipo.value!=0){
    jan = window.open('emp2_gerarq002.php?e87_codgera='+document.form1.e87_codgera.value+'&e87_descgera='+document.form1.e87_descgera.value+'&e83_codtipo='+document.form1.e83_codtipo.value+'&e83_codtipodescr='+e83_codtipodescr,'','width='+(screen.availWidth-5)+',height='+(screen.availHeight-40)+',scrollbars=1,location=0 ');
  }else{
    alert("Informe o código do arquivo ou selecione o tipo para gerar o relatório.");
  }

}
function js_pesquisa_gera(mostra){
  if(mostra==true){
    js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_empagegera','func_empagegera.php?funcao_js=parent.js_mostragera1|e87_codgera|e87_descgera','Pesquisa',true);
  }else{
     if(document.form1.e87_codgera.value != ''){
        js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_empagegera','func_empagegera.php?pesquisa_chave='+document.form1.e87_codgera.value+'&funcao_js=parent.js_mostragera','Pesquisa',false);
     }else{
       document.form1.e87_descgera.value = '';
     }
  }
}
function js_mostragera(chave,erro){
  document.form1.e87_descgera.value = chave;
  if(erro==true){
    document.form1.e87_codgera.focus();
    document.form1.e87_codgera.value = '';
  }
}
function js_mostragera1(chave1,chave2){
  document.form1.e87_codgera.value = chave1;
  document.form1.e87_descgera.value = chave2;
  db_iframe_empagegera.hide();
//  jan = window.open('emp2_gerarq002.php?e87_codgera='+chave1,'','width='+(screen.availWidth-5)+',height='+(screen.availHeight-40)+',scrollbars=1,location=0 ');
}
//--------------------------------
</script>
</body>
</html>
