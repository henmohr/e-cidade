<?php
include("fpdf151/pdf.php");
include("libs/db_sql.php");
include("classes/db_empage_classe.php");
$clempage = new cl_empage;

$legacyQueryParams = [];
parse_str((string) ($_SERVER["QUERY_STRING"] ?? ""), $legacyQueryParams);
foreach ($legacyQueryParams as $legacyKey => $legacyValue) {
  if (!isset($$legacyKey)) {
    $$legacyKey = $legacyValue;
  }
}
//db_postmemory($HTTP_SERVER_VARS,2);
$clrotulo = new rotulocampo;
$clrotulo->label("e60_numemp");
$clrotulo->label("e60_codemp");
$clrotulo->label("z01_numcgm");
$clrotulo->label("z01_nome");
$clrotulo->label("e80_data");
$clrotulo->label("e82_codord");
$clrotulo->label("e60_vlremp");
$clrotulo->label("e60_vlrliq");
$clrotulo->label("e60_vlrpag");
$clrotulo->label("e81_valor");

$clrotulo->label("e81_codmov");
$clrotulo->label("e81_numemp");
$clrotulo->label("pc63_banco");
$clrotulo->label("pc63_agencia");
$clrotulo->label("pc63_conta");
$clrotulo->label("z01_cgccpf");
$clrotulo->label("o58_codigo");

$clrotulo->label('k17_codigo');
$clrotulo->label('k17_data');
$clrotulo->label('k17_debito');
$clrotulo->label('k17_credito');
$clrotulo->label('k17_valor');
$clrotulo->label('k17_hist');
$clrotulo->label('k17_texto');
$clrotulo->label('k17_dtaut');
$clrotulo->label('k17_autent');
$clrotulo->label('c60_descr');

$dbwhere ="  e80_codage =$e80_codage ";

