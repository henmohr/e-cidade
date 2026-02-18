<?php
include("fpdf151/pdf.php");
include("fpdf151/assinatura.php");
include("libs/db_sql.php");
include("dbforms/db_funcoes.php");
include("classes/db_empagegera_classe.php");
$clempagegera = new cl_empagegera;
$clrotulo = new rotulocampo;
$classinatura = new cl_assinatura;
$clempagegera->rotulo->label();
$clrotulo->label("e81_codmov");
$clrotulo->label("e82_codord");
$clrotulo->label("e81_valor");
$clrotulo->label("e81_numemp");
$clrotulo->label("pc63_banco");
$clrotulo->label("pc63_agencia");
$clrotulo->label("pc63_conta");
$clrotulo->label("e60_codemp");
$clrotulo->label("z01_nome");
$clrotulo->label("z01_numcgm");
$clrotulo->label("z01_cgccpf");
db_postmemory($_POST);
//db_postmemory($HTTP_SERVER_VARS,2);exit;

$HEAD3 = "RELATÓRIO DE ARQUIVOS GERADOS";
$HEAD5 = @$e87_codgera;
$HEAD6 = @$e87_descgera;
$e87_codgera = @$e87_codgera;
$xtipo = '';
if(isset($e83_codtipo) && $e83_codtipo!="0"){
  $HEAD3 = "TIPO";
  $HEAD5 = @$e83_codtipo;
  $HEAD6 = @$e83_codtipodescr;
//  $e87_codgera = "select distinct e90_codgera 
//                  from empageconfgera 
//	               inner join empagepag 
//	                 on e85_codmov=e90_codmov 
//	          where e85_codtipo = $e83_codtipo";
  $xtipo = ' and e85_codtipo = '.$e83_codtipo;
}

$pdf = new PDF();
$pdf->Open();
$pdf->AliasNbPages();
$pdf->setfillcolor(235);
$total = 0;
$alt = 4;

$head1 = $HEAD3;
$head3 = "ARQUIVO :  ".$HEAD5.' - '.$HEAD6 ;

if(isset($e87_codgera) && trim($e87_codgera)!="")
  $db_where = ' and empagegera.e87_codgera in ('.$e87_codgera.')';
  $sql  = " select empagemov.e81_codmov,
                   empagemov.e81_numemp,
                   empagemov.e81_valor,
                   empagemov.e81_codage,
		   e85_codtipo,
                   e60_codemp,
                   cgm.z01_nome,
		   e83_descr,
                   pc63_banco,
                   pc63_agencia,
                   pc63_agencia_dig,
                   pc63_conta_dig,
                   pc63_conta,
		   e82_codord,
		   case when  pc63_cnpjcpf = 0 or trim(pc63_cnpjcpf) = '' then z01_cgccpf else pc63_cnpjcpf end as cnpj,
		   z01_numcgm,
		   empagegera.e87_codgera,
		   empagegera.e87_data,
		   empagegera.e87_hora,
		   empageconf.e86_data,
		   c63_banco,
		   empagegera.e87_descgera
            from empagegera
                 inner join empageconfgera
                       on empageconfgera.e90_codgera = empagegera.e87_codgera
                 inner join empageconf
                       on empageconf.e86_codmov = empageconfgera.e90_codmov
                 inner join empagemov
                       on empagemov.e81_codmov = empageconfgera.e90_codmov
		 inner join empord
		       on e82_codmov = e81_codmov
                 inner join empagepag
                       on empagepag.e85_codmov = empagemov.e81_codmov
                 inner join empempenho
                       on empempenho.e60_numemp = empagemov.e81_numemp
                 inner join cgm
                       on cgm.z01_numcgm = empempenho.e60_numcgm
		 left join empagetipo 
		       on e85_codtipo = e83_codtipo
		 left join conplanoreduz 
		       on e83_conta = c61_reduz 
		 left join conplanoconta 
		       on c61_codcon = c63_codcon and c61_anousu = c63_anousu
                 left join pcfornecon
                       on cgm.z01_numcgm = pcfornecon.pc63_numcgm";
  $sql .= " where 1 = 1 $db_where  $xtipo
            order by e85_codtipo,e87_codgera,pc63_banco,pc63_agencia";
//  die($sql);

$result_empagegera = $clempagegera->sql_record($sql);
//db_criatabela($result_empagegera);
$numrows_empagegera = $clempagegera->numrows;

if($numrows_empagegera==0){ 
  db_redireciona("db_erros.php?fechar=true&db_erro=Nenhum registro encontrado.");
}

db_fieldsmemory($result_empagegera,0);

$head5 = "GERAÇÃO  :  ". db_formatar($e87_data,"d").' AS '.$e87_hora.' HS';
$head6 = "PAGAMENTO:  ".db_formatar($e86_data,"d");

