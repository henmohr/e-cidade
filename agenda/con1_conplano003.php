<?php
require("libs/db_stdlib.php");
require("libs/db_conecta.php");
include("libs/db_sessoes.php");
include("libs/db_usuariosonline.php");
include("classes/db_conplano_classe.php");
include("classes/db_conlancamval_classe.php");
include("classes/db_orctiporec_classe.php");
include("classes/db_orcfontes_classe.php");
include("classes/db_orcelemento_classe.php");
include("classes/db_conplanosis_classe.php");
include("classes/db_conplanoconta_classe.php");
include("classes/db_conplanoreduz_classe.php");
include("classes/db_conplanoexe_classe.php");
include("dbforms/db_funcoes.php");
include("classes/db_conparametro_classe.php");
include("classes/db_db_config_classe.php");
require("libs/db_libcontabilidade.php");
include_once("classes/db_conplanoref_classe.php");

$clestrutura_sistema = new cl_estrutura_sistema;
$clconparametro = new cl_conparametro;
$clconplanoreduz = new cl_conplanoreduz;
$clconplanoexe = new cl_conplanoexe;
$clconplanoconta = new cl_conplanoconta;
$clconplanosis = new cl_conplanosis;
$clconplano = new cl_conplano;
$clconlancamval = new cl_conlancamval;
$cldb_config= new cl_db_config;
$clorctiporec = new cl_orctiporec;
$clorcfontes = new cl_orcfontes;
$clorcelemento = new cl_orcelemento;
$clconplanoref= new cl_conplanoref;  


db_postmemory($_POST);
if(isset($tipo) && $tipo!=''){
  $db_opcao = 3;
  $db_botao = true;
}else{  
  $db_opcao = 33;
  $db_botao = false;
}
$anousu = db_getsession("DB_anousu");
if(isset($excluir)){
  $sqlerro=false;
  $codigo=str_replace(".","",$c90_estrutcontabil);
  if($clconplano->db_verifica_conplano_exclusao($codigo)==false){
     $erro_msg=$clconplano->erro_msg;
     $sqlerro=true;
  }else{
     db_inicio_transacao();
    
     
      /*rotina que exclui do conplanoreduz*/
      if($sqlerro==false){
	
 	  $result = $clconplanoreduz->sql_record($clconplanoreduz->sql_query_file(null,null,"c61_reduz",''," c61_anousu=".db_getsession("DB_anousu")." and c61_codcon=$c60_codcon and c61_instit=".db_getsession("DB_instit"))); 
	  if($clconplanoreduz->numrows>0){
	      db_fieldsmemory($result,0);


	      
	     if($sqlerro == false){
				$result = $clconplanoexe->sql_record($clconplanoexe->sql_query_file($anousu,$c61_reduz));
				if($clconplanoexe->numrows>0){
				     $clconplanoexe->c62_reduz=$c61_reduz;
				     $clconplanoexe->excluir($anousu,$c61_reduz);
				     $erro_msg=$clconplanoexe->erro_msg;
				     if($clconplanoexe->erro_status==0){
					$sqlerro=true;
				     }
				}   
          } 
	      if($sqlerro == false){
				$clconplanoreduz->c61_reduz=$c61_reduz;
				$clconplanoreduz->excluir($c61_reduz);
				$erro_msg=$clconplanoreduz->erro_msg;
				if($clconplanoreduz->erro_status==0){
				  $sqlerro=true;
				}
          }  		
	  }
      }
      /*fim*/

   //exluir apenas quando tiver apenas um reduzido para esta tabela..
   $clconplanoreduz->sql_record($clconplanoreduz->sql_query_razao(null,'c60_codcon',''," c61_anousu=".db_getsession("DB_anousu")."  and  c61_reduz <> $c61_reduz and  c60_codcon =$c60_codcon"));     
   if($clconplanoreduz->numrows==0){
      
      // exclui do conplanoref - se existir 
      if($sqlerro==false){
 	  $clconplanoref->sql_record($clconplanoref->sql_query_file($c60_codcon)); 
	  if($clconplanoref->numrows>0){
	      $clconplanoref->c65_codcon=$c60_codcon;
	      $clconplanoref->excluir($c60_codcon);
	      $erro_msg=$clconplanoref->erro_msg;
	      if($clconplanoref->erro_status==0){
		  $sqlerro=true;
	      }
	  }
      }
      // 
      /*rotina que exclui do conplanoconta*/
      if($sqlerro==false){
      	    $clconplanoconta->sql_record($clconplanoconta->sql_query_file($c60_codcon,$anousu,"c63_banco")); 
	    if($clconplanoconta->numrows>0){
	      $clconplanoconta->c63_codcon=$c60_codcon;
	      $clconplanoconta->excluir($c60_codcon);
	      //$clconplanoconta->erro(true,false);
	      $erro_msg=$clconplanoconta->erro_msg;
	      if($clconplanoconta->erro_status==0){
		$sqlerro=true;
	      }
	    }
      } 	    
      /*fim*/ 	    
      /*rotina que exclui do conplano*/
	if($sqlerro==false){
	           
		$clconplano->c60_estrut=$codigo;
		$clconplano->excluir($c60_codcon);
		//$clconplano->erro(true,false);
		if($clconplano->erro_status==0){
		  $sqlerro=true;
		}
		$erro_msg=$clconplano->erro_msg;
              
	} 
      //final  	


      //rotina que verifica se foi  incluido no orcelemento ou no orcfontes
      if($sqlerro==false){
	$arr_tipo = array(
			  "orcelemento" => "3", 
			  "orcfontes"   => "4"
			 );
	if(substr($codigo,0,1) == $arr_tipo["orcelemento"] ){
	  $clorcelemento->sql_record($clorcelemento->sql_query_file($c60_codcon,$anousu));
	  if($clorcelemento->numrows>0){
	    $clorcelemento->o56_codele   = $c60_codcon;
	    $clorcelemento->o56_anousu  = $anousu;
	    $clorcelemento->excluir($c60_codcon,$anousu);
	    $erro_msg=$clorcelemento->erro_msg;
	    if($clorcelemento->erro_status==0){
		     $sqlerro=true;
	    }
	  }  
	  
	}else if(substr($codigo,0,1) == $arr_tipo["orcfontes"] ){
	  $clorcfontes->o57_codfon = $c60_codcon;
	  $clorcfontes->o57_anousu= $anousu;
	  $clorcfontes->sql_record($clorcfontes->sql_query_file($c60_codcon,$anousu));
	  if($clorcfontes->numrows>0){
	    $clorcfontes->excluir($c60_codcon,$anousu);
	    $erro_msg=$clorcfontes->erro_msg;
	    if($clorcfontes->erro_status==0){
		     $sqlerro=true;
	    }
	  }  
	}
      }	
    }   
      db_fim_transacao($sqlerro);
  }    
}else if(isset($chavepesquisa)){
   $db_opcao = 3;
   $db_botao = true;
   $result = $clconplano->sql_record($clconplano->sql_query_geral($chavepesquisa)); 
   db_fieldsmemory($result,0);
   $c90_estrutcontabil=$c60_estrut;
   
   $result = $clconplanoreduz->sql_record($clconplanoreduz->sql_query_file(null,null,"*","","c61_anousu=".db_getsession("DB_anousu")."  and c61_codcon=$c60_codcon and c61_instit=".db_getsession("DB_instit")));
   global $tipo;
 
   if($clconplanoreduz->numrows>0){
       db_fieldsmemory($result,0);
       $tipo='analitica';
   }else{
       $tipo='sintetica';
   }  
   $result = $clconplanoconta->sql_record($clconplanoconta->sql_query_file($c60_codcon,$anousu)); 
   if($clconplanoconta->numrows>0){
      db_fieldsmemory($result,0);
   }
   $result = $clconplanoref->sql_record($clconplanoref->sql_query($c60_codcon,"c64_estrut as c90_estrutsistema,c64_descr"));
   if($clconplanoref->numrows>0){
      db_fieldsmemory($result,0);
   }  
   /* --
   if(isset($c61_codpla) && $c61_codpla!=''&& $c61_codpla!='0' ){
     $result = $clconplanosis->sql_record($clconplanosis->sql_query_file($c61_codpla,"c64_estrut as c90_estrutsistema,c64_descr")); 
     if($clconplanosis->numrows>0){
       db_fieldsmemory($result,0);
     }  
   }  
   */
   if(isset($c61_codigo) && $c61_codigo!=''&& $c61_codigo!='0' ){
     $result = $clorctiporec->sql_record($clorctiporec->sql_query_file($c61_codigo,"o15_descr")); 
     if($clconplanosis->numrows>0){
       db_fieldsmemory($result,0);
     }  
   } 
}   
?>
<html>
<head>
<title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta http-equiv="Expires" CONTENT="0">
<script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
<link href="estilos.css" rel="stylesheet" type="text/css">
</head>
<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onLoad="a=1" >
<table width="790" border="0" cellpadding="0" cellspacing="0" bgcolor="#5786B2">
  <tr> 
    <td width="360" height="18">&nbsp;</td>
    <td width="263">&nbsp;</td>
    <td width="25">&nbsp;</td>
    <td width="140">&nbsp;</td>
  </tr>
