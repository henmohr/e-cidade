<?php
require("libs/db_stdlib.php");
require("libs/db_conecta.php");
include("libs/db_sessoes.php");
include("libs/db_usuariosonline.php");
include("dbforms/db_funcoes.php");

include("libs/db_libcaixa_ze.php");
include("libs/db_libgertxt.php");
$cllayout_BBBS = new cl_layout_BBBS;

include("classes/db_empage_classe.php");
include("classes/db_empagetipo_classe.php");
include("classes/db_empagemov_classe.php");
include("classes/db_empord_classe.php");
include("classes/db_empagepag_classe.php");
include("classes/db_empageslip_classe.php");
include("classes/db_empagemovforma_classe.php");
include("classes/db_empagegera_classe.php");
include("classes/db_empageconf_classe.php");
include("classes/db_empageconfgera_classe.php");
include("classes/db_conplanoconta_classe.php");
include("classes/db_empagemod_classe.php");
include("classes/db_db_bancos_classe.php");
$clempage = new cl_empage;
$clempagetipo = new cl_empagetipo;
$clempagemov = new cl_empagemov;
$clempord = new cl_empord;
$clempagepag = new cl_empagepag;
$clempageslip = new cl_empageslip;
$clempagemovforma = new cl_empagemovforma;
$clempagegera = new cl_empagegera;
$clempageconf = new cl_empageconf;
$clempageconfgera = new cl_empageconfgera;
$clempagemod  = new cl_empagemod;
$cldb_bancos = new cl_db_bancos;

$legacyQueryParams = [];
parse_str((string) ($_SERVER["QUERY_STRING"] ?? ""), $legacyQueryParams);
foreach ($legacyQueryParams as $legacyKey => $legacyValue) {
  if (!isset($$legacyKey)) {
    $$legacyKey = $legacyValue;
  }
}
db_postmemory($_POST);

$db_opcao = 1;
$db_botao = false;

if(isset($e80_data_ano)){
  $data = "$e80_data_ano-$e80_data_mes-$e80_data_dia";
}

