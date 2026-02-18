<?php
require("libs/db_stdlib.php");
require("libs/db_conecta.php");
include("libs/db_sessoes.php");
include("libs/db_usuariosonline.php");
include("classes/db_conplano_classe.php");
include("classes/db_conplanoexe_classe.php");
include("classes/db_conplanosis_classe.php");
include("classes/db_orctiporec_classe.php");
include("classes/db_conplanoconta_classe.php");
include("classes/db_orcfontes_classe.php");
include("classes/db_orcelemento_classe.php");
include("classes/db_conlancamval_classe.php");
include("classes/db_conplanoreduz_classe.php");
include("dbforms/db_funcoes.php");
include("classes/db_conparametro_classe.php");
include("classes/db_db_config_classe.php");
include_once("classes/db_conplanoref_classe.php");
require("libs/db_libcontabilidade.php");

$clestrutura_sistema = new cl_estrutura_sistema;
$clconparametro = new cl_conparametro;
$clconplanoreduz = new cl_conplanoreduz;
$clconplanoconta = new cl_conplanoconta;
$clconplanosis = new cl_conplanosis;
$clconplano = new cl_conplano;
$clconplanoexe = new cl_conplanoexe;
$cldb_config= new cl_db_config;
$clorctiporec = new cl_orctiporec;
$clorcfontes = new cl_orcfontes;
$clorcelemento = new cl_orcelemento;
$clconplanoref= new cl_conplanoref;  
$clconlancamval = new cl_conlancamval;  


db_postmemory($_POST);
if(isset($tipo) && $tipo!=''){
  $db_opcao = 2;
  $db_botao = true;
}else{  
  $db_opcao = 22;
  $db_botao = false;
}
//-- inicio da alteração
		   $anousu=db_getsession("DB_anousu");
		   $c61_instit = db_getsession("DB_instit");