if($c63_banco == '041'){
  $head7 = 'BANCO : 041 - BANRISUL';
}elseif($c63_banco == '001'){
  $head7 = 'BANCO : 001 - BANCO DO BRASIL';
}else{
  $head7 = 'BANCO : NÃO CADASTRADO';
}

//$head8 = 'AGENDA : '.$e81_codage;

$pdf->addpage("L");
$xvalor    = 0;
$xvaltotal = 0;
$xbanco    = '';
$ant_codgera = "";
$total_geral =0;

$soma_dep = 0;
$soma_doc = 0;
$soma_ted = 0;
$tota_dep = 0;
$tota_doc = 0;
$tota_ted = 0;

for($i=0;$i<$numrows_empagegera;$i++){
  db_fieldsmemory($result_empagegera,$i);
  $pdf->setfont('arial','b',8);
  if($pdf->gety() > $pdf->h - 30 || $i==0){
    if($pdf->gety() > $pdf->h - 30){
      $pdf->cell(260,0.1,"","T",1,"L",0);
      $pdf->addpage("L");
    }    
    $pdf->cell(20,$alt,"ARQUIVO",1,0,"C",1);
    $pdf->cell(255,$alt,"DESCRIÇÃO",1,1,"C",1);
    $pdf->cell(20,$alt,$RLe60_codemp,1,0,"C",0);
    $pdf->cell(20,$alt,$RLe82_codord,1,0,"C",0);
    $pdf->cell(65,$alt,$RLz01_nome,1,0,"C",0);
    $pdf->cell(15,$alt,$RLz01_numcgm,1,0,"C",0);
    $pdf->cell(30,$alt,$RLz01_cgccpf,1,0,"C",0);
    $pdf->cell(20,$alt,$RLe81_valor,1,0,"C",0);
    $pdf->cell(15,$alt,"Cod.Pgto.",1,0,"C",0);
    $pdf->cell(15,$alt,$RLpc63_banco,1,0,"C",0);
    $pdf->cell(15,$alt,$RLpc63_agencia,1,0,"C",0);
    $pdf->cell(20,$alt,$RLpc63_conta,1,0,"C",0);
    $pdf->cell(20,$alt,$RLe81_codmov,1,0,"C",0);
    $pdf->cell(20,$alt,$RLe81_numemp,1,1,"C",0);
  }
  if($ant_codgera!=$e85_codtipo.'-'.$e87_codgera){
    if($i !=0){
      $pdf->cell(150,$alt,'DEP',1,0,"C",1);
      $pdf->cell(20,$alt,db_formatar($soma_dep,'f'),1,0,"R",1);
      $pdf->cell(105,$alt,'',1,1,"C",1);

      $pdf->cell(150,$alt,'DOC',1,0,"C",1);
      $pdf->cell(20,$alt,db_formatar($soma_doc,'f'),1,0,"R",1);
      $pdf->cell(105,$alt,'',1,1,"C",1);

      $pdf->cell(150,$alt,'TED',1,0,"C",1);
      $pdf->cell(20,$alt,db_formatar($soma_ted,'f'),1,0,"R",1);
      $pdf->cell(105,$alt,'',1,1,"C",1);

      $pdf->cell(150,$alt,'Total Banco',1,0,"C",1);
      $pdf->cell(20,$alt,db_formatar($xtotal,'f'),1,0,"R",1);
      $pdf->cell(105,$alt,'',1,1,"C",1);
      $soma_dep = 0;
      $soma_doc = 0;
      $soma_ted = 0;

      $pdf->ln(4);
    }
    $pdf->ln(4);
    $pdf->cell(20,$alt,$e85_codtipo,1,0,"C",1);
    $pdf->cell(255,$alt,$e83_descr.'   ('.$e87_codgera.'-'.$e87_descgera.')',1,1,"L",1);
    $xtotal = 0;
    $ant_codgera=$e85_codtipo.'-'.$e87_codgera;
  }
  /*
  if($xbanco != $pc63_banco && $i !=0){
    $pdf->cell(125,$alt,'Total do Banco',1,0,"C",1);
    $pdf->cell(20,$alt,db_formatar($xtotal,'f'),1,0,"R",1);
    $pdf->cell(115,$alt,'',1,1,"C",1);
    $pdf->ln(4);
    $pdf->cell(20,$alt,$e87_codgera,1,0,"C",1);
    $pdf->cell(240,$alt,$e87_descgera,1,1,"L",1);
    $xbanco = $pc63_banco;
    $xtotal = 0;
  }
  */
  if($pc63_banco==$c63_banco){
    $codpgto   = "DEP";
    $soma_dep += $e81_valor;
    $tota_dep += $e81_valor;
  }else{
    if($e81_valor<5000){
      $codpgto   = "DOC";
      $soma_doc += $e81_valor;
      $tota_doc += $e81_valor;
    }else{
      $codpgto   = "TED";
      $soma_ted += $e81_valor;
      $tota_ted += $e81_valor;
    }
  }

  if(trim($pc63_agencia_dig)!=""){
    $pc63_agencia_dig = "-".$pc63_agencia_dig;
  }
  if(trim($pc63_conta_dig)!=""){
    $pc63_conta_dig = "-".$pc63_conta_dig;
  }
  $pdf->setfont('arial','',7);
  $pdf->cell(20,$alt,$e60_codemp,1,0,"C",0);
  $pdf->cell(20,$alt,$e82_codord,1,0,"C",0);
  $pdf->cell(65,$alt,$z01_nome,1,0,"L",0);
  $pdf->cell(15,$alt,$z01_numcgm,1,0,"R",0);
  $pdf->cell(30,$alt,$cnpj,1,0,"R",0);
  $pdf->cell(20,$alt,db_formatar($e81_valor,'f'),1,0,"R",0);
  $pdf->cell(15,$alt,$codpgto,1,0,"C",0);
  $pdf->cell(15,$alt,$pc63_banco,1,0,"C",0);
  $pdf->cell(15,$alt,$pc63_agencia.$pc63_agencia_dig,1,0,"R",0);
  $pdf->cell(20,$alt,$pc63_conta.$pc63_conta_dig,1,0,"R",0);
  $pdf->cell(20,$alt,$e81_codmov,1,0,"C",0);
  $pdf->cell(20,$alt,$e81_numemp,1,1,"C",0);
  $total++;
  $xtotal    += $e81_valor;
  $xvaltotal += $e81_valor;

//  $ant_codgera = $e87_codgera;
}

