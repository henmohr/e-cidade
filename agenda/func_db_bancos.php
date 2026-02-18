<?php
require("libs/db_stdlib.php");
require("libs/db_conecta.php");
include("libs/db_sessoes.php");
include("libs/db_usuariosonline.php");
include("dbforms/db_funcoes.php");
include("classes/db_db_bancos_classe.php");
db_postmemory($_POST);
$legacyQueryParams = [];
parse_str((string) ($_SERVER["QUERY_STRING"] ?? ""), $legacyQueryParams);
foreach ($legacyQueryParams as $legacyKey => $legacyValue) {
  if (!isset($$legacyKey)) {
    $$legacyKey = $legacyValue;
  }
}
$cldb_bancos = new cl_db_bancos;
$cldb_bancos->rotulo->label("db90_codban");
$cldb_bancos->rotulo->label("db90_descr");
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="estilos.css" rel="stylesheet" type="text/css">
<script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
</head>
<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<table height="100%" border="0"  align="center" cellspacing="0" bgcolor="#CCCCCC">
  <tr> 
    <td height="63" align="center" valign="top">
        <table width="35%" border="0" align="center" cellspacing="0">
	     <form name="form2" method="post" action="" >
          <tr> 
            <td width="4%" align="right" nowrap title="<?=$Tdb90_codban?>">
              <?=$Ldb90_codban?>
            </td>
            <td width="96%" align="left" nowrap> 
              <?php
		       db_input("db90_codban",10,$Idb90_codban,true,"text",4,"","chave_db90_codban");
		       ?>
            </td>
          </tr>
          <tr> 
            <td width="4%" align="right" nowrap title="<?=$Tdb90_descr?>">
              <?=$Ldb90_descr?>
            </td>
            <td width="96%" align="left" nowrap> 
              <?php
		       db_input("db90_descr",40,$Idb90_descr,true,"text",4,"","chave_db90_descr");
		       ?>
            </td>
          </tr>
          <tr> 
            <td colspan="2" align="center"> 
              <input name="pesquisar" type="submit" id="pesquisar2" value="Pesquisar"> 
              <input name="limpar" type="reset" id="limpar" value="Limpar" >
              <input name="Fechar" type="button" id="fechar" value="Fechar" onClick="parent.db_iframe_db_bancos.hide();">
             </td>
          </tr>
        </form>
        </table>
      </td>
  </tr>
  <tr> 
    <td align="center" valign="top"> 
      <?php
      if(!isset($pesquisa_chave)){
        if(isset($campos)==false){
           if(file_exists("funcoes/db_func_db_bancos.php")==true){
             include("funcoes/db_func_db_bancos.php");
           }else{
           $campos = "db_bancos.*";
           }
        }
        if(isset($chave_db90_codban) && (trim($chave_db90_codban)!="") ){
	         $sql = $cldb_bancos->sql_query($chave_db90_codban,$campos,"db90_codban");
        }else if(isset($chave_db90_descr) && (trim($chave_db90_descr)!="") ){
	         $sql = $cldb_bancos->sql_query("",$campos,"db90_descr"," db90_descr like '$chave_db90_descr%' ");
        }else{
           $sql = $cldb_bancos->sql_query("",$campos,"db90_codban","");
        }
        db_lovrot($sql,15,"()","",$funcao_js);
      }else{
        if($pesquisa_chave!=null && $pesquisa_chave!=""){
          $result = $cldb_bancos->sql_record($cldb_bancos->sql_query($pesquisa_chave));
          if($cldb_bancos->numrows!=0){
            db_fieldsmemory($result,0);
            echo "<script>".$funcao_js."('$db90_descr',false);</script>";
          }else{
	         echo "<script>".$funcao_js."('Chave(".$pesquisa_chave.") não Encontrado',true);</script>";
          }
        }else{
	       echo "<script>".$funcao_js."('',false);</script>";
        }
      }
      ?>
     </td>
   </tr>
</table>
</body>
</html>
<?php
if(!isset($pesquisa_chave)){
  ?>
  <script>
  </script>
  <?php
}
?>
