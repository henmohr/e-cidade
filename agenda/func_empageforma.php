<?php
require("libs/db_stdlib.php");
require("libs/db_conecta.php");
include("libs/db_sessoes.php");
include("libs/db_usuariosonline.php");
include("dbforms/db_funcoes.php");
include("classes/db_empageforma_classe.php");
db_postmemory($_POST);

$legacyQueryParams = [];
parse_str((string) ($_SERVER["QUERY_STRING"] ?? ""), $legacyQueryParams);
foreach ($legacyQueryParams as $legacyKey => $legacyValue) {
  if (!isset($$legacyKey)) {
    $$legacyKey = $legacyValue;
  }
}
$clempageforma = new cl_empageforma;
$clempageforma->rotulo->label("e96_codigo");
$clempageforma->rotulo->label("e96_codigo");
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
            <td width="4%" align="right" nowrap title="<?=$Te96_codigo?>">
              <?=$Le96_codigo?>
            </td>
            <td width="96%" align="left" nowrap> 
              <?php
		       db_input("e96_codigo",10,$Ie96_codigo,true,"text",4,"","chave_e96_codigo");
		       ?>
            </td>
          </tr>
          <tr> 
            <td width="4%" align="right" nowrap title="<?=$Te96_codigo?>">
              <?=$Le96_codigo?>
            </td>
            <td width="96%" align="left" nowrap> 
              <?
		       db_input("e96_codigo",10,$Ie96_codigo,true,"text",4,"","chave_e96_codigo");
		       ?>
            </td>
          </tr>
          <tr> 
            <td colspan="2" align="center"> 
              <input name="pesquisar" type="submit" id="pesquisar2" value="Pesquisar"> 
              <input name="limpar" type="reset" id="limpar" value="Limpar" >
              <input name="Fechar" type="button" id="fechar" value="Fechar" onClick="parent.db_iframe_empageforma.hide();">
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
           if(file_exists("funcoes/db_func_empageforma.php")==true){
             include("funcoes/db_func_empageforma.php");
           }else{
           $campos = "empageforma.*";
           }
        }
        if(isset($chave_e96_codigo) && (trim($chave_e96_codigo)!="") ){
	         $sql = $clempageforma->sql_query($chave_e96_codigo,$campos,"e96_codigo");
        }else if(isset($chave_e96_codigo) && (trim($chave_e96_codigo)!="") ){
	         $sql = $clempageforma->sql_query("",$campos,"e96_codigo"," e96_codigo like '$chave_e96_codigo%' ");
        }else{
           $sql = $clempageforma->sql_query("",$campos,"e96_codigo","");
        }
        db_lovrot($sql,15,"()","",$funcao_js);
      }else{
        if($pesquisa_chave!=null && $pesquisa_chave!=""){
          $result = $clempageforma->sql_record($clempageforma->sql_query($pesquisa_chave));
          if($clempageforma->numrows!=0){
            db_fieldsmemory($result,0);
            echo "<script>".$funcao_js."('$e96_codigo',false);</script>";
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
