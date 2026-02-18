<?php
require("libs/db_stdlib.php");
require("libs/db_conecta.php");
include("libs/db_sessoes.php");
include("libs/db_usuariosonline.php");
include("classes/db_itensregpreco_classe.php");
include("classes/db_adesaoregprecos_classe.php");
include("classes/db_orcorgao_classe.php");
include("classes/db_orcunidade_classe.php");
include("classes/db_orcdotacao_classe.php");
include("classes/db_orcsuplementacaoparametro_classe.php");
include("dbforms/db_funcoes.php");
require("libs/db_liborcamento.php");
require_once("libs/db_utils.php");

$clorcorgao = new cl_orcorgao;
$clorcunidade = new cl_orcunidade;
$clorcdotacao = new cl_orcdotacao;
$clorcsuplementacaoparametro = new cl_orcsuplementacaoparametro;
$clorcorgao->rotulo->label();
$clorcunidade->rotulo->label();
$clorcdotacao->rotulo->label();
$legacyQueryParams = [];
parse_str((string) ($_SERVER["QUERY_STRING"] ?? ""), $legacyQueryParams);
foreach ($legacyQueryParams as $legacyKey => $legacyValue) {
  if (!isset($$legacyKey)) {
    $$legacyKey = $legacyValue;
  }
}
db_postmemory($_POST);

if (isset($o58_coddot) && $o58_coddot != "") {
    $filtro = " and o58_coddot = $o58_coddot  ";
} else {
    $filtro = "";

    if (!empty($o40_orgao)) {
        $filtro .= " and o58_orgao = $o40_orgao ";
    }
    if (!empty($o41_unidade) && !empty($o40_orgao)) {
        $filtro .= " and o58_unidade = $o41_unidade ";
    }
    if (!empty($o52_funcao)) {
        $filtro .= " and o58_funcao = $o52_funcao ";
    }
    if (!empty($o53_subfuncao)) {
        $filtro .= " and o58_subfuncao = $o53_subfuncao ";
    }
    if (!empty($o54_programa)) {
        $filtro .= " and o58_programa = $o54_programa ";
    }
    if (!empty($o55_projativ)) {
        $filtro .= " and o58_projativ = $o55_projativ ";
    }
    if (!empty($o56_elemento)) {
        $filtro .= " and o56_elemento = '$o56_elemento'";
    }
    if (!empty($o58_codigo)) {
        $filtro .= " and o58_codigo = $o58_codigo ";
    }
}
$sql = " select fc_estruturaldotacao(" . db_getsession("DB_anousu") . ",o58_coddot) as dl_estrutural,
  o56_elemento,
  o58_coddot,
  o58_valor,
  o58_anousu
  from orcdotacao d
  inner join orcprojativ p on p.o55_anousu = " . db_getsession("DB_anousu") . " and p.o55_projativ = d.o58_projativ
  inner join orcelemento e on e.o56_codele = d.o58_codele and o56_anousu = o58_anousu
  where o58_instit =" . db_getsession("DB_instit") . "
  and o58_anousu =" . db_getsession('DB_anousu') . " $filtro
  order by dl_estrutural";

$rsDotacoes = db_query($sql);

$result = $clorcsuplementacaoparametro->sql_record($clorcsuplementacaoparametro->sql_query(db_getsession("DB_anousu"), "o134_orcamentoaprovado"));
db_fieldsmemory($result, 0);
?>
<html>

<head>
    <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta http-equiv="Expires" CONTENT="0">
    <script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/datagrid.widget.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/widgets/DBToogle.widget.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/widgets/windowAux.widget.js"></script>
    <script type="text/javascript" src="scripts/prototype.js"></script>
    <link href="estilos.css" rel="stylesheet" type="text/css">
    <link href="estilos/grid.style.css" rel="stylesheet" type="text/css">
</head>

<body bgcolor=#CCCCCC leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onLoad="a=1">

    <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td height="430" align="left" valign="top" bgcolor="#CCCCCC">
                <?php
                include(Modification::getFile("forms/db_frmmanutdotacaobloco.php"));
                ?>
            </td>
        </tr>
    </table>
    <?php
    db_menu(db_getsession("DB_id_usuario"), db_getsession("DB_modulo"), db_getsession("DB_anousu"), db_getsession("DB_instit"));
    ?>
</body>

</html>