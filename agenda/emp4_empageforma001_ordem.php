<?php
require("libs/db_stdlib.php");
require("libs/db_conecta.php");
include("libs/db_sessoes.php");
include("libs/db_usuariosonline.php");
include("dbforms/db_funcoes.php");

include("classes/db_pagordem_classe.php");
include("classes/db_pagordemconta_classe.php");
include("classes/db_empagetipo_classe.php");
include("classes/db_empord_classe.php");
include("classes/db_empagemov_classe.php");
include("classes/db_empagemovconta_classe.php");
include("classes/db_empagepag_classe.php");
include("classes/db_pcfornecon_classe.php");
include("classes/db_empageforma_classe.php");
include("classes/db_empagemovforma_classe.php");

$clempagetipo = new cl_empagetipo;
$clpagordem   = new cl_pagordem;
$clpagordemconta   = new cl_pagordemconta;
$clempord     = new cl_empord;
$clempagemov  = new cl_empagemov;
$clempagemovconta  = new cl_empagemovconta;
$clempagepag  = new cl_empagepag;
$clpcfornecon  = new cl_pcfornecon;
$clempageforma = new cl_empageforma;
$clempagemovforma = new cl_empagemovforma;

//echo ($_SERVER["QUERY_STRING"]);
$legacyQueryParams = [];
parse_str((string) ($_SERVER["QUERY_STRING"] ?? ""), $legacyQueryParams);
foreach ($legacyQueryParams as $legacyKey => $legacyValue) {
  if (!isset($$legacyKey)) {
    $$legacyKey = $legacyValue;
  }
}
//db_postmemory($_POST);
$db_opcao = 1;
$db_botao = false;

$clrotulo = new rotulocampo;
$clrotulo->label("e50_codord");
$clrotulo->label("o15_descr");
$clrotulo->label("o15_codigo");
$clrotulo->label("e60_numemp");
$clrotulo->label("e60_codemp");
$clrotulo->label("e60_emiss");
$clrotulo->label("z01_numcgm");
$clrotulo->label("z01_nome");
$clrotulo->label("e80_codage");
$clrotulo->label("e83_codtipo");
$clrotulo->label("e53_valor");
$clrotulo->label("e53_vlranu");
$clrotulo->label("e53_vlrpag");
$clrotulo->label("e96_codigo");
$clrotulo->label("pc63_conta");


$dbwhere = "(e53_valor-e53_vlranu-e53_vlrpag > 0 )";

$sql03="
       ";
/*
         select e82_codord from empage
		inner join empagemov   on e81_codage = e80_codage
		inner join empord      on e82_codmov = e81_codmov
		inner join empageconf  on e86_codmov = e81_codmov
*/

$sql = $clpagordem->sql_query_pagordemeleempage(null,"empagemov.e81_codage as e80_codage,e50_data,o15_codigo,o15_descr,e60_emiss,e60_anousu,e60_numemp,e50_codord,z01_numcgm,z01_cgccpf, z01_nome, sum(e53_valor) as e53_valor,sum(e53_vlranu) as e53_vlranu,sum(e53_vlrpag) as e53_vlrpag, case when sum(xxx.e81_valor) is null then 0 else sum(xxx.e81_valor) end as e81_valor","","1=1 and e86_codmov is null group by e60_numemp,e50_codord,e50_data,z01_numcgm,z01_nome,e60_emiss,o15_codigo,o15_descr,e60_anousu,e81_codage,z01_cgccpf");

$sql02 =  "select * from ($sql) as x
	   where $dbwhere
   	   order by e50_codord
	";

//echo($sql02);

$result09 = $clpagordem->sql_record($sql02);
$numrows09= $clpagordem->numrows;