</table>
<table width="790" border="0" cellspacing="0" cellpadding="0">
  <tr> 
    <td height="430" align="left" valign="top" bgcolor="#CCCCCC"> 
    <center>
    <br>
	<?php
	include("forms/db_frmconplano.php");
	?>
    </center>
	</td>
  </tr>
</table>
     <?php 
  db_menu(db_getsession("DB_id_usuario"),db_getsession("DB_modulo"),db_getsession("DB_anousu"),db_getsession("DB_instit"));
     ?>
</body>
</html>
<?php
if(isset($excluir)){
  if($sqlerro==true){
    db_msgbox($erro_msg);
    if($clconplano->erro_campo!=""){
      echo "<script> document.form1.".$clconplano->erro_campo.".style.backgroundColor='#99A9AE';</script>";
      echo "<script> document.form1.".$clconplano->erro_campo.".focus();</script>";
    }else if($clconplanoreduz->erro_campo!=""){
      echo "<script> document.form1.".$clconplanoreduz->erro_campo.".style.backgroundColor='#99A9AE';</script>";
      echo "<script> document.form1.".$clconplanoreduz->erro_campo.".focus();</script>";
    }
  }else{
    db_msgbox($erro_msg);
    db_redireciona("con1_conplano003.php");
  }
}
if($db_opcao==33){
  echo "<script>document.form1.pesquisar.click();</script>";
}
              
if(isset($chavepesquisa)){
	//rotina que verifica se nam existe lançamento contábil para este reduzido  
	$clconlancamval->sql_record($clconlancamval->sql_query_file(null,"c69_sequen","","c69_credito = $c61_reduz or c69_debito =  $c61_reduz")); 
	if($clconlancamval->numrows>0){
	  db_msgbox("Exclusão Abortada. Reduzido está sendo usado nos lançamentos contábeis.");
	  echo "<script>document.form1.excluir.disabled= true;</script>";
	}   
}	      
?>
