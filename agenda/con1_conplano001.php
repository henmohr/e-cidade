<?php
require("libs/db_stdlib.php");
require("libs/db_conecta.php");
include("libs/db_sessoes.php");
include("libs/db_usuariosonline.php");
include("classes/db_conplano_classe.php");
include("classes/db_conplanoexe_classe.php");
include("classes/db_conplanosis_classe.php");
include("classes/db_conplanoconta_classe.php");
include("classes/db_conplanoreduz_classe.php");
include("classes/db_orcfontes_classe.php");
include("classes/db_orcelemento_classe.php");
include("dbforms/db_funcoes.php");
include("classes/db_conparametro_classe.php");
include("classes/db_db_config_classe.php");
require("libs/db_libcontabilidade.php");
include_once("classes/db_conplanoref_classe.php");

$clestrutura_sistema = new cl_estrutura_sistema;
$clorcfontes     = new cl_orcfontes;
$clorcelemento     = new cl_orcelemento;
$clconparametro = new cl_conparametro;
$clconplanoreduz = new cl_conplanoreduz;
$clconplanoconta = new cl_conplanoconta;
$clconplanosis = new cl_conplanosis;
$clconplano = new cl_conplano;
$clconplanoexe = new cl_conplanoexe;
$cldb_config= new cl_db_config;
$clconplanoref= new cl_conplanoref;  

db_postmemory($_POST);
$db_opcao = 1;
$db_botao = true;
$anousu = db_getsession("DB_anousu");

