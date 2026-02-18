<?php
require("libs/db_stdlib.php");
require("libs/db_conecta.php");
include("libs/db_sessoes.php");
include("libs/db_usuariosonline.php");
include("dbforms/db_funcoes.php");
include("classes/db_pagordem_classe.php");
include("classes/db_empempenho_classe.php");
db_postmemory($_POST);
$legacyQueryParams = [];
parse_str((string) ($_SERVER["QUERY_STRING"] ?? ""), $legacyQueryParams);
foreach ($legacyQueryParams as $legacyKey => $legacyValue) {
  if (!isset($$legacyKey)) {
    $$legacyKey = $legacyValue;
  }
}
$clpagordem = new cl_pagordem;
$clempempenho = new cl_empempenho;
$clpagordem->rotulo->label("e50_codord");
$clpagordem->rotulo->label("e50_numemp");
$rotulo = new rotulocampo;
$rotulo->label("e60_codemp");
$rotulo->label("e60_numemp");
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
            <td width="4%" align="right" nowrap title="<?=$Te60_numemp?>"><?=$Le60_codemp?> </td>
            <td width="96%" align="left" nowrap> 
             
	      <input name="chave_e60_codemp" size="12" type='text'  onKeyPress="return js_mascara(event);" >
            </td>
            <td width="4%" align="right" nowrap title="<?=$Te50_numemp?>">
              <?=$Le60_numemp?>
            </td>
            <td width="96%" align="left" nowrap> 
              <?php
		       db_input("e50_numemp",8,$Ie50_numemp,true,"text",4,"","chave_e50_numemp");
		       ?>
            </td>
          </tr>
          <tr> 
            <td width="4%" align="right" nowrap title="<?=$Te50_codord?>">
              <?=$Le50_codord?>
            </td>
            <td width="96%" align="left" nowrap> 
              <?php
		       db_input("e50_codord",6,$Ie50_codord,true,"text",4,"","chave_e50_codord");
		       ?>
            </td>
          </tr>
          <tr> 
            <td colspan="4" align="center"> 
              <input name="pesquisar" type="submit" id="pesquisar2" value="Pesquisar"> 
              <input name="limpar" type="reset" id="limpar" value="Limpar" >
              <input name="Fechar" type="button" id="fechar" value="Fechar" onClick="parent.db_iframe_pagordem.hide();">
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
	$dbwhere=" e60_instit = ".db_getsession("DB_instit");
        if(isset($campos)==false){
           if(file_exists("funcoes/db_func_pagordem.php")==true){
             include("funcoes/db_func_pagordem.php");
           }else{
           $campos = "pagordem.*";
           }
        }
        if(isset($chave_e50_codord) && (trim($chave_e50_codord)!="") ){
	         $sql = $clpagordem->sql_query("",$campos,"e50_codord","$dbwhere and e50_codord = '$chave_e50_codord' ");
	         //$sql = $clpagordem->sql_query("",$campos,"e50_codord","$dbwhere and e50_codord like '$chave_e50_codord%' ");
        }else if(isset($chave_e50_numemp) && (trim($chave_e50_numemp)!="") ){
	         $sql = $clpagordem->sql_query("",$campos,"e50_numemp","$dbwhere and e50_numemp like '$chave_e50_numemp%' ");
        }else if(isset($chave_e60_codemp) && (trim($chave_e60_codemp)!="") ){
	      $arr = explode("/",$chave_e60_codemp);
	      if(count($arr) == 2  && isset($arr[1]) && $arr[1] != '' ){
		$dbwhere_ano = " and e60_anousu = ".$arr[1];
       	      }else{
		$dbwhere_ano = "";
	      }
	      $sql = $clpagordem->sql_query("",$campos,"e50_numemp","$dbwhere and e60_codemp =  '".$arr[0]."' $dbwhere_ano");
        }else{
	  if(isset($filtroquery) || isset($pesquisar)){
             $sql = $clpagordem->sql_query_emp("",$campos,"e50_codord","$dbwhere");
	  } 
        }
	if(isset($sql)){
          db_lovrot($sql,15,"()","",$funcao_js);
	}  
      }else{
        if($pesquisa_chave!=null && $pesquisa_chave!=""){
          $result = $clpagordem->sql_record($clpagordem->sql_query($pesquisa_chave));
          if($clpagordem->numrows!=0){
            db_fieldsmemory($result,0);
            echo "<script>".$funcao_js."('$e50_numemp',false);</script>";
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
