<?php
require("libs/db_stdlib.php");
require("libs/db_conecta.php");
include("libs/db_sessoes.php");
include("libs/db_usuariosonline.php");
include("dbforms/db_funcoes.php");
include("classes/db_pcfornecon_classe.php");
db_postmemory($_POST);
$legacyQueryParams = [];
parse_str((string) ($_SERVER["QUERY_STRING"] ?? ""), $legacyQueryParams);
foreach ($legacyQueryParams as $legacyKey => $legacyValue) {
  if (!isset($$legacyKey)) {
    $$legacyKey = $legacyValue;
  }
}
$clpcfornecon = new cl_pcfornecon;
$clpcfornecon->rotulo->label("pc63_contabanco");
$clpcfornecon->rotulo->label("pc63_numcgm");
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
            <td width="4%" align="right" nowrap title="<?=$Tpc63_contabanco?>">
              <?=$Lpc63_contabanco?>
            </td>
            <td width="96%" align="left" nowrap> 
              <?php
		       db_input("pc63_contabanco",6,$Ipc63_contabanco,true,"text",4,"","chave_pc63_contabanco");
		       ?>
            </td>
          </tr>
          <tr> 
            <td width="4%" align="right" nowrap title="<?=$Tpc63_numcgm?>">
              <?=$Lpc63_numcgm?>
            </td>
            <td width="96%" align="left" nowrap> 
              <?php
		       db_input("pc63_numcgm",10,$Ipc63_numcgm,true,"text",4,"","chave_pc63_numcgm");
		       ?>
            </td>
          </tr>
          <tr> 
            <td colspan="2" align="center"> 
              <input name="pesquisar" type="submit" id="pesquisar2" value="Pesquisar"> 
              <input name="limpar" type="reset" id="limpar" value="Limpar" >
              <input name="Fechar" type="button" id="fechar" value="Fechar" onClick="parent.db_iframe_pcfornecon.hide();">
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
           if(file_exists("funcoes/db_func_pcfornecon.php")==true){
             include("funcoes/db_func_pcfornecon.php");
           }else{
           $campos = "pcfornecon.*";
           }
        }
        if(isset($chave_pc63_contabanco) && (trim($chave_pc63_contabanco)!="") ){
	         $sql = $clpcfornecon->sql_query($chave_pc63_contabanco,$campos,"pc63_contabanco");
        }else if(isset($chave_pc63_numcgm) && (trim($chave_pc63_numcgm)!="") ){
	         $sql = $clpcfornecon->sql_query("",$campos,"pc63_numcgm"," pc63_numcgm like '$chave_pc63_numcgm%' ");
        }else{
           $sql = $clpcfornecon->sql_query("",$campos,"pc63_contabanco","");
        }
        db_lovrot($sql,15,"()","",$funcao_js);
      }else{
        if($pesquisa_chave!=null && $pesquisa_chave!=""){
          $result = $clpcfornecon->sql_record($clpcfornecon->sql_query($pesquisa_chave));
          if($clpcfornecon->numrows!=0){
            db_fieldsmemory($result,0);
            echo "<script>".$funcao_js."('$pc63_numcgm',false);</script>";
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
