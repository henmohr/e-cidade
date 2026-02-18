<?php
require("libs/db_stdlib.php");
require("libs/db_conecta.php");
include("libs/db_sessoes.php");
include("libs/db_usuariosonline.php");
include("dbforms/db_funcoes.php");
include("classes/db_empagemod_classe.php");
db_postmemory($_POST);
$legacyQueryParams = [];
parse_str((string) ($_SERVER["QUERY_STRING"] ?? ""), $legacyQueryParams);
foreach ($legacyQueryParams as $legacyKey => $legacyValue) {
  if (!isset($$legacyKey)) {
    $$legacyKey = $legacyValue;
  }
}
$clempagemod = new cl_empagemod;
$clempagemod->rotulo->label("e84_codmod");
$clempagemod->rotulo->label("e84_descr");
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
            <td width="4%" align="right" nowrap title="<?=$Te84_codmod?>">
              <?=$Le84_codmod?>
            </td>
            <td width="96%" align="left" nowrap> 
              <?php
		       db_input("e84_codmod",10,$Ie84_codmod,true,"text",4,"","chave_e84_codmod");
		       ?>
            </td>
          </tr>
          <tr> 
            <td width="4%" align="right" nowrap title="<?=$Te84_descr?>">
              <?=$Le84_descr?>
            </td>
            <td width="96%" align="left" nowrap> 
              <?php
		       db_input("e84_descr",10,$Ie84_descr,true,"text",4,"","chave_e84_descr");
		       ?>
            </td>
          </tr>
          <tr> 
            <td colspan="2" align="center"> 
              <input name="pesquisar" type="submit" id="pesquisar2" value="Pesquisar"> 
              <input name="limpar" type="reset" id="limpar" value="Limpar" >
              <input name="Fechar" type="button" id="fechar" value="Fechar" onClick="parent.db_iframe_empagemod.hide();">
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
           if(file_exists("funcoes/db_func_empagemod.php")==true){
             include("funcoes/db_func_empagemod.php");
           }else{
           $campos = "empagemod.*";
           }
        }
        if(isset($chave_e84_codmod) && (trim($chave_e84_codmod)!="") ){
	         $sql = $clempagemod->sql_query($chave_e84_codmod,$campos,"e84_codmod");
        }else if(isset($chave_e84_descr) && (trim($chave_e84_descr)!="") ){
	         $sql = $clempagemod->sql_query("",$campos,"e84_descr"," e84_descr like '$chave_e84_descr%' ");
        }else{
           $sql = $clempagemod->sql_query("",$campos,"e84_codmod","");
        }
        db_lovrot($sql,15,"()","",$funcao_js);
      }else{
        if($pesquisa_chave!=null && $pesquisa_chave!=""){
          $result = $clempagemod->sql_record($clempagemod->sql_query($pesquisa_chave));
          if($clempagemod->numrows!=0){
            db_fieldsmemory($result,0);
            echo "<script>".$funcao_js."('$e84_descr',false);</script>";
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
