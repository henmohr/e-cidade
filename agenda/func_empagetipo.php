<?php
require("libs/db_stdlib.php");
require("libs/db_conecta.php");
include("libs/db_sessoes.php");
include("libs/db_usuariosonline.php");
include("dbforms/db_funcoes.php");
include("classes/db_empagetipo_classe.php");
db_postmemory($_POST);
$legacyQueryParams = [];
parse_str((string) ($_SERVER["QUERY_STRING"] ?? ""), $legacyQueryParams);
foreach ($legacyQueryParams as $legacyKey => $legacyValue) {
  if (!isset($$legacyKey)) {
    $$legacyKey = $legacyValue;
  }
}
$clempagetipo = new cl_empagetipo;
$clempagetipo->rotulo->label("e83_codtipo");
$clempagetipo->rotulo->label("e83_conta");
$clempagetipo->rotulo->label("e83_descr");
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
            <td width="4%" align="right" nowrap title="<?=$Te83_codtipo?>">
              <?=$Le83_codtipo?>
            </td>
            <td width="96%" align="left" nowrap> 
              <?php
		       db_input("e83_codtipo",6,$Ie83_codtipo,true,"text",4,"","chave_e83_codtipo");
		       ?>
            </td>
          </tr>
          <tr> 
            <td width="4%" align="right" nowrap title="<?=$Te83_conta?>">
              <?=$Le83_conta?>
            </td>
            <td width="96%" align="left" nowrap> 
              <?php
		       db_input("e83_conta",6,$Ie83_conta,true,"text",4,"","chave_e83_conta");
		       ?>
            </td>
          </tr>
          <tr> 
            <td width="4%" align="right" nowrap title="<?=$Te83_descr?>">
              <?=$Le83_descr?>
            </td>
            <td width="96%" align="left" nowrap> 
              <?php
		       db_input("e83_descr",30,$Ie83_descr,true,"text",4,"","chave_e83_descr");
		       ?>
            </td>
          </tr>
          <tr> 
            <td colspan="2" align="center"> 
              <input name="pesquisar" type="submit" id="pesquisar2" value="Pesquisar"> 
              <input name="limpar" type="reset" id="limpar" value="Limpar" >
              <input name="Fechar" type="button" id="fechar" value="Fechar" onClick="parent.db_iframe_empagetipo.hide();">
             </td>
          </tr>
  <tr> 
    <td align="center" valign="top" colspan='2'> 
      <?php
      if(!isset($pesquisa_chave)){
        if(isset($campos)==false){
           if(file_exists("funcoes/db_func_empagetipo.php")==true){
             include("funcoes/db_func_empagetipo.php");
           }else{
           $campos = "empagetipo.*";
           }
        }
        if(isset($chave_e83_codtipo) && (trim($chave_e83_codtipo)!="") ){
	         $sql = $clempagetipo->sql_query($chave_e83_codtipo,$campos,"e83_codtipo");
        }else if(isset($chave_e83_conta) && (trim($chave_e83_conta)!="") ){
	         $sql = $clempagetipo->sql_query("",$campos,"e83_conta"," e83_conta = $chave_e83_conta ");
        }else if(isset($chave_e83_descr) && (trim($chave_e83_descr)!="") ){
	         $sql = $clempagetipo->sql_query("",$campos,"e83_descr"," e83_descr like '$chave_e83_descr%' ");
        }else if(isset($chave_e83_codtipo) && (trim($chave_e83_codtipo)!="") ){
	         $sql = $clempagetipo->sql_query("",$campos,"e83_codtipo"," e83_codtipo like '$chave_e83_codtipo%' ");
        }else{
//	  if(isset($pesquisar)){
             $sql = $clempagetipo->sql_query("",$campos,"e83_codtipo","");
  //        }	     
        }
	if(isset($sql) && $sql != ''){
           db_lovrot($sql,15,"()","",$funcao_js);
	}
      }else{
        if($pesquisa_chave!=null && $pesquisa_chave!=""){
          $result = $clempagetipo->sql_record($clempagetipo->sql_query($pesquisa_chave));
          if($clempagetipo->numrows!=0){
            db_fieldsmemory($result,0);
            echo "<script>".$funcao_js."('$e83_codtipo',false);</script>";
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
   </form>
  </table>
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