$sql = $clempage->sql_query_cons(null,"
	e85_codtipo,e83_descr,e60_codemp,e82_codord,e81_valor,e81_codmov,e81_numemp,e81_valor,e82_codord,e60_codemp,
        e60_vlranu,e60_vlrliq,e60_vlrpag,e81_valor,e81_valor,e60_vlrpag,e60_anousu,e60_coddot,e83_codtipo,
	case when trim(a.z01_numcgm) is not null then a.z01_numcgm else cgm.z01_numcgm end as z01_numcgm,
	case when trim(a.z01_nome) is not null then a.z01_nome else cgm.z01_nome end as z01_nome,
	case when trim(a.z01_cgccpf) is not null then a.z01_cgccpf else cgm.z01_cgccpf end as z01_cgccpf
        ","",$dbwhere);
if($tipo == 'c'){
  $head5 = 'DADOS DA CONTA';
  $ordem = 'order by e83_codtipo,z01_nome';
}else{
  $head5 = 'DADOS DO EMPENHO';
  $ordem = 'order by z01_nome';
}

if($form=="t"){
  $head6 = 'IMPRESSÃO POR CONTA PAGADORA';
}else{
  $head6 = 'IMPRESSÃO POR RECURSO';
  $ordem = 'order by o15_codigo,z01_nome';
}
//////////////////////////////////////////////////////////////////////////////////////
/* início do select que busca agenda or ordens                                      */
//////////////////////////////////////////////////////////////////////////////////////
$sql1 = "select x.*,orctiporec.*,pcfornecon.*,o58_codigo,
                case when  pc63_cnpjcpf = 0 or trim(pc63_cnpjcpf) = '' then z01_cgccpf else pc63_cnpjcpf end as cnpj
         from ($sql) as x
              left join empagemovconta on empagemovconta.e98_codmov = x.e81_codmov
              left join pcfornecon on x.z01_numcgm = pc63_numcgm
                        and empagemovconta.e98_contabanco = pcfornecon.pc63_contabanco
	      inner join orcdotacao on e60_anousu = o58_anousu
	                           and e60_coddot = o58_coddot
              inner join orctiporec on  orctiporec.o15_codigo =  orcdotacao.o58_codigo 				   
	 $ordem
	 
	      ";
//die($sql1);

/*
	      left join pcfornecon on x.z01_numcgm = pc63_numcgm
	      inner join orcdotacao on e60_anousu = o58_anousu
	                           and e60_coddot = o58_coddot
              left join empagemovconta on empagemovconta.e98_codmov = x.e81_codmov
              left join pcfornecon on x.z01_numcgm = pc63_numcgm
                        and empagemovconta.e98_contabanco = pcfornecon.pc63_contabanco
*/
//die($sql1);
$result  = $clempage->sql_record($sql1);
$numrows = $clempage->numrows;
//////////////////////////////////////////////////////////////////////////////////////
/* final do select que busca agenda ou ordens                                       */
//////////////////////////////////////////////////////////////////////////////////////

//////////////////////////////////////////////////////////////////////////////////////
/* início do select que busca agenda ou slips                                       */
//////////////////////////////////////////////////////////////////////////////////////
/*
echo ( " select slip.k17_codigo,
                             k17_data,
                             k17_debito,
	                     c1.c60_descr as debito_descr,
		             k17_credito,
			     c2.c60_descr as credito_descr,
			     k17_valor,
			     k17_hist,
			     k17_texto,
			     k17_dtaut,
			     k17_autent,
			     z01_numcgm,
			     z01_nome
                      from slip
                             inner join empageslip on empageslip.e89_codigo = slip.k17_codigo
                             inner join empagemov on empagemov.e81_codmov = empageslip.e89_codmov
	                     inner join conplanoreduz r1 on r1.c61_reduz = k17_debito
	                     inner join conplano c1 on c1.c60_codcon = r1.c61_codcon
	                     inner join conplanoreduz r2 on r2.c61_reduz = k17_credito
		             inner join conplano c2 on c2.c60_codcon = r2.c61_codcon
		             left  join slipnum on slipnum.k17_codigo = slip.k17_codigo
		             left  join cgm on cgm.z01_numcgm = slipnum.k17_numcgm
                      where e81_codage = $e80_codage
		      order by slip.k17_codigo" );
die();
*/
$result1 = pg_query( " select slip.k17_codigo,
                             k17_data,
                             k17_debito,
	                     c1.c60_descr as debito_descr,
		             k17_credito,
			     c2.c60_descr as credito_descr,
			     k17_valor,
			     k17_hist,
			     k17_texto,
			     k17_dtaut,
			     k17_autent,
			     z01_numcgm,
			     z01_nome
                      from slip
                             inner join empageslip on empageslip.e89_codigo = slip.k17_codigo
                             inner join empagemov on empagemov.e81_codmov = empageslip.e89_codmov
	                     inner join conplanoreduz r1 on r1.c61_reduz = k17_debito
	                     inner join conplano c1 on c1.c60_codcon = r1.c61_codcon
	                     left  join conplanoreduz r2 on r2.c61_reduz = k17_credito
		             left  join conplano c2 on c2.c60_codcon = r2.c61_codcon
		             left  join slipnum on slipnum.k17_codigo = slip.k17_codigo
		             left  join cgm on cgm.z01_numcgm = slipnum.k17_numcgm
                      where e81_codage = $e80_codage
		      order by slip.k17_codigo" );
$numrows2 = pg_numrows($result1);
//////////////////////////////////////////////////////////////////////////////////////
/* final do select que busca agenda ou slips                                        */
//////////////////////////////////////////////////////////////////////////////////////
if($numrows==0 && $numrows2==0){
  db_redireciona("db_erros.php?fechar=true&db_erro=Nenhum Registro Encontrado ! ");
}
//db_criatabela($result);exit;

$sql = $clempage->sql_query_file(null,"*","",$dbwhere);
$result01  = $clempage->sql_record($sql);
if($clempage->numrows==0){
  db_redireciona("db_erros.php?fechar=true&db_erro=Nenhum Registro Encontrado ! ");
}
db_fieldsmemory($result01,0,true);

$head3 = "AGENDA: $e80_codage";
$head8 = "DATA  : ".$e80_data;

$pdf = new PDF(); 
$pdf->Open(); 
$pdf->AliasNbPages(); 
$pdf->setfillcolor(235);
$pdf->setfont('arial','b',8);

if($tipo == 'e'){
   $troca = 1;
   $alt = 4;
   $total = 0;
   
   $pagina = 1;
   for($i=0;$i<$numrows;$i++){

     db_fieldsmemory($result,$i,true);

     if($pdf->gety()>$pdf->h-30 || $pagina ==1){
       $pagina = 0;
       $pdf->addpage("L");
       $pdf->setfont('arial','b',7);
       
       $pdf->cell(10,$alt,$RLe82_codord,1,0,"C",1);
       $pdf->cell(15,$alt,$RLe60_codemp,1,0,"C",1);
       $pdf->cell(15,$alt,'Recurso',1,0,"C",1);
       $pdf->cell(15,$alt,$RLz01_numcgm,1,0,"C",1);
       $pdf->cell(70,$alt,$RLz01_nome,1,0,"C",1);
       $pdf->cell(20,$alt,$RLe60_vlremp,1,0,"C",1);
       $pdf->cell(20,$alt,$RLe60_vlrliq,1,0,"C",1);
       $pdf->cell(20,$alt,$RLe60_vlrpag,1,0,"C",1);
       $pdf->cell(20,$alt,$RLe81_valor,1,0,"C",1);
       $pdf->cell(30,$alt,"Saldo a pagar",1,1,"C",1);
       $pdf->setfont('arial','',7);
     }
       $pdf->cell(10,$alt,$e82_codord,1,0,"C",0);
       $pdf->cell(15,$alt,$e60_codemp,1,0,"C",0);
       $pdf->cell(15,$alt,db_formatar($o58_codigo,'recurso'),1,0,"C",0);
       $pdf->cell(15,$alt,$z01_numcgm,1,0,"L",0);
       $pdf->cell(70,$alt,$z01_nome,1,0,"L",0);
       $pdf->cell(20,$alt,db_formatar($e60_vlremp-$e60_vlranu,"f"),1,0,"R",0);
       $pdf->cell(20,$alt,db_formatar($e60_vlrliq,"f"),1,0,"R",0);
       $pdf->cell(20,$alt,db_formatar($e60_vlrpag,"f"),1,0,"R",0);
       $pdf->setfont('arial','b',7);
       $pdf->cell(20,$alt,db_formatar($e81_valor,"f"),1,0,"R",0);
       $pdf->setfont('arial','',7);
       $pdf->cell(30,$alt,db_formatar($e81_valor-$e60_vlrpag,"f"),1,1,"R",0);
       $total += $e81_valor;
   }
   $pdf->cell(185,$alt,"T O T A L",1,0,"L",0);
   $pdf->setfont('arial','b',7);
   $pdf->cell(20,$alt,db_formatar($total,"f"),1,0,"L",0);
   $pdf->cell(30,$alt,'',1,1,"L",0);
}else{
   $total = 0;
   $alt = 4;
   
   $xvalor    = 0;
   $xvaltotal = 0;
   $xbanco    = '';
   $ant_codgera = "";
   $total_geral =0;
   $pagina =1;
   $pdf->addpage("L");
   if($numrows>0){
     for($i=0;$i<$numrows;$i++){
       db_fieldsmemory($result,$i);
       $pdf->setfont('arial','b',8);
       if($pdf->gety() > $pdf->h - 30 || $pagina ==1){
         $pagina = 0;
         if($pdf->gety() > $pdf->h - 30){
           $pdf->cell(260,0.1,"","T",1,"L",0);
           $pdf->addpage("L");
         }    
         $pdf->cell(20,$alt,"ARQUIVO",1,0,"C",1);
         $pdf->cell(250,$alt,"DESCRIÇÃO",1,1,"C",1);
         $pdf->cell(20,$alt,$RLe60_codemp  ,1,0,"C",0);
         $pdf->cell(20,$alt,$RLe82_codord  ,1,0,"C",0);
         $pdf->cell(65,$alt,$RLz01_nome    ,1,0,"C",0);
         $pdf->cell(15,$alt,$RLz01_numcgm  ,1,0,"C",0);
         $pdf->cell(30,$alt,$RLz01_cgccpf  ,1,0,"C",0);
         $pdf->cell(20,$alt,$RLe81_valor   ,1,0,"C",0);
         $pdf->cell(15,$alt,$RLpc63_banco  ,1,0,"C",0);
         $pdf->cell(15,$alt,$RLpc63_agencia,1,0,"C",0);
         $pdf->cell(30,$alt,$RLpc63_conta  ,1,0,"C",0);
         $pdf->cell(20,$alt,$RLe81_codmov  ,1,0,"C",0);
         $pdf->cell(20,$alt,$RLe81_numemp  ,1,1,"C",0);
       }
       if($form=="t"){
	 $testa = $e85_codtipo.'-'.$e83_descr;
       }else{
	 $testa = $o15_codigo.'-'.$o15_descr;
       }
       if($ant_codgera!=$testa){
         if($i !=0){
           $pdf->cell(150,$alt,'Total do Banco',1,0,"C",1);
           $pdf->cell(20,$alt,db_formatar($xtotal,'f'),1,0,"R",1);
           $pdf->cell(100,$alt,'',1,1,"C",1);
           $pdf->ln(4);
         }
         $pdf->ln(4);
	 if($form=="t"){
           $pdf->cell(20,$alt,$e85_codtipo,1,0,"C",1);
           $pdf->cell(250,$alt,$e83_descr,1,1,"L",1);
           $ant_codgera=$e85_codtipo.'-'.$e83_descr;
         }else{ 
           $pdf->cell(20,$alt,$o15_codigo,1,0,"C",1);
           $pdf->cell(250,$alt,$o15_descr,1,1,"L",1);
           $ant_codgera=$o15_codigo.'-'.$o15_descr;
         }
         $xtotal = 0;
       }
       $pdf->setfont('arial','',7);
       $pdf->cell(20,$alt,$e60_codemp  ,1,0,"C",0);
       $pdf->cell(20,$alt,$e82_codord  ,1,0,"C",0);
       $pdf->cell(65,$alt,$z01_nome    ,1,0,"L",0);
       $pdf->cell(15,$alt,$z01_numcgm  ,1,0,"R",0);
       $pdf->cell(30,$alt,$cnpj        ,1,0,"R",0);
       $pdf->cell(20,$alt,db_formatar($e81_valor,'f'),1,0,"R",0);
       $pdf->cell(15,$alt,$pc63_banco  ,1,0,"C",0);
       $pdf->cell(15,$alt,$pc63_agencia.($pc63_agencia_dig!=''?'-'.$pc63_agencia_dig:''),1,0,"R",0);
       $pdf->cell(30,$alt,$pc63_conta  ,1,0,"R",0);
       $pdf->cell(20,$alt,$e81_codmov  ,1,0,"C",0);
       $pdf->cell(20,$alt,$e81_numemp  ,1,1,"C",0);
       $total++;
       $xtotal      += $e81_valor;
       $xvaltotal   += $e81_valor;
       //  $ant_codgera = $e87_codgera;
      }
      $pdf->setfont('arial','b',8);
      $pdf->cell(150,$alt,'Total do Banco',1,0,"C",1);
      $pdf->cell(20,$alt,db_formatar($xtotal,'f'),1,0,"R",1);
      $pdf->cell(100,$alt,'',1,1,"C",1);
      
      $pdf->cell(150,$alt,'Total Geral',1,0,"C",1);
      $pdf->cell(20,$alt,db_formatar($xvaltotal,'f'),1,0,"R",1);
      $pdf->cell(100,$alt,'',1,1,"C",1);
      //$pdf->cell(260,$alt,"TOTAL DE REGISTROS  : ".$total,"T",1,"L",0);
    }

   if($numrows2>0){
    $total = 0;
    $pdf->setfillcolor(235);
    $pdf->setfont('arial','b',8);
    $troca = 1;
    $prenc = 0;
    $alt = 4;
    $total = 0;

    for($x = 0; $x < $numrows2;$x++){
       db_fieldsmemory($result1,$x);
       if ($pdf->gety() > $pdf->h - 30 || $troca != 0 ){
          if ($pdf->gety() > $pdf->h - 30){
	    $pdf->addpage("L");
          }
	  $pdf->setfont('arial','b',8);
	  $pdf->cell(30,$alt,$RLk17_codigo,1,0,"C",1);
	  $pdf->cell(30,$alt,$RLk17_data,1,0,"C",1); 
	  $pdf->cell(40,$alt,$RLk17_valor,1,0,"C",1); 
	  $pdf->cell(30,$alt,'Data Aut.',1,0,"C",1); 
	  $pdf->cell(148,$alt,$RLk17_texto,1,1,"C",1);

	  $pdf->cell(15,$alt,"C. Débito",1,0,"C",1); 
	  $pdf->cell(90,$alt,$RLc60_descr,1,0,"C",1); 
	  $pdf->cell(15,$alt,"C. Crédito",1,0,"C",1); 
	  $pdf->cell(90,$alt,$RLc60_descr,1,0,"C",1); 
	  $pdf->cell(68,$alt,$RLz01_nome,1,1,"C",1); 

	  $troca = 0;
	  $prenc = 1;
       }
	 if ($prenc == 0){
	    $prenc = 1;
	   }else $prenc = 0;
       $pdf->setfont('arial','',7);
       $pdf->cell(30,$alt,$k17_codigo,0,0,"C",$prenc);
       $pdf->cell(30,$alt,db_formatar($k17_data,'d'),0,0,"C",$prenc); 
       $pdf->cell(40,$alt,db_formatar($k17_valor,'f'),0,0,"R",$prenc); 
       $pdf->cell(30,$alt,db_formatar($k17_dtaut,'d'),0,0,"C",$prenc); 
       $pdf->multicell(148,$alt,$k17_texto,0,"L",$prenc); 
       
       
       $pdf->cell(15,$alt,$k17_debito,0,0,"C",$prenc); 
       $pdf->cell(90,$alt,$debito_descr,0,0,"L",$prenc); 
       $pdf->cell(15,$alt,$k17_credito,0,0,"C",$prenc); 
       $pdf->cell(90,$alt,$credito_descr,0,0,"L",$prenc); 
       $pdf->cell(68,$alt,substr($z01_nome,0,35),0,1,"L",$prenc); 
       
       
    //     if ($prenc == 0){
    //        $prenc = 1;
    //       }else $prenc = 0;
       $total++;

    }

    $pdf->setfont('arial','b',8);
    $pdf->cell(278,$alt,'TOTAL DE REGISTROS  :  '.$total,"T",0,"L",0);
    }
}
$pdf->Output();
?>
