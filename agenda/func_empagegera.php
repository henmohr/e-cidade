<?php
require("libs/db_stdlib.php");
require("libs/db_conecta.php");
include("libs/db_sessoes.php");
include("libs/db_usuariosonline.php");
include("dbforms/db_funcoes.php");
include("classes/db_empagegera_classe.php");
db_postmemory($_POST);
$legacyQueryParams = [];
parse_str((string) ($_SERVER["QUERY_STRING"] ?? ""), $legacyQueryParams);
foreach ($legacyQueryParams as $legacyKey => $legacyValue) {
  if (!isset($$legacyKey)) {
    $$legacyKey = $legacyValue;
  }
}
$clempagegera = new cl_empagegera;
$clempagegera->rotulo->label("e87_codgera");
$clempagegera->rotulo->label("e87_descgera");
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
            <td width="4%" align="right" nowrap title="<?=$Te87_codgera?>">
              <?=$Le87_codgera?>
            </td>
            <td width="96%" align="left" nowrap> 
              <?php
		       db_input("e87_codgera",6,$Ie87_codgera,true,"text",4,"","chave_e87_codgera");
		       ?>
            </td>
          </tr>
          <tr> 
            <td width="4%" align="right" nowrap title="<?=$Te87_descgera?>">
              <?=$Le87_descgera?>
            </td>
            <td width="96%" align="left" nowrap> 
              <?php
		       db_input("e87_descgera",40,$Ie87_descgera,true,"text",4,"","chave_e87_descgera");
		       ?>
            </td>
          </tr>
          <tr> 
            <td colspan="2" align="center"> 
              <input name="pesquisar" type="submit" id="pesquisar2" value="Pesquisar"> 
              <input name="limpar" type="reset" id="limpar" value="Limpar" >
              <input name="Fechar" type="button" id="fechar" value="Fechar" onClick="parent.db_iframe_empagegera.hide();">
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
           if(file_exists("funcoes/db_func_empagegera.php")==true){
             include("funcoes/db_func_empagegera.php");
           }else{
           $campos = "empagegera.*";
           }
        }
        if(isset($chave_e87_codgera) && (trim($chave_e87_codgera)!="") ){
	         $sql = $clempagegera->sql_query($chave_e87_codgera,$campos,"e87_codgera");
        }else if(isset($chave_e87_descgera) && (trim($chave_e87_descgera)!="") ){
	         $sql = $clempagegera->sql_query("",$campos,"e87_descgera"," e87_descgera like '$chave_e87_descgera%' ");
        }else{
           $sql = $clempagegera->sql_query("",$campos,"e87_codgera","");
        }
        db_lovrot($sql,15,"()","",$funcao_js);
      }else{
        if($pesquisa_chave!=null && $pesquisa_chave!=""){
          $result = $clempagegera->sql_record($clempagegera->sql_query($pesquisa_chave));
          if($clempagegera->numrows!=0){
            db_fieldsmemory($result,0);
            echo "<script>".$funcao_js."('$e87_descgera',false);</script>";
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
