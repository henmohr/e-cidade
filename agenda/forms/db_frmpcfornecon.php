<?php
//MODULO: compras
include("dbforms/db_classesgenericas.php");
$cliframe_alterar_excluir = new cl_iframe_alterar_excluir;
$clpcfornecon->rotulo->label();
$clrotulo = new rotulocampo;
$clrotulo->label("pc60_dtlanc");
$clrotulo->label("nome");
$clrotulo->label("pc64_contabanco");
if(isset($db_opcaoal)){
   $db_opcao=33;
    $db_botao=false;
}else if(isset($opcao) && $opcao=="alterar"){
    $db_botao=true;
    $db_opcao = 2;
}else if(isset($opcao) && $opcao=="excluir"){
    $db_opcao = 3;
    $db_botao=true;
}else{
    $db_opcao = 1;
    $db_botao=true;
    if(isset($novo) || isset($alterar) ||   isset($excluir) || (isset($incluir) && $sqlerro==false ) ){
     $pc63_banco = "";
     $pc63_agencia = "";
     $pc63_agencia_dig = "";
     $pc63_conta = "";
     $pc63_id_usuario = "";
     $pc63_cnpjcpf = "";
     unset($pc64_contabanco);
   }
}

if($db_opcao == 1){
   $resultx =  $clpcforne->sql_record($clpcforne->sql_query($pc63_numcgm,"z01_cgccpf as pc63_cnpjcpf"));
   if($clpcforne->numrows>0){
     db_fieldsmemory($resultx,0);
   }
}
?>
<form name="form1" method="post" action="">
<center>
<table border="0">
  <tr>
    <td nowrap title="<?=@$Tpc63_numcgm?>">
       <?php
       db_ancora(@$Lpc63_numcgm,"js_pesquisapc63_numcgm(true);",3);
       ?>
    </td>
    <td colspan="3">
<?php
if(isset($submita)){
  db_input('submita',6,0,true,'hidden',3,"");
}
db_input('pc63_contabanco',6,$Ipc63_contabanco,true,'hidden',3,"");
db_input('pc63_numcgm',8,$Ipc63_numcgm,true,'text',3," onchange='js_pesquisapc63_numcgm(false);'")
?>
    </td>
  </tr>
  <tr>
    <td nowrap title="<?=@$Tpc63_banco?>">
       <?=@$Lpc63_banco?>
    </td>
    <td colspan="3">
<?php
db_input('pc63_banco',5,$Ipc63_banco,true,'text',$db_opcao,"")
?>
    </td>
  </tr>
  <tr>
    <td nowrap title="<?=@$Tpc63_agencia?>">
       <?=@$Lpc63_agencia?>
    </td>
    <td>
<?php
db_input('pc63_agencia',5,$Ipc63_agencia,true,'text',$db_opcao,"")
?>

    <td nowrap title="<?=@$Tpc63_agencia_dig?>">
       <b><?=@$RLpc63_agencia_dig?>:</b>
    </td>
    <td>

<?php
db_input('pc63_agencia_dig',2,$Ipc63_agencia_dig,true,'text',$db_opcao,"");
?>
    </td>
  </tr>
  <tr>
    <td nowrap title="<?=@$Tpc63_conta?>">
       <?=@$Lpc63_conta?>
    </td>
    <td>
<?php
db_input('pc63_conta',14,$Ipc63_conta,true,'text',$db_opcao,"")
?>
    </td>
    <td nowrap title="<?=@$Tpc63_conta_dig?>">
       <b><?=@$RLpc63_conta_dig?>:</b>
    </td>
    <td>
<?php
db_input('pc63_conta_dig',2,$Ipc63_conta_dig,true,'text',$db_opcao,"");
?>
    </td>
  </tr>
  <tr>
    <td nowrap title="<?=@$Tpc63_cnpjcpf?>">
       <?=@$Lpc63_cnpjcpf?>
    </td>
    <td colspan="3">
<?php
db_input('pc63_cnpjcpf',15,@$Ipc63_cnpjcpf,true,'text',$db_opcao," onBlur='js_verificaCGCCPF(this)'")
?>
    </td>
  </tr>
  <tr>
    <td nowrap title="<?=@$Tpc64_contabanco?>">
    <b>Conta padrão:</b>
    </td>
    <td>
<?php
$x = array("t"=>"SIM","f"=>"NÃO");
db_select('pc64_contabanco',$x,true,$db_opcao,"")
?>
    </td>
    <td nowrap title="<?=@$Tpc63_dataconf?>">
    <b><?php//=@$Lpc63_dataconf?>Conferido:</b>
    </td>
    <td>
    <?php
    $checked = "";
    if(isset($pc63_dataconf) && trim($pc63_dataconf)!=""){
      $checked = "checked";
    }

    $disabled = "";
    if($db_opcao==3 || $db_opcao==33){
      $disabled = "disabled";
    }
    ?>
    <input type='checkbox' name="conferido" <?=@$checked?> <?=@$disabled?>>
    </td>
  </tr>




  <tr>
    <td nowrap>
    </td>
    <td>
<?php
global $pc63_id_usuario ;
$pc63_id_usuario = db_getsession("DB_id_usuario") ;

