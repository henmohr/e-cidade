<?php
require("libs/db_stdlib.php");
require("libs/db_conecta.php");
include("libs/db_sessoes.php");
include("libs/db_usuariosonline.php");
include("dbforms/db_funcoes.php");
include("classes/db_conplanoconta_classe.php");
db_postmemory($_POST);
$legacyQueryParams = [];
parse_str((string) ($_SERVER["QUERY_STRING"] ?? ""), $legacyQueryParams);
foreach ($legacyQueryParams as $legacyKey => $legacyValue) {
  if (!isset($$legacyKey)) {
    $$legacyKey = $legacyValue;
  }
}
$clconplanoconta = new cl_conplanoconta;
$clconplanoconta->rotulo->label("c63_codcon");
$clconplanoconta->rotulo->label("c63_banco");
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
            <td width="4%" align="right" nowrap title="<?=$Tc63_codcon?>">
              <?=$Lc63_codcon?>
            </td>
            <td width="96%" align="left" nowrap> 
              <?php
		       db_input("c63_codcon",6,$Ic63_codcon,true,"text",4,"","chave_c63_codcon");
		       ?>
            </td>
          </tr>
          <tr> 
            <td width="4%" align="right" nowrap title="<?=$Tc63_banco?>">
              <?=$Lc63_banco?>
            </td>
            <td width="96%" align="left" nowrap> 
              <?php
		       db_input("c63_banco",5,$Ic63_banco,true,"text",4,"","chave_c63_banco");
		       ?>
            </td>
          </tr>
          <tr> 
            <td colspan="2" align="center"> 
              <input name="pesquisar" type="submit" id="pesquisar2" value="Pesquisar"> 
              <input name="limpar" type="reset" id="limpar" value="Limpar" >
              <input name="Fechar" type="button" id="fechar" value="Fechar" onClick="parent.db_iframe_conplanoconta.hide();">
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
           if(file_exists("funcoes/db_func_conplanoconta.php")==true){
             include("funcoes/db_func_conplanoconta.php");
           }else{
           $campos = "conplanoconta.*";
           }
        }
        if(isset($chave_c63_codcon) && (trim($chave_c63_codcon)!="") ){
	         $sql = $clconplanoconta->sql_query($chave_c63_codcon,$campos,"c63_codcon");
        }else if(isset($chave_c63_banco) && (trim($chave_c63_banco)!="") ){
	         $sql = $clconplanoconta->sql_query("",$campos,"c63_banco"," c63_banco like '$chave_c63_banco%' ");
        }else{
           $sql = $clconplanoconta->sql_query("",$campos,"c63_codcon","");
        }
        db_lovrot($sql,15,"()","",$funcao_js);
      }else{
        if($pesquisa_chave!=null && $pesquisa_chave!=""){
          $result = $clconplanoconta->sql_record($clconplanoconta->sql_query($pesquisa_chave));
          if($clconplanoconta->numrows!=0){
            db_fieldsmemory($result,0);
            echo "<script>".$funcao_js."('$c63_banco',false);</script>";
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
