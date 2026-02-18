<?php
require("libs/db_stdlib.php");
require("libs/db_conecta.php");
include("libs/db_sessoes.php");
include("libs/db_usuariosonline.php");
include("dbforms/db_funcoes.php");
include("classes/db_empagepag_classe.php");
db_postmemory($_POST);
$legacyQueryParams = [];
parse_str((string) ($_SERVER["QUERY_STRING"] ?? ""), $legacyQueryParams);
foreach ($legacyQueryParams as $legacyKey => $legacyValue) {
  if (!isset($$legacyKey)) {
    $$legacyKey = $legacyValue;
  }
}
$clempagepag = new cl_empagepag;
$clempagepag->rotulo->label("e85_codmov");
$clempagepag->rotulo->label("e85_codtipo");
$clempagepag->rotulo->label("e85_codmov");
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
            <td width="4%" align="right" nowrap title="<?=$Te85_codmov?>">
              <?=$Le85_codmov?>
            </td>
            <td width="96%" align="left" nowrap> 
              <?php
		       db_input("e85_codmov",6,$Ie85_codmov,true,"text",4,"","chave_e85_codmov");
		       ?>
            </td>
          </tr>
          <tr> 
            <td width="4%" align="right" nowrap title="<?=$Te85_codtipo?>">
              <?=$Le85_codtipo?>
            </td>
            <td width="96%" align="left" nowrap> 
              <?php
		       db_input("e85_codtipo",6,$Ie85_codtipo,true,"text",4,"","chave_e85_codtipo");
		       ?>
            </td>
          </tr>
          <tr> 
            <td width="4%" align="right" nowrap title="<?=$Te85_codmov?>">
              <?=$Le85_codmov?>
            </td>
            <td width="96%" align="left" nowrap> 
              <?php
		       db_input("e85_codmov",6,$Ie85_codmov,true,"text",4,"","chave_e85_codmov");
		       ?>
            </td>
          </tr>
          <tr> 
            <td colspan="2" align="center"> 
              <input name="pesquisar" type="submit" id="pesquisar2" value="Pesquisar"> 
              <input name="limpar" type="reset" id="limpar" value="Limpar" >
              <input name="Fechar" type="button" id="fechar" value="Fechar" onClick="parent.db_iframe_empagepag.hide();">
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
           if(file_exists("funcoes/db_func_empagepag.php")==true){
             include("funcoes/db_func_empagepag.php");
           }else{
           $campos = "empagepag.*";
           }
        }
        if(isset($chave_e85_codmov) && (trim($chave_e85_codmov)!="") ){
	         $sql = $clempagepag->sql_query($chave_e85_codmov,$chave_e85_codtipo,$campos,"e85_codmov");
        }else if(isset($chave_e85_codmov) && (trim($chave_e85_codmov)!="") ){
	         $sql = $clempagepag->sql_query("","",$campos,"e85_codmov"," e85_codmov like '$chave_e85_codmov%' ");
        }else{
           $sql = $clempagepag->sql_query("","",$campos,"e85_codmov#e85_codtipo","");
        }
        db_lovrot($sql,15,"()","",$funcao_js);
      }else{
        if($pesquisa_chave!=null && $pesquisa_chave!=""){
          $result = $clempagepag->sql_record($clempagepag->sql_query($pesquisa_chave));
          if($clempagepag->numrows!=0){
            db_fieldsmemory($result,0);
            echo "<script>".$funcao_js."('$e85_codmov',false);</script>";
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
