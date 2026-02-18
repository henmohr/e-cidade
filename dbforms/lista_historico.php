<?php
/*
 *     E-cidade Software Publico para Gestao Municipal                
 *  Copyright (C) 2009  DBselller Servicos de Informatica             
 *                            www.dbseller.com.br                     
 *                         e-cidade@dbseller.com.br                   
 *                                                                    
 *  Este programa e software livre; voce pode redistribui-lo e/ou     
 *  modifica-lo sob os termos da Licenca Publica Geral GNU, conforme  
 *  publicada pela Free Software Foundation; tanto a versao 2 da      
 *  Licenca como (a seu criterio) qualquer versao mais nova.          
 *                                                                    
 *  Este programa e distribuido na expectativa de ser util, mas SEM   
 *  QUALQUER GARANTIA; sem mesmo a garantia implicita de              
 *  COMERCIALIZACAO ou de ADEQUACAO A QUALQUER PROPOSITO EM           
 *  PARTICULAR. Consulte a Licenca Publica Geral GNU para obter mais  
 *  detalhes.                                                         
 *                                                                    
 *  Voce deve ter recebido uma copia da Licenca Publica Geral GNU     
 *  junto com este programa; se nao, escreva para a Free Software     
 *  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA          
 *  02111-1307, USA.                                                  
 *  
 *  Copia da licenca no diretorio licenca/licenca_en.txt 
 *                                licenca/licenca_pt.txt 
 */

require("libs/db_stdlib.php");
require("libs/db_conecta.php");
include("libs/db_sessoes.php");
$legacyQueryParams = [];
parse_str((string) ($_SERVER['QUERY_STRING'] ?? ''), $legacyQueryParams);
foreach ($legacyQueryParams as $legacyKey => $legacyValue) {
  if (!isset($$legacyKey)) {
    $$legacyKey = $legacyValue;
  }
}
if(!isset($arg)) {
  $legacyDecodedQuery = [];
  parse_str(base64_decode((string) ($_SERVER['QUERY_STRING'] ?? '')), $legacyDecodedQuery);
  foreach ($legacyDecodedQuery as $legacyKey => $legacyValue) {
    if (!isset($$legacyKey)) {
      $$legacyKey = $legacyValue;
    }
  }
}
if(isset($retorno)) {
  $ret = explode("##",$retorno);
  echo "
  <script>
    opener.parent.corpo.document.forms[0].k02_estrut.value = '".$ret[0]."';
    opener.parent.corpo.document.form1.k02_drecei.value = '".$ret[1]."';
    opener.parent.corpo.document.form1.k02_descr.focus();
	window.close();
  </script>
  ";
}
if(isset($_POST["arg"]))
  $arg = $_POST["arg"];
  if($arg == 'O') {
    $sql = "select (o02_codigo || '##' || o02_descr) as db_codigo,o02_codigo,o02_descr,o02_valor,o02_codtce,o02_percen
          from orcam
		  where o02_anousu = ".db_getsession("DB_anousu")."
		  and o02_codigo like '".$_POST["filtro"]."%'";
  } else if($arg == 'E') {
    $sql = "select (c01_estrut::varchar(13) || '##' || c01_descr) as db_codigo,c01_estrut,c01_descr
	      from plano
	  	  where c01_anousu = ".db_getsession("DB_anousu")."
		  and c01_estrut like '".$_POST["filtro"]."%'";
  }
?>
<html>
<head>
<title>Lista de Valores</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onFocus="document.form5.filtro.focus()">
<center>
<table border="0" cellspacing="5" cellpadding="0">
<tr>
<td align="center" nowrap>

<form name="form5" method="post">
  <input type="text" name="filtro" value="<?=@$_POST['filtro']?>" onBlur="window.focus();">
  <input type="hidden" name="arg" value="<?=@$arg?>">
  <input type="submit" name="procurar" value="Procurar">
</form>
</td>
</tr>
<tr>
<td align="center">
<?php
db_lov($sql,15,"lista_historico.php",$_POST["filtro"]);
?>
</td>
</tr>
</table>
</center>
</body>
</html>