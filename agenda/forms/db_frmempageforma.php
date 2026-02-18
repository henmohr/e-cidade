<?php
$clrotulo = new rotulocampo;
$clrotulo->label("e80_data");
$clrotulo->label("e83_codtipo");
$clrotulo->label("e80_codage");
$clrotulo->label("e50_codord");
$clrotulo->label("e50_numemp");
$clrotulo->label("e60_numemp");
$clrotulo->label("e60_codemp");
$clrotulo->label("z01_numcgm");
$clrotulo->label("z01_nome");
$clrotulo->label("e60_emiss");
$clrotulo->label("e87_descgera");

$dados="ordem";


?>
<script>
function js_mascara(evt){
  var evt = (evt) ? evt : (window.event) ? window.event : "";

  if((evt.charCode >46 && evt.charCode <58) || evt.charCode ==0){//8:backspace|46:delete|190:.
    return true;
  }else{
    return false;
  }
}

function js_atualizar(){
  obj = ordem.document.form1;
  var coluna='';
  var sep='';
  var tcoluna='';
  var tsep='';
  var asep='';
  var agens = '';

  for(i=0; i<obj.length; i++){
    nome = obj[i].name.substr(0,5);
    if(nome=="CHECK" && obj[i].checked==true){
      ord = obj[i].name.substring(6);
      numemp  = obj[i+1].value;
      tipo    = obj[i+2].value;
      conta   = obj[i+4].value;
      forma   = obj[i+6].value;
      valor   = obj[i+8].value;

      coluna += sep+obj[i+9].value+"-"+obj[i].value+"-"+numemp+"-"+valor+"-"+tipo+"-"+forma+"-"+conta;
      sep= "XX";
    }
    if(nome=="CHECK"){
      if(agens.search(obj[i+1].value)==-1){
        agens+= asep+obj[i+1].value ;
        asep  = ",";
	if(tsep!=""){
	  tsep = ",";
	}
      }
      tcoluna+= tsep+obj[i].value;
      tsep    = "-";
    }
  }
  document.form1.tords.value = tcoluna;
  document.form1.ords.value = coluna;
  document.form1.agens.value = agens;
}

function js_label(liga,uak1,uak2){
  if(liga){
    document.getElementById('uak1').innerHTML=uak1;
    document.getElementById('uak2').innerHTML=uak2;
    document.getElementById('divlabel').style.visibility='visible';
  }else{
    document.getElementById('divlabel').style.visibility='hidden';
  }
}

function js_labelconta(liga,uak1,uak2,uak3){
  if(liga){
    document.getElementById('uak3').innerHTML=uak1;
    document.getElementById('uak4').innerHTML=uak2;
    document.getElementById('uak5').innerHTML=uak3;
    document.getElementById('divlabelconta').style.visibility='visible';
  }else{
    document.getElementById('divlabelconta').style.visibility='hidden';
  }
}
function js_mostravalores(){
  obj = ordem.document.form1;
  coluna = "";
  sep = "";
  for(i=0;i<obj.length;i++){
    nome = obj[i].name.substr(0,5);
    if(nome=="CHECK" && obj[i].checked==true){
      ord = obj[i].name.substring(6);
      valor   = obj[i+8].value;
      tipo    = obj[i+2].value;

      if(tipo!=0 && valor!=""){
        coluna += sep+tipo+'-'+valor;
        sep= ",";
      }
    }
  }
  if(coluna!=""){
    js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_mostratotal','func_mostratotal.php?coluna='+coluna,'Pesquisa',true,'20','390','400','300');
  }else{
    alert("Selecione algum movimento.");
  }
}
</script>
<form name="form1" method="post" action=""><BR><BR>
<?=db_input('ords',100,'',true,'hidden',1);?>
<?=db_input('tords',40,'',true,'hidden',1);?>
<?=db_input('dados',10,'',true,'hidden',1);?>
<?=db_input('agens',40,'',true,'hidden',1);?>
<?php//=db_input('forma',40,'',true,'text',1);?>
<center>
  <div align="left" id="divlabel" style="position:absolute; z-index:1; top:400; left:420; visibility: hidden; border: 1px none #000000; background-color: #CCCCCC; background-color:#999999; font-weight:bold;">
      Pago:    <span id="uak1"></span><br>
      Anulado: <span id="uak2"></span><br>
  </div>
  <div align="left" id="divlabelconta" style="position:absolute; z-index:1; top:400; left:420; visibility: hidden; border: 1px none #000000; background-color: #CCCCCC; background-color:#999999; font-weight:bold;">
      Banco:   <span id="uak3"></span><br>
      Agência: <span id="uak4"></span><br>
      Conta Padrão:   <span id="uak5"></span><br>
  </div>

<table border="0" align="left" cellpadding='0' cellspacing='0'>
  <tr>
    <td width="15%" align='right'><b>Conta pagadora padrão:</b></td>
    <td width="25%">

     <?php
      $result05  = $clempagetipo->sql_record($clempagetipo->sql_query_file(null,"e83_codtipo as codtipo,e83_descr","e83_descr"));
      $numrows05 = $clempagetipo->numrows;
      $arr['0']="Nenhum";
      for($r=0; $r<$numrows05; $r++){
	db_fieldsmemory($result05,$r);
	$arr[$codtipo] = $e83_descr;
      }
      $e83_codtipo ='0';
      db_select("e83_codtipo",$arr,true,1,"onchange='ordem.js_padrao(this.value)';");
     ?>
     </td>
    <td width="15%" align='right'><b>Recursos:</b></td>
    <td width="15%">

     <?php
       if(!isset($recursos)){
         $recursos = "proprios";
       }
       $ar = array("proprios"=>"Vinculados","todos"=>"Todos");
       db_select("recursos",$ar,true,1,"onchange='js_reload();'");
     ?>
     </td>
    <td align='left'>
      <input name="atualizar" type="submit"  value="Atualizar" onclick='return js_atualizar();'>
      <input name="total" type="button" value="Ver totais" onclick='js_mostravalores();'>
    </td>
  </tr>
  <tr>
    <td colspan='5' align='center'>
      <iframe name="ordem" src='emp4_empageforma001_ordem.php?recursos=<?=($recursos)?>' width="760" height="360" marginwidth="0" marginheight="0" frameborder="0"></iframe>
    </td>
  </tr>
  <tr>
    <td colspan='5' align='left'>
      <span style="color:red;">**</span><b>Conta de outro credor</b>
    </td>
  </tr>
</table>

</center>
</form>
<script>
function js_reload(){

  document.form1.submit();
}
</script>