db_input('pc63_id_usuario',5,$Ipc63_id_usuario,true,'hidden',3," onchange='js_pesquisapc63_id_usuario(false);'")
?>
    </td>
  <tr>
  </tr>
    <td colspan="4" align="center">
 <input name="<?=($db_opcao==1?"incluir":($db_opcao==2||$db_opcao==22?"alterar":"excluir"))?>" type="submit" id="db_opcao" value="<?=($db_opcao==1?"Incluir":($db_opcao==2||$db_opcao==22?"Alterar":"Excluir"))?>" <?=($db_botao==false?"disabled":"")?> onclick="return js_ver()"  >
 <input name="novo" type="button" id="cancelar" value="Novo" onclick="js_cancelar();" <?=($db_opcao==1||isset($db_opcaoal)?"style='visibility:hidden;'":"")?> >
<?php
   if(isset($novo)){?>
   <input name="fechar" type="button" value="Fechar" onclick="parent.db_iframe_pcfornecon.hide();">
<?php
   }
?>
    </td>
  </tr>
  </table>
 <table>
  <tr>
    <td valign="top"  align="center">
    <?php
	 $chavepri= array("pc63_numcgm"=>@$pc63_numcgm,"pc63_contabanco"=>@$pc63_contabanco);
	 $cliframe_alterar_excluir->chavepri=$chavepri;
	 $cliframe_alterar_excluir->sql     = $clpcfornecon->sql_query_file(null,'pc63_contabanco,pc63_numcgm,pc63_banco,pc63_agencia,pc63_agencia_dig,pc63_conta,pc63_conta_dig,pc63_cnpjcpf,pc63_id_usuario,pc63_dataconf','pc63_contabanco'," pc63_numcgm = $pc63_numcgm ");
	 $cliframe_alterar_excluir->campos  ="pc63_contabanco,pc63_numcgm,pc63_banco,pc63_agencia,pc63_agencia_dig,pc63_conta,pc63_conta_dig,pc63_cnpjcpf,pc63_id_usuario,pc63_dataconf";
	 $cliframe_alterar_excluir->legenda="ITENS LANÇADOS";
	 $cliframe_alterar_excluir->iframe_height ="160";
	 $cliframe_alterar_excluir->iframe_width ="700";
	 $cliframe_alterar_excluir->iframe_alterar_excluir($db_opcao);
    ?>
    </td>
   </tr>
 </table>
  </center>
</form>
<script>
function js_ver(){
  if(document.form1.pc63_cnpjcpf.value != 0 &&document.form1.pc63_cnpjcpf.value != '' && <?=$db_opcao?> !=3){
    return  js_verificaCGCCPF(document.form1.pc63_cnpjcpf);
  }else{
    return true;
  }
}

function js_cancelar(){
  var opcao = document.createElement("input");
  opcao.setAttribute("type","hidden");
  opcao.setAttribute("name","novo");
  opcao.setAttribute("value","true");
  document.form1.appendChild(opcao);
  document.form1.submit();
}
function js_pesquisapc63_numcgm(mostra){
  if(mostra==true){
    js_OpenJanelaIframe('CurrentWindow.corpo.iframe_pcfornecon','db_iframe_pcforne','func_pcforne.php?funcao_js=parent.js_mostrapcforne1|pc60_numcgm|pc60_dtlanc','Pesquisa',true,'0','1','775','390');
  }else{
     if(document.form1.pc63_numcgm.value != ''){
        js_OpenJanelaIframe('CurrentWindow.corpo.iframe_pcfornecon','db_iframe_pcforne','func_pcforne.php?pesquisa_chave='+document.form1.pc63_numcgm.value+'&funcao_js=parent.js_mostrapcforne','Pesquisa',false);
     }else{
       document.form1.pc60_dtlanc.value = '';
     }
  }
}
function js_mostrapcforne(chave,erro){
  document.form1.pc60_dtlanc.value = chave;
  if(erro==true){
    document.form1.pc63_numcgm.focus();
    document.form1.pc63_numcgm.value = '';
  }
}
function js_mostrapcforne1(chave1,chave2){
  document.form1.pc63_numcgm.value = chave1;
  document.form1.pc60_dtlanc.value = chave2;
  db_iframe_pcforne.hide();
}
function js_pesquisapc63_id_usuario(mostra){
  if(mostra==true){
    js_OpenJanelaIframe('CurrentWindow.corpo.iframe_pcfornecon','db_iframe_db_usuarios','func_db_usuarios.php?funcao_js=parent.js_mostradb_usuarios1|id_usuario|nome','Pesquisa',true,'0','1','775','390');
  }else{
     if(document.form1.pc63_id_usuario.value != ''){
        js_OpenJanelaIframe('CurrentWindow.corpo.iframe_pcfornecon','db_iframe_db_usuarios','func_db_usuarios.php?pesquisa_chave='+document.form1.pc63_id_usuario.value+'&funcao_js=parent.js_mostradb_usuarios','Pesquisa',false);
     }else{
       document.form1.nome.value = '';
     }
  }
}
function js_mostradb_usuarios(chave,erro){
  document.form1.nome.value = chave;
  if(erro==true){
    document.form1.pc63_id_usuario.focus();
    document.form1.pc63_id_usuario.value = '';
  }
}
function js_mostradb_usuarios1(chave1,chave2){
  document.form1.pc63_id_usuario.value = chave1;
  document.form1.nome.value = chave2;
  db_iframe_db_usuarios.hide();
}
</script>