if(isset($atualizar)){
  $sqlerro = false;
  db_inicio_transacao();

  $sqlinst = "select * from db_config where codigo = ".db_getsession("DB_instit");
  $resultinst = pg_query($sqlinst);

  db_fieldsmemory($resultinst,0);


  if($sqlerro==false){
    $result_quantmod = $clempagepag->sql_record($clempagepag->sql_query_tipo(null,null,"distinct e84_codmod as codigomodelo","","e84_codmod <> 1 and e81_codmov in ($movs) "));
    if($clempagepag->numrows == 1){
      db_fieldsmemory($result_quantmod,0);
      $result_facilita  = $clempagemod->sql_record($clempagemod->sql_query_modforma(null,"distinct e81_codmov as codmovimento,e83_sequencia as tipsequencia,e83_conta,e83_codmod,e83_codtipo,c63_banco ","c63_banco","e84_codmod=$codigomodelo and e81_codmov in ($movs) "));
      $numrows_facilita = $clempagemod->numrows;
      if($sqlerro==false){
	$clempagegera->e87_descgera = "$e87_descgera";
	$clempagegera->e87_data     = "$dtin_ano-$dtin_mes-$dtin_dia";
	$clempagegera->e87_dataproc = "$deposito_ano-$deposito_mes-$deposito_dia";
	$clempagegera->e87_hora     = db_hora();
	$clempagegera->incluir(null);
	$erro_msg = $clempagegera->erro_msg;
	$arquivogera = $clempagegera->e87_codgera;
	if($clempagegera->erro_status==0){
	  $sqlerro = true;
	}
      }
      if($sqlerro==false){
	$result_atualseq = $clempagemod->sql_record($clempagemod->sql_query_file($codigomodelo,"e84_sequencia"));
	if($clempagemod->numrows>0){
	  db_fieldsmemory($result_atualseq,0);
	  $clempagemod->e84_codmod    = $codigomodelo;
	  $clempagemod->e84_sequencia = $e84_sequencia+1;
	  $clempagemod->alterar($codigomodelo);
	  if($clempagemod->erro_status==0){
	    $erro_msg = $clempagemod->erro_msg;
	    $sqlerro = true;
	  }
	}
      }

      for($i=0;$i<$numrows_facilita;$i++){
	db_fieldsmemory($result_facilita,$i);
	if($sqlerro==false){
	  $clempageconfgera->e90_codmov  = $codmovimento;
	  $clempageconfgera->e90_codgera = $arquivogera;
	  $clempageconfgera->e90_correto = "true";
	  $clempageconfgera->e90_dataret = "null";
	  $clempageconfgera->e90_codret  = "";
	  $clempageconfgera->incluir($codmovimento,$arquivogera);
	  if($clempageconfgera->erro_status==0){
	    $erro_msg = $clempageconfgera->erro_msg;
	    $sqlerro = true;
	  }
	}
      }
    }else if($clempagepag->numrows > 1){
      $sqlerro = true;
      $erro_msg = "Usuário:\\n\\nMais de um modelo cadastrado.\\n\\nAdministrador:";
    }
  }

  if($sqlerro==false && isset($arquivogera) && trim($arquivogera)!=""){
  //////////////////////////////////////////////////////////////////////////////
  /*                              começa layouts                              */
  $sql = "
  select  distinct
	  e90_codgera,
	  e90_codmov,
	  c63_banco,
	  c63_agencia,
	  c63_dvagencia,
	  c63_conta,
	  c63_dvconta,
	  pc63_agencia,
	  pc63_agencia_dig,
	  pc63_conta,
	  pc63_conta_dig,
	  translate(to_char(round(e81_valor,2),'99999999999.99'),'.','') as valor,
	  e81_valor as valorori,
	  case when  pc63_banco = c63_banco then '01' else '03' end as  lanc,
	  coalesce(pc63_banco,'000') as pc63_banco,
	  case when pc63_cnpjcpf = '0' or trim(pc63_cnpjcpf) = '' or pc63_cnpjcpf is null then length(trim(z01_cgccpf)) else length(trim(pc63_cnpjcpf)) end as tam,
	  convenio,
	  numcgm,
	  substr(z01_nome,1,40) as z01_nome,
	  case when  pc63_cnpjcpf = '0' or trim(pc63_cnpjcpf) = '' or pc63_cnpjcpf is null then z01_cgccpf else pc63_cnpjcpf end as z01_cgccpf,
	  cancelado
  from
	  (select e90_codgera,
		  e90_codmov,
		  c63_banco,
		  c63_agencia,
		  c63_dvagencia,
		  c63_conta,
		  c63_dvconta,
		  e83_convenio as convenio,
		  e81_valor,
		  case when e60_numcgm is null then k17_numcgm else e60_numcgm end as numcgm,
		  e88_codmov as cancelado

	  from empageconfgera
		  inner join empagemov on e90_codmov = e81_codmov
		  left join empempenho on e60_numemp = e81_numemp
		  inner join empagepag on e81_codmov = e85_codmov
		  inner join empagetipo on e85_codtipo = e83_codtipo
		  left join empageslip on e81_codmov = e89_codmov
		  inner join conplanoreduz on e83_conta = c61_reduz and c61_anousu=".db_getsession("DB_anousu")."
		  inner join conplanoconta on c63_codcon = c61_codcon and c63_anousu=".db_getsession("DB_anousu")."
		  left join slip on slip.k17_codigo = e89_codigo
		  left join slipnum on slipnum.k17_codigo = slip.k17_codigo
		  left join empageconfcanc on e88_codmov = e90_codmov
	  ) as x
	  left join cgm on z01_numcgm = numcgm
	  left join empagemovconta on e90_codmov = e98_codmov
	  left join pcfornecon on z01_numcgm = pc63_numcgm and pc63_contabanco = e98_contabanco

  where e90_codgera = $arquivogera
  order by c63_conta,lanc,e90_codmov
	 ";
    $result  =  @pg_query($sql);
    $numrows =  @pg_numrows($result);
    if($numrows==0){
      $sqlerro =true;
      $erro_msg = "Erro. Contate suporte";
    }
    if($sqlerro==false){
      ///////////////////////////////////////////////////////////////////////////////////////////////////////
      //////LAYOUTS//////////////////////////////////////////////////////////////////////////////////////////
      //// LAYOUT BANCO DO BANRISUL
      $registro = 0;
      db_fieldsmemory($result,0);


      if($codigomodelo==3 && $sqlerro == false){
	$data    =  $dtin_dia.$dtin_mes.$dtin_ano;
	$dat_cred = $deposito_dia.$deposito_mes.$deposito_ano;
	/// criar campo de sequencia do arquivo no empagetipo.
	$banco = db_formatar(str_replace('.','',str_replace('-','',$c63_banco)),'s','0',3,'e',0);

	$nomearquivo = 'pagt'.$c63_banco.'_'.date("Y-m-d",db_getsession("DB_datausu")).'_'.$e90_codgera.'.txt';
	//indica qual será o nome do arquivo

	$cllayout_BBBS->nomearq = "tmp/$nomearquivo";

	if(!is_writable("tmp/")){
	  $sqlerro= true;
	  $erro_msg = 'Sem permissão de gravar o arquivo. Contate suporte.';
	}
	if($banco == '001'){
	  $dbanco = db_formatar('BANCO DO BRASIL','s',' ',30,'d',0);
	}else{
	  $dbanco = db_formatar('BANRISUL','s',' ',30,'d',0);;
	}
	$agencia_pre = db_formatar(str_replace('.','',str_replace('-','',$c63_agencia)),'s','0',5,'e',0);

        $dvagencia_pre = "0";
	$dvconta_pre   = "0";
	$dvagconta_pre = "0";

	if(trim($c63_dvconta)!=""){
	  $digitos = strlen($c63_dvconta);
          $dvconta_pre   = $c63_dvconta[0];
	  if($digitos>1){
	    $dvagconta_pre = $c63_dvconta[1];
	  }
	}
	if(trim($c63_dvagencia)!=""){
	  $digitos1 = strlen($c63_dvagencia);
          $dvagencia_pre   = $c63_dvagencia[0];
	  if(isset($digitos) && $digitos==1){
	    $dvagconta_pre = $c63_dvagencia[0];
	    if($digitos1>1){
	      $dvagconta_pre = $c63_dvagencia[1];
	    }
	  }
	}

	$numero_dia = 2;
	$seq_arq = 1;

	///// HEADER DO ARQUIVO
	if($banco=="041"){
	  $conta_pre   = db_formatar(str_replace('.','',str_replace('-','',$c63_conta)),'s','0',10,'e',0);
	  $cllayout_BBBS->BSheaderA_001_003 = $banco;
	  $cllayout_BBBS->BSheaderA_004_007 = str_repeat('0',4);
	  $cllayout_BBBS->BSheaderA_008_008 = "0";
	  $cllayout_BBBS->BSheaderA_009_017 = str_repeat(' ',9);
	  $cllayout_BBBS->BSheaderA_018_018 = "2";
	  $cllayout_BBBS->BSheaderA_019_032 = $cgc;
	  $cllayout_BBBS->BSheaderA_033_037 = db_formatar(substr($convenio,0,5),'s',' ',5,'e',0);
	  $cllayout_BBBS->BSheaderA_038_052 = str_repeat(' ',15);
	  $cllayout_BBBS->BSheaderA_053_057 = $agencia_pre;
	  $cllayout_BBBS->BSheaderA_058_058 = "0";
	  $cllayout_BBBS->BSheaderA_059_061 = str_repeat('0',3);
	  $cllayout_BBBS->BSheaderA_062_071 = $conta_pre;
	  $cllayout_BBBS->BSheaderA_072_072 = "0";
	  $cllayout_BBBS->BSheaderA_073_102 = db_formatar(substr(strtoupper($nomeinst),0,30),'s',' ',30,'e',0);
	  $cllayout_BBBS->BSheaderA_103_132 = $dbanco;
	  $cllayout_BBBS->BSheaderA_133_142 = str_repeat(' ',10);
	  $cllayout_BBBS->BSheaderA_143_143 = "1";
	  $cllayout_BBBS->BSheaderA_144_151 = $data;
	  $cllayout_BBBS->BSheaderA_152_157 = date("H").date("i").date("s");
	  $cllayout_BBBS->BSheaderA_158_163 = db_formatar($e90_codgera,'s','0',6,'e',0);
	  $cllayout_BBBS->BSheaderA_164_166 = "030";
	  $cllayout_BBBS->BSheaderA_167_171 = str_repeat('0',5);
	  $cllayout_BBBS->BSheaderA_172_191 = str_repeat(' ',20);
	  $cllayout_BBBS->BSheaderA_192_211 = db_formatar($e90_codgera,'s',' ',20,'e',0);
	  $cllayout_BBBS->BSheaderA_212_240 = str_repeat(' ',29);
	  $cllayout_BBBS->geraHEADERArqBS();
        }else if($banco=="001"){
	  $conta_pre   = db_formatar(str_replace('.','',str_replace('-','',$c63_conta)),'s','0',12,'e',0);
	  $cllayout_BBBS->BBheaderA_001_003 = $banco;
	  $cllayout_BBBS->BBheaderA_004_007 = str_repeat('0',4);
	  $cllayout_BBBS->BBheaderA_008_008 = "0";
	  $cllayout_BBBS->BBheaderA_009_017 = str_repeat(' ',9);
	  $cllayout_BBBS->BBheaderA_018_018 = "2";
	  $cllayout_BBBS->BBheaderA_019_032 = $cgc;
	  $cllayout_BBBS->BBheaderA_033_052 = db_formatar(trim($convenio),'s',' ',20,'d',0);
	  $cllayout_BBBS->BBheaderA_053_057 = $agencia_pre;
	  $cllayout_BBBS->BBheaderA_058_058 = $dvagencia_pre;
	  $cllayout_BBBS->BBheaderA_059_070 = $conta_pre;
	  $cllayout_BBBS->BBheaderA_071_071 = $dvconta_pre;
	  $cllayout_BBBS->BBheaderA_072_072 = $dvagconta_pre;
	  $cllayout_BBBS->BBheaderA_073_102 = db_formatar(substr(strtoupper($nomeinst),0,30),'s',' ',30,'e',0);
	  $cllayout_BBBS->BBheaderA_103_132 = $dbanco;
	  $cllayout_BBBS->BBheaderA_133_142 = str_repeat(' ',10);
	  $cllayout_BBBS->BBheaderA_143_143 = "1";
	  $cllayout_BBBS->BBheaderA_144_151 = $data;
	  $cllayout_BBBS->BBheaderA_152_157 = date("H").date("i").date("s");
	  $cllayout_BBBS->BBheaderA_158_163 = db_formatar($e90_codgera,'s','0',6,'e',0);
	  $cllayout_BBBS->BBheaderA_164_166 = "030";
	  $cllayout_BBBS->BBheaderA_167_171 = str_repeat('0',5);
	  $cllayout_BBBS->BBheaderA_172_191 = str_repeat(' ',20);
	  $cllayout_BBBS->BBheaderA_192_211 = db_formatar($e90_codgera,'s',' ',20,'e',0);
	  $cllayout_BBBS->BBheaderA_212_222 = str_repeat(' ',11);
	  $cllayout_BBBS->BBheaderA_223_225 = str_repeat(' ',3);
	  $cllayout_BBBS->BBheaderA_226_228 = str_repeat('0',3);
	  $cllayout_BBBS->BBheaderA_229_230 = str_repeat(' ',2);
	  $cllayout_BBBS->BBheaderA_231_240 = str_repeat(' ',10);
	  $cllayout_BBBS->geraHEADERArqBB();
	}
	///// FINAL HEADER DO ARQUIVO

	$seq_header   = 0;
	$xconta       = '';
	$registro     = 1;
	$valor_header = 0;
	$xlanc        = 0;
	for($i = 0;$i < $numrows;$i++){
	  if($i == 0){
	    $pri = "$c63_banco";
	  }
	  db_fieldsmemory($result,$i);
	  if($xconta != $c63_conta || $lanc != $xlanc){
	    $xconta = $c63_conta;
	    $xlanc  = $lanc;
	    if($pri != $pc63_banco){
	      $pri = $pc63_banco;
	    }
	    if($pc63_banco == $banco){
	      $tiposerv = "20";
	      $tipopag = "01";
	    }else{
	      if($banco=="001" || $valorori<5000){
		$tiposerv = "20";
	      }else{
		$tiposerv = "12";
	      }
	      $tipopag = "03";
	    }

	    if($seq_header != 0){
	      ///// TRAILLER DO LOTE
	      $cllayout_BBBS->BBBStraillerL_001_003 = $banco;
	      $cllayout_BBBS->BBBStraillerL_004_007 = db_formatar($seq_header,'s','0',4,'e',0);
	      $cllayout_BBBS->BBBStraillerL_008_008 = '5';
	      $cllayout_BBBS->BBBStraillerL_009_017 = str_repeat(' ',9);
	      $cllayout_BBBS->BBBStraillerL_018_023 = db_formatar($seq_detalhe + 2,'s','0',6,'e',0);
	      $cllayout_BBBS->BBBStraillerL_024_041 = db_formatar(str_replace(',','',str_replace('.','',$valor_header)),'s','0',18,'e',0);
	      $cllayout_BBBS->BBBStraillerL_042_059 = str_repeat('0',18);
	      $cllayout_BBBS->BBBStraillerL_060_230 = str_repeat(' ',171);
	      $cllayout_BBBS->BBBStraillerL_231_240 = str_repeat(' ',10);
	      $cllayout_BBBS->geraTRAILLERLote();
	      $valor_header = 0;
	      $registro += 1;
	      ///// FINAL DO TRAILLER DO LOTE
	    }
	    $seq_header  += 1;
	    $seq_detalhe  = 0;
	    $registro    += 1;

	    $agencia_pre = db_formatar(str_replace('.','',str_replace('-','',$c63_agencia)),'s','0',5,'e',0);

	    $dvagencia_pre = "0";
	    $dvconta_pre   = "0";
	    $dvagconta_pre = "0";

	    if(trim($c63_dvconta)!=""){
	      $digitos = strlen($c63_dvconta);
	      $dvconta_pre   = $c63_dvconta[0];
	      if($digitos>1){
		$dvagconta_pre = $c63_dvconta[1];
	      }
	    }
	    if(trim($c63_dvagencia)!=""){
	      $digitos1 = strlen($c63_dvagencia);
	      $dvagencia_pre   = $c63_dvagencia[0];
	      if(isset($digitos) && $digitos==1){
		$dvagconta_pre = $c63_dvagencia[0];
		if($digitos1>1){
		  $dvagconta_pre = $c63_dvagencia[1];
		}
	      }
	    }

	    // HEADER DO LOTE
	    if($banco=="041"){
	      $conta_pre   = db_formatar(str_replace('.','',str_replace('-','',$c63_conta)),'s','0',10,'e',0);
	      $cllayout_BBBS->BSheaderL_001_003 = $banco;
	      $cllayout_BBBS->BSheaderL_004_007 = db_formatar($seq_header,'s','0',4,'e',0);
	      $cllayout_BBBS->BSheaderL_008_008 = "1";
	      $cllayout_BBBS->BSheaderL_009_009 = "C";
	      $cllayout_BBBS->BSheaderL_010_011 = $tiposerv;
	      $cllayout_BBBS->BSheaderL_012_013 = $tipopag;
	      $cllayout_BBBS->BSheaderL_014_016 = '020';
	      $cllayout_BBBS->BSheaderL_017_017 = ' ';
	      $cllayout_BBBS->BSheaderL_018_018 = '2';
	      $cllayout_BBBS->BSheaderL_019_032 = $cgc;
	      $cllayout_BBBS->BSheaderL_033_037 = db_formatar(substr($convenio,0,5),'s',' ',5,'e',0);
	      $cllayout_BBBS->BSheaderL_038_052 = str_repeat(' ',15);
	      $cllayout_BBBS->BSheaderL_053_057 = $agencia_pre;
	      $cllayout_BBBS->BSheaderL_058_061 = str_repeat('0',4);
	      $cllayout_BBBS->BSheaderL_062_071 = $conta_pre;
	      $cllayout_BBBS->BSheaderL_072_072 = " ";
	      $cllayout_BBBS->BSheaderL_073_102 = substr(strtoupper($nomeinst),0,30);
	      $cllayout_BBBS->BSheaderL_103_142 = str_repeat(' ',40);
	      $cllayout_BBBS->BSheaderL_143_172 = db_formatar(strtoupper($ender),'s',' ',30,'d',0);
	      $cllayout_BBBS->BSheaderL_173_177 = db_formatar($numero,'s',' ',5,'e',0);
	      $cllayout_BBBS->BSheaderL_178_192 = str_repeat(' ',15);
	      $cllayout_BBBS->BSheaderL_193_212 = db_formatar(strtoupper($munic),'s',' ',20,'d',0);
	      $cllayout_BBBS->BSheaderL_213_220 = db_formatar($cep,'s',' ',8,'e',0);
	      $cllayout_BBBS->BSheaderL_221_222 = $uf;
	      $cllayout_BBBS->BSheaderL_223_224 = str_repeat(' ',2);
	      $cllayout_BBBS->BSheaderL_225_240 = str_repeat(' ',16);
	      $cllayout_BBBS->geraHEADERLoteBS();
	    }else if($banco=="001"){
	      $conta_pre   = db_formatar(str_replace('.','',str_replace('-','',$c63_conta)),'s','0',12,'e',0);
	      $tamanho = strlen($cep);
	      if($tamanho>5){
		$com = db_formatar(substr($cep,5,$tamanho),'s',' ',3,'d',0);
		$cep = substr($cep,0,5);
	      }
	      $cllayout_BBBS->BBheaderL_001_003 = $banco;
	      $cllayout_BBBS->BBheaderL_004_007 = db_formatar($seq_header,'s','0',4,'e',0);
	      $cllayout_BBBS->BBheaderL_008_008 = "1";
	      $cllayout_BBBS->BBheaderL_009_009 = "C";
	      $cllayout_BBBS->BBheaderL_010_011 = $tiposerv;
	      $cllayout_BBBS->BBheaderL_012_013 = $tipopag;
	      $cllayout_BBBS->BBheaderL_014_016 = '020';
	      $cllayout_BBBS->BBheaderL_017_017 = ' ';
	      $cllayout_BBBS->BBheaderL_018_018 = '2';
	      $cllayout_BBBS->BBheaderL_019_032 = $cgc;
	      $cllayout_BBBS->BBheaderL_033_052 = db_formatar(trim($convenio),'s',' ',20,'d',0);
	      $cllayout_BBBS->BBheaderL_053_057 = $agencia_pre;
	      $cllayout_BBBS->BBheaderL_058_058 = $dvagencia_pre;
	      $cllayout_BBBS->BBheaderL_059_070 = $conta_pre;
	      $cllayout_BBBS->BBheaderL_071_071 = $dvconta_pre;
	      $cllayout_BBBS->BBheaderL_072_072 = $dvagconta_pre;
	      $cllayout_BBBS->BBheaderL_073_102 = substr(strtoupper($nomeinst),0,30);
	      $cllayout_BBBS->BBheaderL_103_142 = str_repeat(' ',40);
	      $cllayout_BBBS->BBheaderL_143_172 = db_formatar(strtoupper($ender),'s',' ',30,'d',0);
	      $cllayout_BBBS->BBheaderL_173_177 = db_formatar($numero,'s',' ',5,'e',0);
	      $cllayout_BBBS->BBheaderL_178_192 = str_repeat(' ',15);
	      $cllayout_BBBS->BBheaderL_193_212 = db_formatar(strtoupper(trim($munic)),'s',' ',20,'d',0);
	      $cllayout_BBBS->BBheaderL_213_217 = db_formatar($cep,'s',' ',5,'e',0);
	      $cllayout_BBBS->BBheaderL_218_220 = $com;
	      $cllayout_BBBS->BBheaderL_221_222 = $uf;
	      $cllayout_BBBS->BBheaderL_223_230 = str_repeat(' ',8);
              $cllayout_BBBS->BBheaderL_231_240 = str_repeat(' ',10);
	      $cllayout_BBBS->geraHEADERLoteBB();
	    }
	    // FINAL HEADER DO LOTE
	  }

	  $seq_detalhe += 1;
	  $tot_valor    = 0;
	  $numero_lote  = 1;

	  $tama =  strlen($pc63_conta);
	  if($tama>11){
	    $pc63_conta = substr($pc63_conta,($tama-11));
	  }

	  if($tam == 14){
	    $conf = 2;
	    $cgccpf = $z01_cgccpf;
	  }elseif($tam == 11){
	    $conf = 1;
	    $cgccpf = db_formatar($z01_cgccpf,'s','0',14,'e',0);
	  }else{
	    $conf = 3;
	    $cgccpf= str_repeat('0',14);
	  }

	  $registro += 1;
	  $compensacao = "   ";
	  if($pc63_banco == $banco || $valorori<5000){
	    if($banco=="001"){
	      $compensacao = "700";
	    }else{
	      $compensacao = "010";
	    }
	  }else{
	    if($valorori>=5000){
	      $compensacao = "018";
	    }
	  }

	  $agencia_fav = $pc63_agencia;
	  $conta_fav   = db_formatar(str_replace('.','',str_replace('-','',trim($pc63_conta))),'s','0',12,'e',0);

	  $dvagencia_fav = "0";
	  $dvconta_fav   = "0";
	  $dvagconta_fav = "0";

	  if(trim($pc63_conta_dig)!=""){
	    $digitos2 = strlen($pc63_conta_dig);
	    $dvconta_fav   = $pc63_conta_dig[0];
	    if($digitos2>1){
	      $dvagconta_fav = $pc63_conta_dig[1];
	    }
	  }
	  if(trim($pc63_agencia_dig)!=""){
	    $digitos3 = strlen($pc63_agencia_dig);
	    $dvagencia_fav   = $pc63_agencia_dig[0];
	    if(isset($digitos2) && $digitos2==1){
	      $dvagconta_fav = $pc63_agencia_dig[0];
	      if($digitos3>1){
	        $dvagconta_fav = $pc63_agencia_dig[1];
	      }
	    }
	  }

	  // REGISTROS
	  if($banco=="041"){
	    $cllayout_BBBS->BSregist_001_003 = $banco;
	    $cllayout_BBBS->BSregist_004_007 = db_formatar($seq_header,'s','0',4,'e',0);
	    $cllayout_BBBS->BSregist_008_008 = "3";
	    $cllayout_BBBS->BSregist_009_013 = db_formatar($seq_detalhe,'s','0',5,'e',0);
	    $cllayout_BBBS->BSregist_014_014 = "A";
	    $cllayout_BBBS->BSregist_015_015 = "0";
	    $cllayout_BBBS->BSregist_016_017 = "00";
	    $cllayout_BBBS->BSregist_018_020 = $compensacao;
	    $cllayout_BBBS->BSregist_021_023 = $pc63_banco;
	    $cllayout_BBBS->BSregist_024_028 = db_formatar($agencia_fav,'s','0',5,'e',0);
	    $cllayout_BBBS->BSregist_029_029 = "0";
	    $cllayout_BBBS->BSregist_030_042 = $conta_fav.$dvconta_fav;
	    $cllayout_BBBS->BSregist_043_043 = " ";
	    $cllayout_BBBS->BSregist_044_073 = db_formatar(str_replace("-",'',substr($z01_nome,0,30)),'s',' ',30,'d',0);
	    $cllayout_BBBS->BSregist_074_088 = db_formatar($e90_codmov,'s','0',15,'d',0);
	    $cllayout_BBBS->BSregist_089_093 = "00005";
	    $cllayout_BBBS->BSregist_094_101 = $dat_cred;
	    $cllayout_BBBS->BSregist_102_104 = "BRL";
	    $cllayout_BBBS->BSregist_105_119 = str_repeat('0',15);
	    $cllayout_BBBS->BSregist_120_134 = db_formatar(str_replace(',','',str_replace('.','',$valor)),'s','0',15,'e',0);
	    $cllayout_BBBS->BSregist_135_154 = str_repeat(' ',20);
	    $cllayout_BBBS->BSregist_155_162 = str_repeat(' ',8);
	    $cllayout_BBBS->BSregist_163_177 = str_repeat(' ',15);
	    $cllayout_BBBS->BSregist_178_182 = str_repeat(' ',5);
	    $cllayout_BBBS->BSregist_183_202 = str_repeat(' ',20);
	    $cllayout_BBBS->BSregist_203_203 = $conf;
	    $cllayout_BBBS->BSregist_204_217 = $cgccpf;
	    $cllayout_BBBS->BSregist_218_229 = str_repeat(' ',12);
	    $cllayout_BBBS->BSregist_230_230 = "0";
	    $cllayout_BBBS->BSregist_231_240 = str_repeat(' ',10);
	    $cllayout_BBBS->geraREGISTROSBS();
	  }else if($banco=="001"){
	    $cllayout_BBBS->BBregist_001_003 = $banco;
	    $cllayout_BBBS->BBregist_004_007 = db_formatar($seq_header,'s','0',4,'e',0);
	    $cllayout_BBBS->BBregist_008_008 = "3";
	    $cllayout_BBBS->BBregist_009_013 = db_formatar($seq_detalhe,'s','0',5,'e',0);
	    $cllayout_BBBS->BBregist_014_014 = "A";
	    $cllayout_BBBS->BBregist_015_015 = "0";
	    $cllayout_BBBS->BBregist_016_017 = "00";
	    $cllayout_BBBS->BBregist_018_020 = $compensacao;
	    $cllayout_BBBS->BBregist_021_023 = $pc63_banco;
	    $cllayout_BBBS->BBregist_024_028 = db_formatar(str_replace('.','',str_replace('-','',$agencia_fav)),'s','0',5,'e',0);
	    $cllayout_BBBS->BBregist_029_029 = $dvagencia_fav;
	    $cllayout_BBBS->BBregist_030_041 = $conta_fav;
	    $cllayout_BBBS->BBregist_042_042 = $dvconta_fav;
	    $cllayout_BBBS->BBregist_043_043 = $dvagconta_fav;
	    $cllayout_BBBS->BBregist_044_073 = db_formatar(str_replace("-",'',substr($z01_nome,0,30)),'s',' ',30,'d',0);
	    $cllayout_BBBS->BBregist_074_093 = db_formatar($e90_codmov,'s','0',20,'d',0);
	    $cllayout_BBBS->BBregist_094_101 = $dat_cred;
	    $cllayout_BBBS->BBregist_102_104 = "BRL";
	    $cllayout_BBBS->BBregist_105_119 = str_repeat('0',15);
	    $cllayout_BBBS->BBregist_120_134 = db_formatar(str_replace(',','',str_replace('.','',$valor)),'s','0',15,'e',0);
	    $cllayout_BBBS->BBregist_135_154 = str_repeat(' ',20);
	    $cllayout_BBBS->BBregist_155_162 = str_repeat(' ',8);
	    $cllayout_BBBS->BBregist_163_177 = str_repeat(' ',15);
	    $cllayout_BBBS->BBregist_178_217 = str_repeat(' ',40);
	    $cllayout_BBBS->BBregist_218_229 = str_repeat(' ',12);
	    $cllayout_BBBS->BBregist_230_230 = "0";
	    $cllayout_BBBS->BBregist_231_240 = str_repeat(' ',10);
	    $cllayout_BBBS->geraREGISTROSBB();
	  }
	  $valor_header += $valor;
	  // FINAL REGISTROS
	}


	///// TRAILLER DO LOTE
	$cllayout_BBBS->BBBStraillerL_001_003 = $banco;
	$cllayout_BBBS->BBBStraillerL_004_007 = db_formatar($seq_header,'s','0',4,'e',0);
	$cllayout_BBBS->BBBStraillerL_008_008 = '5';
	$cllayout_BBBS->BBBStraillerL_009_017 = str_repeat(' ',9);
	$cllayout_BBBS->BBBStraillerL_018_023 = db_formatar($seq_detalhe + 2,'s','0',6,'e',0);
	$cllayout_BBBS->BBBStraillerL_024_041 = db_formatar(str_replace(',','',str_replace('.','',$valor_header)),'s','0',18,'e',0);
	$cllayout_BBBS->BBBStraillerL_042_059 = str_repeat('0',18);
	$cllayout_BBBS->BBBStraillerL_060_230 = str_repeat(' ',171);
	$cllayout_BBBS->BBBStraillerL_231_240 = str_repeat(' ',10);
	$cllayout_BBBS->geraTRAILLERLote();
	$valor_header = 0;
	$registro += 1;
	///// FINAL DO TRAILLER DO LOTE


	////  TRAILLER DO ARQUIVO
	$registro += 1;
	$cllayout_BBBS->BBBStraillerA_001_003 = $banco;
	$cllayout_BBBS->BBBStraillerA_004_007 = '9999';
	$cllayout_BBBS->BBBStraillerA_008_008 = '9';
	$cllayout_BBBS->BBBStraillerA_009_017 = str_repeat(' ',9);
	$cllayout_BBBS->BBBStraillerA_018_023 = db_formatar($seq_header,'s','0',6,'e',0);
	$cllayout_BBBS->BBBStraillerA_024_029 = db_formatar($registro,'s','0',6,'e',0);
	$cllayout_BBBS->BBBStraillerA_230_035 = str_repeat('0',6);
	$cllayout_BBBS->BBBStraillerA_236_240 = str_repeat(' ',205);
	$cllayout_BBBS->geraTRAILLERArquivo();
	$cllayout_BBBS->gera();
	///// FINAL DO TRAILLER DO ARQUIVO


      }else if($sqlerro==false){
	$sqlerro  = true;
	$erro_msg = "O tipo selecionado, não possui layout cadastrado para pagar no Banco Banrisul.";
      }
    }
  }
  if(trim($nmov)==""){
    unset($db_bancos);
  }
//  $sqlerro = true;
  db_fim_transacao($sqlerro);
}else if(isset($desatualizar)){
  $sqlerro = false;
  db_inicio_transacao();
  $clempageconf->excluir(null,"e86_codmov in ($movs)");
  if($clempageconf->erro_status==0){
    $erro_msg = $clempageconf->erro_msg;
    $sqlerro = true;
  }
  if(trim($nmov)==""){
    unset($db_bancos);
  }
  db_fim_transacao($sqlerro);
}