?>
<html>
<head>
<title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta http-equiv="Expires" CONTENT="0">
<script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
<link href="estilos.css" rel="stylesheet" type="text/css">
<style>
<?php$cor="#999999"?>
.bordas02{
         border: 2px solid #cccccc;
         border-top-color: <?=$cor?>;
         border-right-color: <?=$cor?>;
         border-left-color: <?=$cor?>;
         border-bottom-color: <?=$cor?>;
         background-color: #999999;
}
.bordas{
         border: 1px solid #cccccc;
         border-top-color: <?=$cor?>;
         border-right-color: <?=$cor?>;
         border-left-color: <?=$cor?>;
         border-bottom-color: <?=$cor?>;
         background-color: #cccccc;
}
</style>
<script>
function js_marca(obj){
   var OBJ = document.form1;
   for(i=0;i<OBJ.length;i++){
     if(OBJ.elements[i].type == 'checkbox' && OBJ.elements[i].disabled==false){
       OBJ.elements[i].checked = !(OBJ.elements[i].checked == true);
     }
     if(OBJ.elements[i].checked==true){
         js_colocaval(OBJ.elements[i]);
     }
   }
   return false;
}
function js_confere(campo){
	erro     = false;
	erro_msg = '';

	vlrgen= new Number(campo.value);


	if(isNaN(vlrgen)){
	    erro = true;
	}
	nome = campo.name.substring(6);

	vlrlimite = new Number(eval("document.form1.disponivel_"+nome+".value"));
	if(vlrgen > vlrlimite){
	  erro_msg = "Valor digitado é maior do que o disponível!";
	  erro=true;
	}

        if(vlrgen == ''){
	   eval("document.form1."+campo.name+".value = '0.00';");
        }
        if(vlrgen == 0){
	  eval("document.form1.CHECK_"+nome+".checked=false");
	}else{
	  eval("document.form1.CHECK_"+nome+".checked=true");
	}

	if(erro==false){
	   eval("document.form1."+campo.name+".value = vlrgen.toFixed(2);");
	}else{
	   if(erro_msg != ''){
	     //alert(erro_msg);
	   }
	   eval("document.form1."+campo.name+".focus()");
	   eval("document.form1."+campo.name+".value = vlrlimite.toFixed(2);");
	   return false;
	}

}

function js_colocaval(campo){
  if(campo.checked==true){
    valor = new Number(eval('document.form1.disponivel_'+campo.value+'.value'));
    v = valor.toFixed(2);
   eval('document.form1.valor_'+campo.value+'.value='+v);
  }else{
 //   eval('document.form1.valor_'+campo.value+'.value='+valor);
  }
}

function js_padrao(val){
   var OBJ = document.form1;
   for(i=0;i<OBJ.length;i++){
     nome = OBJ.elements[i].name;
     tipo = OBJ.elements[i].type;
     if( tipo.substr(0,6) == 'select' && nome.substr(0,11)=='e83_codtipo'){
       ord = nome.substr(12);
       checa = eval("document.form1.CHECK_"+ord+".checked");
       if(checa==false){
	 continue;
       }
      for(q=0; q<OBJ.elements[i].options.length; q++){
	if(OBJ.elements[i].options[q].value==val){
	   OBJ.elements[i].options[q].selected=true;
	   break;
	 }
      }
    }
   }
}
function js_verificacampo(nomecampo,valforma){
  arr_nome = nomecampo.split("_");
  numordem = arr_nome[1];
  valorcam = eval("document.form1."+nomecampo+".value");
  if(valforma=="2" && valorcam=="3"){
    alert("Fornecedor sem conta cadastrada.\n Selecione outra forma de pagamento.");
    eval("document.form1."+nomecampo+".selectedIndex = 0;");
  }
}
function js_conta(cgm,opcao,nomecampo){
  if(opcao=="button" || opcao=="n"){
    js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_pcfornecon','com1_pcfornecon001.php?novo=true&submita=true&z01_numcgm='+cgm,'Pesquisa',true);
  }
}
</script>
</head>
<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onLoad="a=1" >
<div align="left" id="menu" style="position:absolute; z-index:1; top:15; left:32; visibility: hidden; border: 1px none #000000; background-color: #CCCCCC; background-color:#999999; font-weight:bold;">
    &nbsp;Agenda&nbsp;&nbsp;&nbsp;&nbsp; Empenho &nbsp;&nbsp;Ordem &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Recurso
