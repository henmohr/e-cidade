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

function js_atualizar(opcao){
  obj = ordem.document.form1;
  var coluna='';
  var sep='';
  var ncoluna='';
  var nsep='';
  for(i=0; i<obj.length; i++){
    tipo = obj[i].type;
    if(tipo=="checkbox" && obj[i].checked==true){
      coluna += sep+obj[i].name;
      sep = ",";
    }else if(tipo=="checkbox" && obj[i].checked==false){
      ncoluna += nsep+obj[i].name;
      nsep = ",";
    }
  }

  if(coluna != ""){
    document.form1.movs.value = coluna;
    document.form1.nmov.value = ncoluna;
    return true;
  }else{
    if(opcao=="1"){
      alert("Selecione algum item para gerar arquivo.");
    }else{
      alert("Selecione algum item para cancelar.");
    }
    return false;
  }
}
function js_mostravalores(){
  obj = ordem.document.form1;
  valores = "";
  vir = "";
  for(i=0;i<obj.length;i++){
    if(obj.elements[i].checked==true){
      valores += vir+obj.elements[i].name;
      vir = ",";
    }
  }
  if(valores!=""){
    js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_mostratotal','func_mostratotal.php?valores='+valores,'Pesquisa',true,'20','390','400','300');
  }else{
    alert("Selecione algum movimento.");
  }
}
function js_valores(opcao,valortotal,valortipo,descrtipo,codtipo){
  obj = ordem.document.form1;
  if(opcao){
    valortipo = 0;
    valortotal = 0;
    for(i=0;i<obj.length;i++){
      if(obj.elements[i].type=="checkbox" && obj.elements[i].checked==true){
	if(obj.elements[i].name == eval("ordem.document.form1.valor_"+obj.elements[i].name+"_"+codtipo+".name")){
	  alert(obj.elements[i].name);
	  valortipo += new Number(eval("ordem.document.form1.valor_"+obj.elements[i].name+"_"+codtipo+".value"));
	}
	if(obj.elements[i].type=="text"){
	  valortotal += new Number(obj.elements[i].value);
	}
      }
    }
    document.getElementById("descr").innerHTML=descrtipo;
    valortipo  = new Number(valortipo);
    valortotal = new Number(valortotal);
    document.getElementById("valgertot").innerHTML=valortipo.toFixed(2);
    document.getElementById("valtottip").innerHTML=valortotal.toFixed(2);
    document.getElementById('valores').style.visibility='visible';
  }else{
    document.getElementById('valores').style.visibility='hidden';
  }
}
</script>
<form name="form1" method="post" action=""><BR><BR>
<?=db_input('movs',100,'',true,'hidden',1);?>
<?=db_input('nmov',100,'',true,'hidden',1);?>
<center>
<div align="left" id="valores" style="position:absolute; z-index:1; top:360; left:420; visibility: hidden; border: 1px none #000000; background-color: #CCCCCC; background-color:#999999; font-weight:bold;">
    <span id="descr"></span><br>
    Total tipo: <span id="valtottip"></span><br>
    Total geral: <span id="valgertot"></span><br>
</div>
<table border="0" align="left" cellpadding='0' cellspacing='0'>
  <tr>
    <td width="15%" colspan='1' align='right'>
      <strong>Descrição da agenda:</strong>
    </td>
    <td width="35%" colspan='1' align='left'>
     <?php
     db_input('e87_descgera',40,$Ie87_descgera,true,'text',1);
     ?>
    </td>
    <td width="15%" colspan='2' align='left'>
      <table>
        <tr>
	  <td><b>Data geração:</b></td>
	  <td>
     <?php
     if(!isset($dtin_dia)){
       $dtin_dia = date('d',db_getsession('DB_datausu'));
     }
     if(!isset($dtin_mes)){
       $dtin_mes = date('m',db_getsession('DB_datausu'));
     }
     if(!isset($dtin_ano)){
       $dtin_ano = date('Y',db_getsession('DB_datausu'));
     }
     db_inputdata('dtin',$dtin_dia,$dtin_mes,$dtin_ano,true,'text',1);
       ?>
	  </td>
        </tr>
        <tr>
	  <td><b>Autoriza pgto.:</b></td>
	  <td>
     <?php
     if(!isset($deposito_dia)){
       $deposito_dia = date('d',db_getsession('DB_datausu'));
     }
     if(!isset($deposito_mes)){
       $deposito_mes = date('m',db_getsession('DB_datausu'));
     }
     if(!isset($deposito_ano)){
       $deposito_ano = date('Y',db_getsession('DB_datausu'));
     }
     db_inputdata('deposito',$deposito_dia,$deposito_mes,$deposito_ano,true,'text',1,"onchange='js_geradescr();'");
       ?>
	  </td>
        </tr>
      </table>
    </td>
  </tr>
  <tr>
    <td width="15%" colspan='1' align='right'>
      <strong>Banco:</strong>
    </td>
    <td width="35%" colspan='1' align='left'>
     <?php
     $arr_bancos = Array();
     $result_bancos = $cldb_bancos->sql_record($cldb_bancos->sql_query_empage(null,"distinct db90_codban,db90_descr","db90_descr"," e90_codmov is null "));
     $numrows_bancos = $cldb_bancos->numrows;
     for($i=0;$i<$numrows_bancos;$i++){
       db_fieldsmemory($result_bancos,$i);
//       db_msgbox($db90_codban);
       if($i==0 && !isset($db_bancos)){
	 $db_bancos = $db90_codban;
       }
       $arr_bancos[$db90_codban] = $db90_descr;
     }

     $qualdescr = "";
     if(isset($db_bancos) && isset($arr_bancos[$db_bancos])){
       $qualdescr = $arr_bancos[$db_bancos];
     }
     db_select("db_bancos",$arr_bancos,true,1,"onchange='js_reload();'");
     ?>
      <input name="geradescr" type="button" value="Atualizar descrição" onclick='js_geradescr();'>
    </td>
    <td align='left' colspan="2" nowrap>
      <input name="atualizar" type="submit"  value="Gerar arquivo TXT" onclick='return js_atualizar(1);'>
      <input name="desatualizar" type="submit" value="Cancelar selecionados" onclick='return js_atualizar(2);'>
      <input name="total" type="button" value="Ver totais" onclick='js_mostravalores();'>
    </td>
  </tr>
  <tr>
    <td colspan='4' align='center'>
      <iframe name="ordem" src='emp4_empageconfgera001_ordem.php?db_banco=<?=(@$db_bancos)?>' width="760" height="320" marginwidth="0" marginheight="0" frameborder="0">
      </iframe>
    </td>
  </tr>
  <tr>
    <td colspan='4' align='left'>
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
function js_geradescr(){
  data =     document.form1.deposito_dia.value;
  data+= "/"+document.form1.deposito_mes.value;
  data+= "/"+document.form1.deposito_ano.value;
  document.form1.e87_descgera.value = '<?=($qualdescr)?> '+data;
}
js_geradescr();
</script>