//quando entra pela primeira vez
if(empty($e80_data_ano)){
  $e80_data_ano = date("Y",db_getsession("DB_datausu"));
  $e80_data_mes = date("m",db_getsession("DB_datausu"));
  $e80_data_dia = date("d",db_getsession("DB_datausu"));
  $data = "$e80_data_ano-$e80_data_mes-$e80_data_dia";
}

if(isset($data)){
    $result01 = $clempage->sql_record($clempage->sql_query_file(null,'e80_codage','',"e80_data='$data'"));
    $numrows01 = $clempage->numrows;
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
<table width="790" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td height="450" align="left" valign="top" bgcolor="#CCCCCC">
   <?php
	include("forms/db_frmempageconfgera.php");
   ?>
    </td>
  </tr>
</table>
<?php
db_menu(db_getsession("DB_id_usuario"),db_getsession("DB_modulo"),db_getsession("DB_anousu"),db_getsession("DB_instit"));
?>
</body>
</html>
<script>

function js_empage(){
    js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_empage','func_empage.php?funcao_js=parent.js_mostra|e80_codage|e80_data','Pesquisa',true);
}
function js_mostra(codage,data){
  arr = data.split('-');

  obj = document.form1;

  obj.e80_data_ano.value = arr[0];
  obj.e80_data_mes.value = arr[1];
  obj.e80_data_dia.value = arr[2];

            obj=document.createElement('input');
            obj.setAttribute('name','pri_codage');
            obj.setAttribute('type','hidden');
            obj.setAttribute('value',codage);
            document.form1.appendChild(obj);

  document.form1.pesquisar.click();

  db_iframe_empage.hide();

}
</script>
<?php
if(isset($atualizar) ){
  if($sqlerro == true){
    db_msgbox($erro_msg);
  }
  if(isset($nomearquivo)){
    echo "
    <script>
      function js_emitir(){
//        js_OpenJanelaIframe('CurrentWindow.corpo','e','tmp/$nomearquivo','Pesquisa',false,0,0,0,0 );

//        jan = window.open('tmp/$nomearquivo','',width=0,height=0,scrollbars=0,location=0 ');
        jan = window.open('tmp/$nomearquivo','','width='+(screen.availWidth-5)+',height='+(screen.availHeight-40)+',scrollbars=1,location=0 ');
        jan.moveTo(0,0);
      }
      js_emitir();
    </script>
    ";
  }
}
?>
<?php
/*
if((isset($atualizar) || isset($desatualizar)) && $sqlerro==true){
  db_msgbox($erro_msg);
}
*/
?>