if(isset($alterar)){
   $sqlerro=false;
   $codigo=str_replace(".","",$c90_estrutcontabil);
   if($clconplano->db_verifica_conplano($codigo)==false){
        $erro_msg=$clconplano->erro_msg;
       $sqlerro=true;
       $focar="c90_estrutcontabil";
   }else{

   /*rotina que verifica se a conta é analitica ou nao*/ 
    $nivel =  db_le_mae_conplano($codigo,true);
    if($nivel!=1){
	$mae =  db_le_mae_conplano($codigo,false);
	$result = $clconplano->sql_record($clconplano->sql_query_file("",null,"c60_codcon as codcon","","c60_estrut='$mae' and c60_anousu=$anousu"));
	db_fieldsmemory($result,0);
	$result = $clconplanoreduz->sql_record($clconplanoreduz->sql_query_file(null,null,"*","","c61_codcon=$codcon and c61_anousu=$anousu "));
	if($clconplanoreduz->numrows>0){
	  $erro_msg="Conta superior $mae é analítica!\\n Inclusão não permitida!";
	  $sqlerro=true;
	    $focar="c90_estrutcontabil";
	}   
     }		
     /*fim*/	
     db_inicio_transacao();
      if($sqlerro==false){
	    $clconplano->c60_estrut=$codigo;
	    $clconplano->alterar($c60_codcon);
	    //$clconplano->erro(true,false);
	    if($clconplano->erro_status==0){
	      $sqlerro=true;
	    }
	    $erro_msg=$clconplano->erro_msg;

      } 

      //rotina da tabela conplanoref
      if($tipo=="analitica" && $sqlerro==false){
            /*rotina para pegar o codigo da tabela conplanosis com o estrutural*/ 
	    if (isset($c90_estrutsistema) and ($c90_estrutsistema !="") and ($c90_estrutsistema > 0)){
	        $sistema=str_replace(".","",$c90_estrutsistema);
	        $result = $clconplanosis->sql_record($clconplanosis->sql_query_file(null,"c64_codpla",'',"c64_estrut='$sistema'")); 
	        if($clconplanosis->numrows>0){
	                 db_fieldsmemory($result,0);
                         // estrutural valido, se não existe no conplanoref então inclui, se existe, altera  
                         $res = $clconplanoref->sql_record($clconplanoref->sql_query_file($c60_codcon)); 
	                 if($clconplanoref->numrows>0){
			     $clconplanoref->c65_codpla = $c64_codpla;
			     $clconplanoref->c65_codcon = $c60_codcon;
	                     $clconplanoref->alterar(); 
		         } else { 
			     // inclui no conplanoref
			     $clconplanoref->c65_codpla = $c64_codpla;
	                     $clconplanoref->incluir($c60_codcon); 
			 };
	        }else{
	            $sqlerro=true;
	            $erro_msg='Inclusão abortada. Estrutural de sistema inválido!';
	            $focar="c90_estrutsistema";
		};   
	    }else{
	          $excluir_conplanoref=true;//para excluir 
	    }
      }else{//se for sintetica, verifica se não existia, se existir será excluido
	          $excluir_conplanoref=true;//para excluir 
      }
      if(isset($excluir_conplanoref)){
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
       //fim

  //rotina de alteração da tabela conplanoconta
      if($tipo=="analitica" && $sqlerro==false){
	  if(isset($c63_banco) && $c63_banco !="" || isset($c63_agencia) && $c63_agencia!="" || isset($c63_conta) && $c63_conta!=""){
 	    $clconplanoconta->sql_record($clconplanoconta->sql_query_file($c60_codcon,$anousu,"c63_banco")); 
	    if($clconplanoconta->numrows>0){
	       $proces="alterar"; 
	    }else{
	       $proces="incluir"; 
	    }
	    if(strlen($c63_banco)>3){
	      $sqlerro = true;
	      $erro_msg = "Usuário:\\n\\nBanco deve conter no máximo três(3) caracteres.\\n\\nAdministrador.";
            }else if(strlen($c63_agencia)>5){
              $sqlerro=true;	      
              $erro_msg = "Usuário:\\n\\nAgência deve ter no máximo cinco(5) caracteres.\\n\\nAdministrador:";
	    }else if(trim($c63_dvagencia)=="" && trim($c63_agencia)!=""){
	      $sqlerro = true;
	      $erro_msg = "Usuário:\\n\\nInforme o dígito verificador da agência.\\n\\nAdministrador.";
	    }else if(strlen($c63_conta)>12){
	      $sqlerro = true;
	      $erro_msg = "Usuário:\\n\\nConta deve ter no máximo doze(12) caracteres.\\n\\nAdministrador.";
	    }else if(trim($c63_dvconta)=="" && trim($c63_conta)!=""){
	      $sqlerro = true;
	      $erro_msg = "Usuário:\\n\\nInforme o dígito verificador da conta.\\n\\nAdministrador.";
	    }
            if($sqlerro==false){
	      $clconplanoconta->c63_banco=$c63_banco;
	      $clconplanoconta->c63_agencia=$c63_agencia;
	      $clconplanoconta->c63_conta=$c63_conta;
	      $clconplanoconta->c63_codcon=$c60_codcon;
	      $clconplanoconta->c63_anousu=$anousu;
	      if($proces=="alterar"){
		  $clconplanoconta->alterar($c60_codcon,$anousu);
	      }else {   
		  $clconplanoconta->incluir($c60_codcon,$anousu);
	      }   
	      $erro_msg=$clconplanoconta->erro_msg;
	      //$clconplanoconta->erro(true,false);
	      if($clconplanoconta->erro_status==0){
		$sqlerro=true;
	      }
            }
	  }else{
              $excluir_conplanoconta=true;//irá excluir do conplanoconta
	  }
      }else{
              $excluir_conplanoconta=true;//irá excluir do conplanoconta
      }  
      //rotina para excluir do conplanoconta
	  if(isset($excluir_conplanoconta) && $sqlerro==false){
	    $clconplanoconta->sql_record($clconplanoconta->sql_query_file($c60_codcon,$anousu,"c63_banco")); 
	    if($clconplanoconta->numrows>0){
	      $clconplanoconta->c63_codcon=$c60_codcon;
	      $clconplanoconta->c63_anousu=$anousu;
	      $clconplanoconta->excluir($c60_codcon,$anousu);
	      //$clconplanoconta->erro(true,false);
	      $erro_msg=$clconplanoconta->erro_msg;
	      if($clconplanoconta->erro_status==0){
		$sqlerro=true;
	      }
	    }
	  }  	
      //fim
    ///fim      


    //rotina de alteração da conplanoreduz
    $resultz = $clconplanoreduz->sql_record($clconplanoreduz->sql_query_file(null,null,"c61_reduz,c61_codcon,c61_instit","","c61_anousu=$anousu and c61_codcon=$c60_codcon and c61_instit=".db_getsession("DB_instit")));
    $numrows_veri= $clconplanoreduz->numrows;
    if($numrows_veri>0 && $sqlerro==false){//se existir conplanreduz ele sera alterado
       db_fieldsmemory($resultz,0);
    }        
	       

    
    if($tipo=="analitica" && $sqlerro==false){
      if($numrows_veri>0){//se existir conplanreduz ele sera alterado
          if($sqlerro==false){ 
	       $clconplanoreduz->c61_instit=$c61_instit; 
	       $clconplanoreduz->c61_codigo=$c61_codigo;
	       //$clconplanoreduz->c61_codcon=$c60_codcon;
	       $clconplanoreduz->c61_reduz=$c61_reduz;
	       $clconplanoreduz->c61_anousu=$anousu;
	       $clconplanoreduz->alterar($c61_reduz,$anousu);
	       $erro_msg=$clconplanoreduz->erro_msg;
	       if($clconplanoreduz->erro_status==0){
	  	    $sqlerro=true;
	       }
	   } 
      }else{//se nao existir     conplanoreduz ele sera incluido
     	  $result =$clconplanoreduz->sql_record("select nextval('conplanoreduz_c61_reduz_seq') as c61_reduz"); 
	  if($clconplanoreduz->numrows>0){
   	       db_fieldsmemory($result,0);
	  }else{
	       die("Crie a sequencia 'conplano_c61_reduz_seq'!  ");
	  }  
          if($sqlerro==false){ 
	       $clconplanoreduz->c61_reduz=$c61_reduz; 
	       $clconplanoreduz->c61_instit=$c61_instit; 
	       $clconplanoreduz->c61_codigo=$c61_codigo;
	       $clconplanoreduz->c61_codcon=$c60_codcon;
	       $clconplanoreduz->c61_anousu=$anousu;
	       $clconplanoreduz->incluir($c61_reduz,$anousu);
	       $erro_msg=$clconplanoreduz->erro_msg;
	       if($clconplanoreduz->erro_status==0){
	  	    $sqlerro=true;
	       }
	  }     
      }
      if(isset($c61_reduz) && $sqlerro==false){
	$result = $clconplanoexe->sql_record($clconplanoexe->sql_query_file($anousu,$c61_reduz));
	if($clconplanoexe->numrows>0){           
		 //altera conplanoexe    
		   $anousu=db_getsession("DB_anousu");
		   $clconplanoexe->c62_anousu = $anousu ;
		   $clconplanoexe->c62_reduz  = $c61_reduz;
		   $clconplanoexe->c62_codrec = $c61_codigo;
		   $clconplanoexe->alterar($anousu,$c61_reduz);
	 	   $erro_msg=$clconplanoexe->erro_msg;
		   if($clconplanoexe->erro_status==0){
			$sqlerro=true;
		   }
		 //fim
	}else{ 		
		 //incluir conplanoexe  
		     $clconplanoexe->c62_anousu = $anousu ;
		     $clconplanoexe->c62_reduz  = $c61_reduz;
		     $clconplanoexe->c62_codrec = $c61_codigo;
		     $clconplanoexe->c62_vlrcre = '0';
		     $clconplanoexe->c62_vlrdeb = '0';
		     $clconplanoexe->incluir($anousu,$c61_reduz);
		     $erro_msg=$clconplanoexe->erro_msg;
		     if($clconplanoexe->erro_status==0){
			  $sqlerro=true;
		     }
		  //fim   
	}
      }  	
    }else{//se vier sintetica
      if(isset($c61_reduz) && $c61_reduz != 0 && $sqlerro == false){
		
        $result = $clconplanoexe->sql_record($clconplanoexe->sql_query_file($anousu,$c61_reduz));
        if($clconplanoexe->numrows>0){
	   $clconplanoexe->c62_reduz=$c61_reduz;
           if($anousu == '' || $c61_reduz==''){
	     die('Contate suporte!');
	   }
	   
	   $clconplanoexe->excluir($anousu,$c61_reduz);
           $erro_msg=$clconplanoexe->erro_msg;
	   if($clconplanoexe->erro_status==0){
	      $sqlerro=true;
	   }
        }   
      }   
      if($numrows_veri>0 && $sqlerro==false){//se existir conplanreduz ele sera excluido e tambem o conplanoexe
         //excluir conplanoreduz
         $clconplanoreduz->c61_reduz   =$c61_reduz;
         $clconplanoreduz->c61_anousu=$anousu;
         $clconplanoreduz->excluir($c61_reduz,$anousu);
         if($clconplanoreduz->erro_status==0){
   	    $sqlerro=true;
         }
      }
    }
    //fim

      //rotina que verifica quando é para incluir no orcelemento ou no orcfontes
      if($sqlerro==false){
	$arr_tipo = array(
			  "orcelemento" => "3", 
			  "orcfontes"   => "4"
			 );
	if(substr($codigo,0,1) == $arr_tipo["orcelemento"] ){
	  $clorcelemento->o56_codele   = $c60_codcon;
	  $clorcelemento->o56_anousu  = $anousu;
	  $clorcelemento->o56_elemento = substr($codigo,0,13);
	  $clorcelemento->o56_descr    = $c60_descr;
	  $clorcelemento->o56_finali   = $c60_finali;
	  $clorcelemento->o56_orcado   = 'true';
	  $clorcelemento->sql_record($clorcelemento->sql_query_file($c60_codcon,$anousu));
	  if($clorcelemento->numrows>0){
	    $clorcelemento->alterar($c60_codcon,$anousu);
	  }else{
  	    $clorcelemento->incluir($c60_codcon,$anousu);
	  }  
	  $erro_msg=$clorcelemento->erro_msg;
	  if($clorcelemento->erro_status==0){
	      $sqlerro=true;
	  }
	  
	}else if(substr($codigo,0,1) == $arr_tipo["orcfontes"] ){
	  $clorcfontes->o57_codfon = $c60_codcon;
	  $clorcfontes->o57_anousu= $anousu;
	  $clorcfontes->o57_fonte  = $codigo;
	  $clorcfontes->o57_descr  = $c60_descr;
	  $clorcfontes->o57_finali = $c60_finali;
	  $clorcfontes->sql_record($clorcfontes->sql_query_file($c60_codcon,$anousu));
	  if($clorcfontes->numrows>0){
	    $clorcfontes->alterar($c60_codcon,$anousu);
	  }else{
  	    $clorcfontes->incluir($c60_codcon,$anousu);
	  }  
	  $erro_msg=$clorcfontes->erro_msg;
	  if($clorcfontes->erro_status==0){
	      $sqlerro=true;
	  }
	}
      }	
   
    db_fim_transacao($sqlerro);
  }    
}else if(isset($chavepesquisa)){
   $db_opcao = 2;
   $db_botao = true;
   $result = $clconplano->sql_record($clconplano->sql_query_geral($chavepesquisa,$anousu)); 
   db_fieldsmemory($result,0);
   $c90_estrutcontabil=$c60_estrut;
   
   $result = $clconplanoreduz->sql_record($clconplanoreduz->sql_query_file(null,null,"*","","c61_anousu=$anousu and c61_codcon=$c60_codcon and c61_instit=".db_getsession("DB_instit")));
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
    // procura na tabela conplanoref, se existe referencia para conta mae
    $result = $clconplanoref->sql_record($clconplanoref->sql_query($c60_codcon,"c64_estrut as c90_estrutsistema,c64_descr"));
    if($clconplanoref->numrows>0){
        db_fieldsmemory($result,0);
    }  
   if(isset($c61_codigo) && $c61_codigo!=''&& $c61_codigo!='0' ){
     $result = $clorctiporec->sql_record($clorctiporec->sql_query_file($c61_codigo,"o15_descr")); 
     if($clorctiporec->numrows>0){
       db_fieldsmemory($result,0);
     }  
   } 

   //regrinhas...
   if($tipo=="sintetica"){
         $clconplano->db_verifica_conplano_exclusao($c60_estrut,$anousu);
	 if($clconplano->erro_status==0){
           $disab_tipo=true;
   	   $disab_msg="Conta não poderá ser alterada para analitica pois já possui conta filha.";
	 }
   }else{
     $clconlancamval->sql_record($clconlancamval->sql_query_file(null,"c69_sequen",""," c69_anousu=$anousu and ( c69_credito=$c61_reduz or c69_debito=$c61_reduz) "));    
     if($clconlancamval->numrows>0){
        $disab_tipo=true;
	$disab_msg="Conta não poderá ser alterada para sintética pois já existem lançamentos para ela.";
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
if(isset($alterar)){
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
    db_redireciona("con1_conplano002.php");
  }
}
if($db_opcao==22){
  echo "<script>document.form1.pesquisar.click();</script>";
}
?>
