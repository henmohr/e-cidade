<?php
require("libs/db_stdlib.php");
require("libs/db_conecta.php");
include("libs/db_sessoes.php");
include("libs/db_usuariosonline.php");
include("dbforms/db_funcoes.php");
include("classes/db_pagordemconta_classe.php");
db_postmemory($_POST);
$legacyQueryParams = [];
parse_str((string) ($_SERVER["QUERY_STRING"] ?? ""), $legacyQueryParams);
foreach ($legacyQueryParams as $legacyKey => $legacyValue) {
  if (!isset($$legacyKey)) {
    $$legacyKey = $legacyValue;
  }
}
$clpagordemconta = new cl_pagordemconta;
$clpagordemconta->rotulo->label("e49_codord");
$clpagordemconta->rotulo->label("e49_codord");
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
            <td width="4%" align="right" nowrap title="<?=$Te49_codord?>">
              <?=$Le49_codord?>
            </td>
            <td width="96%" align="left" nowrap> 
              <?php
		       db_input("e49_codord",6,$Ie49_codord,true,"text",4,"","chave_e49_codord");
		       ?>
            </td>
          </tr>
          <tr> 
            <td width="4%" align="right" nowrap title="<?=$Te49_codord?>">
              <?=$Le49_codord?>
            </td>
            <td width="96%" align="left" nowrap> 
              <?php
		       db_input("e49_codord",6,$Ie49_codord,true,"text",4,"","chave_e49_codord");
		       ?>
            </td>
          </tr>
          <tr> 
            <td colspan="2" align="center"> 
              <input name="pesquisar" type="submit" id="pesquisar2" value="Pesquisar"> 
              <input name="limpar" type="reset" id="limpar" value="Limpar" >
              <input name="Fechar" type="button" id="fechar" value="Fechar" onClick="parent.db_iframe_pagordemconta.hide();">
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
           if(file_exists("funcoes/db_func_pagordemconta.php")==true){
             include("funcoes/db_func_pagordemconta.php");
           }else{
           $campos = "pagordemconta.*";
           }
        }
        if(isset($chave_e49_codord) && (trim($chave_e49_codord)!="") ){
	         $sql = $clpagordemconta->sql_query($chave_e49_codord,$campos,"e49_codord");
        }else if(isset($chave_e49_codord) && (trim($chave_e49_codord)!="") ){
	         $sql = $clpagordemconta->sql_query("",$campos,"e49_codord"," e49_codord like '$chave_e49_codord%' ");
        }else{
           $sql = $clpagordemconta->sql_query("",$campos,"e49_codord","");
        }
        db_lovrot($sql,15,"()","",$funcao_js);
      }else{
        if($pesquisa_chave!=null && $pesquisa_chave!=""){
          $result = $clpagordemconta->sql_record($clpagordemconta->sql_query($pesquisa_chave));
          if($clpagordemconta->numrows!=0){
            db_fieldsmemory($result,0);
            echo "<script>".$funcao_js."('$e49_codord',false);</script>";
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
