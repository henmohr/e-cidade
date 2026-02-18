<?php
include("fpdf151/pdf.php");
include("libs/db_sql.php");
include("classes/db_empagemov_classe.php");
include("classes/db_empagemovconta_classe.php");
include("classes/db_db_bancos_classe.php");
$clempagemov = new cl_empagemov;
$clempagemovconta = new cl_empagemovconta;
$cldb_bancos = new cl_db_bancos;
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
$clrotulo->label("e81_codmov");
$clrotulo->label("e83_codtipo");
$clrotulo->label("e53_valor");
$clrotulo->label("e53_vlranu");
$clrotulo->label("e53_vlrpag");
$clrotulo->label("e96_codigo");

$legacyQueryParams = [];
parse_str((string) ($_SERVER['QUERY_STRING'] ?? ''), $legacyQueryParams);
foreach ($legacyQueryParams as $legacyKey => $legacyValue) {
  if (!isset($$legacyKey)) {
    $$legacyKey = $legacyValue;
  }
}

if($ordem == "a") {
  $desc_ordem = "Alfabética";
  $order_by = "z01_nome";
}else if($ordem == "b"){
  $desc_ordem = "Numérica";
  $order_by = "z01_numcgm";
}else{
  $desc_ordem = "Recurso";
  $order_by = "o15_codigo";
}

$result_bancos = $cldb_bancos->sql_record($cldb_bancos->sql_query_file($banco));
if($cldb_bancos->numrows==0){
  db_redireciona('db_erros.php?fechar=true&db_erro=Banco não encontrado.');
}
db_fieldsmemory($result_bancos,0);
$head3 = "RELATÓRIO DE ARQUIVOS A GERAR";
$head5 = "$db90_descr";
$head6 = "ORDEM $desc_ordem";


$dbwhere = " and c63_banco='$banco'";
$result = $clempagemov->sql_record($clempagemov->sql_query_txt(null,"pc63_conta,pc63_agencia,e80_codage,e50_codord,e50_data,e82_codord,o15_codigo,o15_descr,e81_codmov,e83_codtipo,e83_descr,e60_emiss,e60_numemp,e60_codemp,e82_codord,z01_numcgm,z01_nome,e81_valor","e83_codtipo,$order_by","e90_codmov is null $dbwhere")); 
$numrows= $clempagemov->numrows; 

if($clempagemov->numrows == 0){
  db_redireciona('db_erros.php?fechar=true&db_erro=Nenhum registro encontrado.');
}
//db_criatabela($result);exit;
$pdf = new PDF(); 
$pdf->Open(); 
$pdf->AliasNbPages(); 
$total = 0;
$pdf->setfillcolor(235);
$pdf->setfont('arial','b',8);
$troca = 1;
$alt = 4;
$total = 0;
$cor = 1;

$antigaconta = "";
$valorconta  = 0;

$valdep = 0;
$valdoc = 0;
$valted = 0;