</div>
<table width="100" height="100" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td align="left" valign="top" bgcolor="#CCCCCC">
<form name="form1" method="post" action="">
    <center>
      <table  class='bordas' border='5'>
        <tr>
          <th class='bordas02' align='center'><a  title='Inverte Marcação' href='' onclick='return js_marca(this);return false;'>M</a></th>
          <th class='bordas02' align='center'><b><?=$RLe60_codemp?></b></th>
          <th class='bordas02' align='center'><b><?=$RLe50_codord?></b></th>
          <th class='bordas02' align='center'><b>Conta pagadora</b></th>
          <th class='bordas02' align='center'><b>Recurso</b></th>
          <th class='bordas02' align='center'><b><?=$RLz01_nome?></b></th>
          <th class='bordas02' align='center' nowrap><b><?php/*=$RLe60_emiss*/?>Banco - Agência - Conta (credor)</b></th>
          <th class='bordas02' align='center'><b>Forma pgto.</b></th>
          <th class='bordas02' align='center'><b>Total OP</b></th>
          <th class='bordas02' align='center'><b>Liberado OP</b></th>
          <th class='bordas02' align='center'><b>Valor a pagar</b></th>
          <th class='bordas02' align='center'><b><?=$RLe80_codage?></b></th>
	</tr>
        <?php
	   $nords =  '';
	   $nvirg ='';
	   $arr_forma = Array();
	  $result_forma = $clempageforma->sql_record($clempageforma->sql_query_file(null,"e96_codigo,e96_descr","e96_codigo"));
	  $arr_forma[0] = "NDA";
	  for($i=0;$i<$clempageforma->numrows;$i++){
	    db_fieldsmemory($result_forma,$i);
	    $arr_forma[$e96_codigo] = $e96_descr;
	  }

	 // db_msgbox($numrows09);
	 $index = 0;
	  for($i=0; $i<$numrows09; $i++){
            $dbwhere02='';
	    db_fieldsmemory($result09,$i,true);

//	    if ($e60_numemp != 11808 and $e60_numemp != 8707) continue;

	    $codigodaforma = "for_$e50_codord";
	    $$codigodaforma = 0;

	    $x= "e60_numemp_$e50_codord";
	    $$x = $e60_numemp;



           //--------------------------------------
	   //rotina que verifica se tem movimento para a ordem nesta agenda.. se tiver ele marca o campo checkbox
	   $result01 = $clempagemov->sql_record($clempagemov->sql_query_ord(null,'e81_codmov,e81_valor','',"e82_codord=$e50_codord and e81_codage=$e80_codage"));
	   if($clempagemov->numrows>0){
	     db_fieldsmemory($result01,0,true);

	     $result_empagemovforma = $clempagemovforma->sql_record($clempagemovforma->sql_query_file($e81_codmov,"e97_codforma"));
	     if($clempagemovforma->numrows>0){
	       db_fieldsmemory($result_empagemovforma,0);
	       $$codigodaforma = $e97_codforma;
	     }

	     //rotina que verifica quais movimentos eh para trazer.. se todos,selecionados e naum selecionados
               //---------------------------------------------------------
	       //pega o tipo do movimento
		 $result01 = $clempagepag->sql_record($clempagepag->sql_query_file($e81_codmov,null,"e85_codtipo"));
		 if($clempagepag->numrows>0){
		   db_fieldsmemory($result01,0,true);
		   $x= "e83_codtipo_$e50_codord";
		   $$x = $e85_codtipo;

		   $dbwhere02 = " or e83_codtipo=$e85_codtipo";

		 }
              //-------------------------------------------------------------
	   }else{
	     //verifica se eh para trazer apenas os selecionados
 	     $e81_valor = '0.00';
          }

         //coloca o valor com campo
	   $x= "valor_$e50_codord";
	   $$x = number_format($e81_valor,"2",".","");

	  //rotina que verifica se existe valor disponivel
	     $result03  = $clempagemov->sql_record($clempagemov->sql_query_ord(null,"e82_codord,sum(e81_valor) as tot_valor",""," e82_codord = $e50_codord group by e82_codord "));
	     $numrows03 = $clempagemov->numrows;
	     if($numrows03 > 0){
	       db_fieldsmemory($result03,0);
	     }else{
	       $tot_valor ='0.00';
	     }

	     $result033  = $clempagemov->sql_record($clempagemov->sql_query_ord(null,"e82_codord,sum(e81_valor) as tot_valor_desta",""," e82_codord = $e50_codord and e81_codage = $e80_codage group by e82_codord "));
	     $numrows033 = $clempagemov->numrows;
	     if($numrows033 > 0){
	       db_fieldsmemory($result033,0);
	     } else {
	       $tot_valor_desta = 0;
	     }

	    $total = $e53_valor - $e53_vlrpag - $e53_vlranu;
	    $disponivel = $total - $tot_valor + $tot_valor_desta;

	    /*
	    echo "<BR><BR>
	    $total = $e53_valor - $e53_vlrpag - $e53_vlranu;
	    $disponivel = $total - $tot_valor + $tot_valor_desta;
	    ";
	    */
//	    $disponivel = $total - ($tot_valor - $e81_valor);

	    $x= "disponivel_$e50_codord";
	    $$x = number_format($disponivel,"2",".","");
	   //=-------------------------------------------

//            die("ord: $e50_codord - disp: $disponivel\n");

	   if( ($disponivel == 0 || $disponivel < 0)){
	      // echo $e50_codord." sem valor disponivel!";
              $nords .= $nvirg.$e50_codord;
	      $nvirg = " ,";
              continue;
	   }

//	    echo "$disponivel = $total - ($tot_valor - $e81_valor);<br><br>";

          //pega os tipos
          $zero = false;
          if($e60_anousu == db_getsession("DB_anousu") && $recursos != 'todos'){
             // echo "<BR><BR>".($clempagetipo->sql_query_emprec(null," distinct e83_codtipo as codtipo,e83_descr","e83_descr","e60_numemp=$e60_numemp $dbwhere02"));
              $result05  = $clempagetipo->sql_record($clempagetipo->sql_query_emprec(null," distinct e83_codtipo as codtipo,e83_descr","e83_descr","e60_numemp=$e60_numemp $dbwhere02"));
              $numrows05 = $clempagetipo->numrows;
              if($numrows05 == 0){
	        $zero = true;
              }
          }
          if($e60_anousu < db_getsession("DB_anousu") || $recursos == 'todos' || $zero==true){
              $result05  = $clempagetipo->sql_record($clempagetipo->sql_query_file(null,"e83_codtipo as codtipo,e83_descr","e83_descr"));
              $numrows05 = $clempagetipo->numrows;
          }
	  $arr = Array();
	  $arr['0']="Nenhum";
	  for($r=0; $r<$numrows05; $r++){
	    db_fieldsmemory($result05,$r);
            $arr[$codtipo] = $e83_descr;
            if($numrows05==1 && !isset($e83_codtipo)){
              $t = "e83_codtipo_$e50_codord";
              $$t = $codtipo;
            }
	  }
          flush();



	    if(isset($e83_codtipo)){
	      $t = "e83_codtipo_$e50_codord";
	      $$t = $e83_codtipo;
	    }

         //rotina que verifica se o fornecedor possui conta cadastrada para pagamento eletrônico
	 $outr = '';
         $result = $clpagordemconta->sql_record($clpagordemconta->sql_query($e50_codord,"e49_numcgm"));
         if($clpagordemconta->numrows>0){
          db_fieldsmemory($result,0);
	   $numcgm = $e49_numcgm;
	     $outr = "<span style=\"color:red;\">**</span>";
         }else{
	   $numcgm = $z01_numcgm;
	 }

	 $arr_contas = Array();
	 $result_contasforn = $clpcfornecon->sql_record($clpcfornecon->sql_query_file(null,"pc63_agencia,pc63_agencia_dig,pc63_banco,pc63_conta,pc63_conta_dig,pc63_contabanco,pc63_cnpjcpf",'',"pc63_numcgm=$numcgm"));
	 $numrows_contasforn = $clpcfornecon->numrows;
	 $arr_contas['n']="Nova conta";
	 $arr_contas[0] = "Nenhum";
	 $index = 0;
	 if($numrows_contasforn>0){
	   for($ii=0;$ii<$clpcfornecon->numrows;$ii++){
	     db_fieldsmemory($result_contasforn,$ii);
//             pc63_agencia,pc63_agencia_dig,pc63_banco,pc63_conta,pc63_contabanco
             if(trim($pc63_agencia_dig)!=""){
	       $pc63_agencia_dig = "/".$pc63_agencia_dig;
	     }
	     if(($pc63_conta_dig)!=""){
	       $pc63_conta_dig = "/".$pc63_conta_dig;
	     }

	     $arr_contas[$pc63_contabanco] = $pc63_banco.' - '.$pc63_agencia.$pc63_agencia_dig.' - '.$pc63_conta.$pc63_conta_dig;
	     $arr_cnpjcpf[$pc63_contabanco] = $pc63_cnpjcpf;
	     $arr_index[$index] = $pc63_contabanco;
	     $index++;
	   }
	 }

	 if(isset($e81_codmov)){
           $result_movconta = $clempagemovconta->sql_record($clempagemovconta->sql_query_file($e81_codmov,"e98_contabanco as con_$e50_codord"));
	   $numrows_movconta = $clempagemovconta->numrows;
	   if($numrows_movconta>0){
	     db_fieldsmemory($result_movconta,0);
	   }
	 }else{
	   $numrows_movconta = 0;
	 }

	 if($numrows_movconta==0){
	   $result_contapad = $clpcfornecon->sql_record($clpcfornecon->sql_query_padrao(null,"pc63_contabanco as con_".$e50_codord,'',"pc63_numcgm=$numcgm"));
	   $numrows_contapad = $clpcfornecon->numrows;
	   if($numrows_contapad > 0){
	     db_fieldsmemory($result_contapad,0);
	   }else{
	     $contapad = "con_$e50_codord";
	     $$contapad = 0;
	   }
	 }
	?>
        <tr>
          <td class='bordas' align='right'><input value="<?=$e50_codord?>" name="CHECK_<?=$e50_codord?>" type='checkbox' onclick='js_colocaval(this);'></td>
          <td class='bordas' align='right' title="<?=($RLe60_codemp)?> - Data de emissão:<?=$e60_emiss?>">
	  <?php
	  $codigoempenho = 'e60_numemp_'.$e50_codord;
	  $$codigoempenho = $e60_numemp;
	  db_input('e60_numemp_'.$e50_codord,5,$Ie60_numemp,true,'text',3);
	  ?>
	  </td>
          <td class='bordas' align='right' title="<?=$RLe50_codord?> - Data de emissão:<?=$e50_data?>"><?=$outr?><?=$e50_codord?></td>
          <td class='bordas' title='Conta pagadora' align='left'><?=db_select("e83_codtipo_$e50_codord",$arr,true,1)?></td>
          <td class='bordas' align='left' title="Recurso"><?=$o15_descr?></td>
          <td class='bordas' title="<?=$RLz01_nome?>" label="Numcgm:<?=$z01_numcgm?>" id="ord_<?=$e50_codord?>"><?=$z01_nome?></td>
	  <?php
	  $cpfcgc = "cpfcgc_$e50_codord";
	  $$cpfcgc = $z01_cgccpf;
	  if(sizeof($arr_contas)>2){
	    echo "<td class='bordas' align='left' title='Banco - Agência - Conta (credor)'>";
            echo "<input type='hidden' name='conta_$e50_codord'>";
	    db_select("con_$e50_codord",$arr_contas,$Ipc63_conta,1,"onchange='js_conta(\"$numcgm\",this.value,\"con_$e50_codord\");'");
	    db_input("cpfcgc_$e50_codord",6,0,true,'hidden',3);
	    echo "</td>";
	    $verificacampo = 1;
	    $formapagto = "for_$e50_codord";
	    $$formapagto = 3;
	  }else{
	    echo "<td class='bordas' align='center' title='Banco - Agência - Conta (credor)'>";
            echo "<input type='button' name='con_$e50_codord' value='Cadastrar conta' onclick='js_conta(\"$numcgm\",\"button\",\"con_$e50_codord\");'>";
	    db_input("con_$e50_codord",6,$Ipc63_conta,true,'hidden',3);
	    db_input("cpfcgc_$e50_codord",6,0,true,'hidden',3);
	    echo "</td>";
	    $verificacampo = 2;
	  }
	  ?>
          <td class='bordas' nowrap title='Forma de pagamento'><small>
	  <?php
	    echo "
		  <script>
		  function js_vercpfcgc$e50_codord(campo,valor,cgccpf){
		    arr_valores = new Array();
		    TouF = false;
	    ";
	      for($iq=0;$iq<$index;$iq++){
		$valorarray = $arr_index[$iq];
		$cpfcnpj    = $arr_cnpjcpf[$valorarray];
		echo "alert('$iq. -- .$valorarray. -- .$cpfcnpj');\n";
		echo "arr_valores['$cpfcnpj'] = '$cpfcnpj';\n";
		if($iq==0){
		  echo "TouF = true;\n";
		}
	      }
	    echo "
	            if(valor==3){
		      if(TouF == true){
			if(js_verificaCGCCPF(eval('document.form1.'+cgccpf))==false){
			  alert('Fornecedor com CGC/CPF inválido');
			}
		      }else{
			alert('Fornecedor sem CGC/CPF cadastrado.');
		      }
		    }
		  }
		  </script>
	    ";
          db_select("for_$e50_codord",$arr_forma,$Ie96_codigo,1,"onchange='js_verificacampo(this.name,\"$verificacampo\");js_vercpfcgc$e50_codord(this.name,this.value,\"$cpfcgc\");'");
	  ?>
	  </small></td>
          <td class='bordas' title='Valor total OP' align='right'  style='cursor:help' onMouseOut='parent.js_label(false);' onMouseOver="parent.js_label(true,'<?=$e53_vlrpag?>','<?=$e53_vlranu?>');"><?=$e53_valor?></td>
          <td class='bordas' title='Valor liberado OP' align='right'><?=db_input("disponivel_$e50_codord",10,$Iz01_numcgm,true,'text',3)?></td>
          <td class='bordas' title='Valor a pagar' align='right'><?=db_input("valor_$e50_codord",10,$Ie53_valor,true,'text',$db_opcao,"onChange='js_confere(this);'")?></td>
          <td class='bordas' align='center' title='<?=($RLe80_codage)?>'>
	  <?php
	  $codigodaagenda = 'e80_codage_'.$e50_codord;
	  $$codigodaagenda = $e80_codage;
	  db_input('e80_codage_'.$e50_codord,5,$Ie80_codage,true,'text',3);
	  ?>
	  </td>
	</tr>
        <?php
	  }
	?>
	<!--
	<tr>
	  <td class='bordas' align='left' colspan='11'>
	      <b>Ordens em outras agendas: <small></b><?=$nords?></small>
	  </td>
	</tr>
	-->
      </table>
    </center>
    </form>
    </td>
  </tr>
</table>
</body>
</html>