$pdf->setfont('arial','b',8);

$pdf->cell(150,$alt,'DEP',1,0,"C",1);
$pdf->cell(20,$alt,db_formatar($soma_dep,'f'),1,0,"R",1);
$pdf->cell(105,$alt,'',1,1,"C",1);

$pdf->cell(150,$alt,'DOC',1,0,"C",1);
$pdf->cell(20,$alt,db_formatar($soma_doc,'f'),1,0,"R",1);
$pdf->cell(105,$alt,'',1,1,"C",1);

$pdf->cell(150,$alt,'TED',1,0,"C",1);
$pdf->cell(20,$alt,db_formatar($soma_ted,'f'),1,0,"R",1);
$pdf->cell(105,$alt,'',1,1,"C",1);

$pdf->cell(150,$alt,'Total Banco',1,0,"C",1);
$pdf->cell(20,$alt,db_formatar($xtotal,'f'),1,0,"R",1);
$pdf->cell(105,$alt,'',1,1,"C",1);

$pdf->ln(4);
$pdf->cell(150,$alt,'Total DEP',1,0,"C",1);
$pdf->cell(20,$alt,db_formatar($tota_dep,'f'),1,0,"R",1);
$pdf->cell(105,$alt,'',1,1,"C",1);

$pdf->cell(150,$alt,'Total DOC',1,0,"C",1);
$pdf->cell(20,$alt,db_formatar($tota_doc,'f'),1,0,"R",1);
$pdf->cell(105,$alt,'',1,1,"C",1);

$pdf->cell(150,$alt,'Total TED',1,0,"C",1);
$pdf->cell(20,$alt,db_formatar($tota_ted,'f'),1,0,"R",1);
$pdf->cell(105,$alt,'',1,1,"C",1);

$pdf->cell(150,$alt,'Total Geral',1,0,"C",1);
$pdf->cell(20,$alt,db_formatar($xvaltotal,'f'),1,0,"R",1);
$pdf->cell(105,$alt,'',1,1,"C",1);
//$pdf->cell(260,$alt,"TOTAL DE REGISTROS  : ".$total,"T",1,"L",0);

$tes =  "______________________________"."\n"."Tesoureiro";
$pref =  "______________________________"."\n"."Prefeito";
//$ass_pref = $classinatura->assinatura(1000,$pref);
//$ass_pref = $classinatura->assinatura_usuario();
$ass_pref = $pref;
$ass_tes  = $classinatura->assinatura(1004,$tes);


//echo $ass_pref;
$largura = ( $pdf->w ) / 2;
$pdf->ln(10);
$pos = $pdf->gety();
$pdf->multicell($largura,3,$ass_pref,0,"C",0,0);
$pdf->setxy($largura,$pos);
$pdf->multicell($largura,3,$ass_tes,0,"C",0,0);
$pdf->Output();
?>