for($x=0;$x<$numrows;$x++){
  db_fieldsmemory($result,$x);

  $result_movconta = $clempagemovconta->sql_record($clempagemovconta->sql_query_conta($e81_codmov,"pc63_banco as banco,pc63_agencia as agencia,pc63_agencia_dig as digito,pc63_conta as conta,pc63_conta_dig as digitoc "));
  $numrows_movconta = $clempagemovconta->numrows;
  if($numrows_movconta>0){
    db_fieldsmemory($result_movconta,0);
    if(trim($digito)!=""){
      $digito = "-$digito";
    }
    if(trim($digitoc)!=""){
      $digitoc = "-$digitoc";
    }
  }

  if(trim($db_banco)==trim($banco)){
    $codigopagamento = "DEP";
  }else if($e81_valor<5000){
    $codigopagamento = "DOC";
  }else{
    $codigopagamento = "TED";
  }

  if($cor==1){
    $cor=0;
  }else{
    $cor=1;
  }

  // $pdf->cell(70,$alt,$e83_descr,0,0,"L",$cor);
  if($pdf->gety() > $pdf->h - 30 || $troca != 0 ){
    $pdf->addpage("L");
    $pdf->setfont('arial','b',8);
    $pdf->cell( 80,$alt,"Instituição",1,0,"C",1);
    $pdf->cell(165,$alt,"Fornecedor",1,1,"C",1);

    $pdf->cell(15,$alt,$RLe60_codemp,1,0,"C",1);
    $pdf->cell(15,$alt,$RLe50_codord,1,0,"C",1);
    $pdf->cell(50,$alt,"Recurso",1,0,"C",1);
    $pdf->cell(70,$alt,$RLz01_nome,1,0,"C",1);
    $pdf->cell(15,$alt,"Cód.Pgto.",1,0,"C",1);
    $pdf->cell(10,$alt,"Banco",1,0,"C",1);
    $pdf->cell(25,$alt,"Agência",1,0,"C",1);
    $pdf->cell(25,$alt,"Conta",1,0,"C",1);
    $pdf->cell(20,$alt,"Valor a pagar",1,1,"C",1);
    $total = 0;
    $troca = 0;
  }

  if($antigaconta != $e83_codtipo){
    if($antigaconta!=""){
      $pdf->setfont('arial','b',8);
      $pdf->cell(220,$alt,"Valor conta","T",0,"R",0);
      $pdf->cell( 25,$alt,db_formatar($valorconta,"f"),"T",1,"R",0);
      $pdf->cell(220,$alt,"Valor DEP",0,0,"R",0);
      $pdf->cell( 25,$alt,db_formatar($valdep,"f"),0,1,"R",0);
      $pdf->cell(220,$alt,"Valor DOC",0,0,"R",0);
      $pdf->cell( 25,$alt,db_formatar($valdoc,"f"),0,1,"R",0);
      $pdf->cell(220,$alt,"Valor TED","B",0,"R",0);
      $pdf->cell( 25,$alt,db_formatar($valted,"f"),"B",1,"R",0);
      $valdep = 0;
      $valdoc = 0;
      $valted = 0;
    }
    $valorconta = 0;
    $pdf->ln(3);
    $pdf->cell(245,$alt,$e83_codtipo." - ".$e83_descr,1,1,"L",1);
    $antigaconta = $e83_codtipo;
  }
  
  $pdf->setfont('arial','',7);
  $pdf->cell(15,$alt,$e60_numemp,0,0,"C",$cor);
  $pdf->cell(15,$alt,$e50_codord,0,0,"C",$cor);
  $pdf->cell(50,$alt,$o15_codigo.'-'.$o15_descr,0,0,"L",$cor);
  $pdf->cell(70,$alt,$z01_nome,0,0,"L",$cor);
  $pdf->cell(15,$alt,$codigopagamento,0,0,"C",$cor);
  $pdf->cell(10,$alt,$banco,0,0,"C",$cor);
  $pdf->cell(25,$alt,$agencia.$digito,0,0,"R",$cor);
  $pdf->cell(25,$alt,$conta.$digitoc,0,0,"R",$cor);
  $pdf->cell(20,$alt,db_formatar($e81_valor,"f"),0,1,"R",$cor);
  $total++;
  $valorconta+= $e81_valor;  
  if(trim($db_banco)==trim($banco)){
    $valdep += $e81_valor;
    $totdep += $e81_valor;
  }else if($e81_valor<5000){
    $valdoc += $e81_valor;
    $totdoc += $e81_valor;
  }else{
    $valted += $e81_valor;
    $totted += $e81_valor;
  }
}

$pdf->setfont('arial','b',8);
$pdf->cell(220,$alt,"Valor conta","T",0,"R",0);
$pdf->cell( 25,$alt,db_formatar($valorconta,"f"),"T",1,"R",0);
$pdf->cell(220,$alt,"Valor DEP",0,0,"R",0);
$pdf->cell( 25,$alt,db_formatar($valdep,"f"),0,1,"R",0);
$pdf->cell(220,$alt,"Valor DOC",0,0,"R",0);
$pdf->cell( 25,$alt,db_formatar($valdoc,"f"),0,1,"R",0);
$pdf->cell(220,$alt,"Valor TED","B",0,"R",0);
$pdf->cell( 25,$alt,db_formatar($valted,"f"),"B",1,"R",0);
$pdf->Output();
?>
