<?php
require("libs/db_stdlib.php");
require("libs/db_conecta.php");
include("libs/db_sessoes.php");
include("libs/db_usuariosonline.php");
include("dbforms/db_funcoes.php");
include("classes/db_db_bancos_classe.php");
$cldb_bancos = new cl_db_bancos;
db_postmemory($_POST);
?>

<html>
<head>
<title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta http-equiv="Expires" CONTENT="0">
<script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>

<script>
function js_emite(){
  jan = window.open('cai2_geratxt002.php?ordem='+document.form1.ordem.value+'&banco='+document.form1.db_bancos.value,'','width='+(screen.availWidth-5)+',height='+(screen.availHeight-40)+',scrollbars=1,location=0 ');
  jan.moveTo(0,0);
}
</script>  
<link href="estilos.css" rel="stylesheet" type="text/css">
</head>
<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onLoad="a=1" bgcolor="#cccccc">
  <table width="790" border="0" cellpadding="0" cellspacing="0" bgcolor="#5786B2">
  <tr>
    <td width="360" height="18">&nbsp;</td>
    <td width="263">&nbsp;</td>
    <td width="25">&nbsp;</td>
    <td width="140">&nbsp;</td>
  </tr>
</table>

  <table  align="center">
    <form name="form1" method="post" action="">
      <tr>
         <td >&nbsp;</td>
         <td >&nbsp;</td>
      </tr>
      <tr>
    <td align='right'>
      <strong>Banco:</strong>
    </td>
    <td>
     <?php
     $arr_bancos = Array();
     $result_bancos = $cldb_bancos->sql_record($cldb_bancos->sql_query_empage(null,"distinct db90_codban,db90_descr","db90_descr"," e90_codmov is null "));
     $numrows_bancos = $cldb_bancos->numrows;
     for($i=0;$i<$numrows_bancos;$i++){
       db_fieldsmemory($result_bancos,$i);
       if($i==0 && !isset($db_bancos)){
         $db_bancos = $db90_codban;
       }
       $arr_bancos[$db90_codban] = $db90_descr;
     }

     $qualdescr = "";
     if(isset($db_bancos) && isset($arr_bancos[$db_bancos])){
       $qualdescr = $arr_bancos[$db_bancos];
     }
     db_select("db_bancos",$arr_bancos,true,1,"onchange='js_reload();'");
     ?>
    </td>

      </tr>
      <tr >
        <td align="right"  >
        <strong>Ordem :&nbsp;&nbsp;</strong>
        </td>
        <td>
	  <?php 
	  $tipo_ordem = array("a"=>"Nome fornecedor","b"=>"CGM fornecedor","c"=>"Recurso");
	  db_select("ordem",$tipo_ordem,true,2); ?>
        </td>
      </tr>
      <tr>
        <td >&nbsp;</td>
        <td >&nbsp;</td>
      </tr>
      <tr>
        <td colspan="2" align = "center"> 
          <input  name="emite2" id="emite2" type="button" value="Processar" onclick="js_emite();" >
        </td>
      </tr>
    </form>
  </table>
<?php
  db_menu(db_getsession("DB_id_usuario"),db_getsession("DB_modulo"),db_getsession("DB_anousu"),db_getsession("DB_instit"));
?>
</body>
</html>