if(isset($incluir)){
  $sqlerro=false;
  $codigo=str_replace(".","",$c90_estrutcontabil);
  $c61_instit = db_getsession("DB_instit");

  //**************************************************************
  //rotina que verifica se e estrutural já não existe
   $result = $clconplano->sql_record($clconplano->sql_query_file(null,null,"c60_codcon as codcon","","c60_estrut='$codigo' and c60_anousu=$anousu"));
  if($clconplano->numrows >0 && empty($novo_reduz)){
       db_fieldsmemory($result,0);
       $clconplanoreduz->sql_record($clconplanoreduz->sql_query_file(null,null,"*",'',"c61_anousu=$anousu and  c61_codcon=$codcon and c61_instit=".db_getsession("DB_instit")));
       if($clconplanoreduz->numrows>0){
         $erro_msg='Inclusão abortada. Estrutural de contabilidade já incluido para esta instituição!';
         $sqlerro=true;
       }else  if($tipo == "analitica"){
	      $sqlerro  =  true;
              $perg_msg = "Conta já existe, deseja cadastrar um reduzido para esta intituição?"; 
       }else{
               $sqlerro = true;
	       $erro_msg = "Conta já cadastrada. Não é permitido incluir ela como sintética.";
       }
  }else if($clconplano->db_verifica_conplano($codigo,$anousu)==false){
      $erro_msg=$clconplano->erro_msg;
      $sqlerro=true;
      $focar="c90_estrutcontabil";
  }else{

   /*rotina que verifica se a conta é analitica ou nao*/ 
    $nivel =  db_le_mae_conplano($codigo,true);
    if($nivel!=1){
	$mae =  db_le_mae_conplano($codigo,false);
	$result = $clconplano->sql_record($clconplano->sql_query_file("",null,"c60_codcon as c60_codcon_mae","","c60_estrut='$mae' and c60_anousu=$anousu"));
	db_fieldsmemory($result,0);
	$result = $clconplanoreduz->sql_record($clconplanoreduz->sql_query_file(null,null,"*",'',"c61_codcon=$c60_codcon_mae and c60_anousu=$anousu"));
	if($clconplanoreduz->numrows>0){
 	    $erro_msg="Conta superior $mae é analítica!\\n Inclusão não permitida!";
	    $sqlerro=true;
	    $focar="c90_estrutcontabil";
	}   
     }		
     //**********************************


	
     //rotina que verifica se o estrutural não está no conplanoconta
      if($sqlerro==false ){
         $sql = "select *
                     from conplano
		 	              inner conplanoconta on c63_codcon=c60_codcon and c63_anousu=c60_anousu
	                 where c60_estrut = '$codigo' and 
                               c60_anousu=".db_getsession("DB_anousu")."
            "; 
        $clconplanoconta->sql_record($sql);
		if($clconplanoconta->numrows >0){
		       $sqlerro = true;
		       $erro_msg = "tem conplanoconta";
		   } 
	  }
      //*********************************************
      
      
      //{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{
     /*fim*/	
     db_inicio_transacao();
      if($sqlerro==false){
	  if(isset($novo_reduz)){
         $result = $clconplano->sql_record($clconplano->sql_query_file("","","c60_codcon as codcon","","c60_estrut='$codigo' and c60_anousu=$anousu"));
         if($clconplano->numrows >0){
	           db_fieldsmemory($result,0);
	          $c60_codcon = $codcon;
	    }else{
	      $sqlerro = true;
	      $erro_msg = "Contate suporte!";
	    }  
          }else{
	    $clconplano->c60_estrut=$codigo;
	    $clconplano->incluir(null,$anousu);
	    //$clconplano->erro(true,false);
	    if($clconplano->erro_status==0){
	      $sqlerro=true;
	    }else{
	      $c60_codcon=$clconplano->c60_codcon;
	    }
	    $erro_msg=$clconplano->erro_msg;
          }	    

      } 
      if($tipo=="analitica" && $sqlerro==false){
	  /*rotina para pegar o codigo da tabela conplanosis com o estrutural*/ 
	  
	  // se ta preenchido o estrutural -- inclui na tabela conplanoref
          if (isset($c90_estrutsistema) and ($c90_estrutsistema !="") and ($c90_estrutsistema > 0)){
               $sistema=str_replace(".","",$c90_estrutsistema);
  	       $result = $clconplanosis->sql_record($clconplanosis->sql_query_file(null,"c64_codpla",'',"c64_estrut='$sistema'")); 
	       if($clconplanosis->numrows>0){
                     db_fieldsmemory($result,0);
		     // inclui no conplanoref
                     $clconplanoref->sql_record($clconplanoref->sql_query_file($c60_codcon));  
		     if($clconplanoref->numrows>0){

		       $clconplanoref->c65_codcon = $c60_codcon;
	               $clconplanoref->excluir($c60_codcon,$anousu); 
	               $erro_msg=$clconplanoref->erro_msg;
	               if($clconplanoref->erro_status==0){
               	         $sqlerro=true;
	               }
		     }

		     $clconplanoref->c65_codpla = $c64_codpla;
	             $clconplanoref->incluir($c60_codcon); 
	       }else{
                   $sqlerro=true;
                   $erro_msg='Inclusão abortada. Estrutural de sistema inválido!';
	           $focar="c90_estrutsistema";
	       }
	  } else {
             //--

	  }  

         if($sqlerro==false){
	    // - inclusao na tabela conplanoreduz
	    $result =$clconplanoreduz->sql_record("select nextval('conplanoreduz_c61_reduz_seq') as c61_reduz"); 
	    if($clconplanoreduz->numrows>0){
		 db_fieldsmemory($result,0);
	    }else{
		 die("Crie a sequencia 'conplanoreduz_c61_reduz_seq'!  ");
	    }  
          }
	  if($sqlerro==false){ 
	       $clconplanoreduz->c61_reduz=$c61_reduz; 
	       $clconplanoreduz->c61_codcon=$c60_codcon; 
	       $clconplanoreduz->c61_instit=$c61_instit; 
	       //$clconplanoreduz->c61_codpla=$c61_codpla;
	       $clconplanoreduz->c61_codigo=$c61_codigo;
	       $clconplanoreduz->incluir($c61_reduz);
	       $erro_msg=$clconplanoreduz->erro_msg;
	       if($clconplanoreduz->erro_status==0){
	  	    $sqlerro=true;
	       }
          } 		
      }
      //rotina da conplanoexe
      if($tipo=="analitica" && $sqlerro==false){
   	 $anousu=db_getsession("DB_anousu");
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
      }
      //fim
      if($tipo=="analitica" && $sqlerro==false){
	  if(isset($c63_banco) && $c63_banco !="" || isset($c63_agencia) && $c63_agencia!="" || isset($c63_conta) && $c63_conta!=""){
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
	      $clconplanoconta->incluir($c60_codcon,$anousu);
	      $erro_msg=$clconplanoconta->erro_msg;
	     // $clconplanoconta->erro(true,false);
	      if($clconplanoconta->erro_status==0){
		$sqlerro=true;
	      }
	    }
	  }
      }  
      if($sqlerro==false  && empty($novo_reduz)){
	//rotina que verifica quando é para incluir no orcelemento ou no orcfontes
	$arr_tipo = array(
			  "orcelemento" => "3", 
			  "orcfontes"   => "4"
			 );
	if(substr($codigo,0,1) == $arr_tipo["orcelemento"] ){
	  $clorcelemento->o56_codele   = $c60_codcon;
	  $clorcelemento->o56_elemento = substr($codigo,0,13);
	  $clorcelemento->o56_descr    = $c60_descr;
	  $clorcelemento->o56_finali   = $c60_finali;
	  $clorcelemento->o56_orcado   = 'true';
	  $clorcelemento->incluir($c60_codcon);
	  $erro_msg=$clorcelemento->erro_msg;
	  if($clorcelemento->erro_status==0){
	      $sqlerro=true;
	  }
	  
	}else if(substr($codigo,0,1) == $arr_tipo["orcfontes"] ){
	  $clorcfontes->o57_codfon = $c60_codcon;
	  $clorcfontes->o57_fonte  = $codigo;
	  $clorcfontes->o57_descr  = $c60_descr;
	  $clorcfontes->o57_finali = $c60_finali;
	  $clorcfontes->incluir($c60_codcon);
	  $erro_msg=$clorcfontes->erro_msg;
	  if($clorcfontes->erro_status==0){
	      $sqlerro=true;
	  }
	}
      }	
      db_fim_transacao($sqlerro);
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
if(isset($incluir)){
  if(isset($perg_msg)){
     echo "<script>";
     echo "  retorna = confirm('$perg_msg');\n";
     echo"   if(retorna == true){\n";
     echo "      obj=document.createElement('input');\n
		 obj.setAttribute('name','novo_reduz');\n
		 obj.setAttribute('type','hidden');\n
		 obj.setAttribute('value','true');\n
		 document.form1.appendChild(obj);\n
		 document.form1.incluir.click();\n
	      }\n 
	      ";
     echo "</script>";     

  }else{
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
	db_redireciona("con1_conplano001.php");
      }
  }   
}
?>
