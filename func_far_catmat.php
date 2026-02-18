<?php
/*
 *     E-cidade Software Publico para Gestao Municipal
 *  Copyright (C) 2014  DBSeller Servicos de Informatica
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
include("libs/db_usuariosonline.php");
include("dbforms/db_funcoes.php");
use App\Models\MaterCatMat;
db_postmemory($_POST);
$legacyQueryParams = [];
parse_str((string) ($_SERVER["QUERY_STRING"] ?? ""), $legacyQueryParams);
foreach ($legacyQueryParams as $legacyKey => $legacyValue) {
  if (!isset($$legacyKey)) {
    $$legacyKey = $legacyValue;
  }
}
$clMaterCatMat = new MaterCatMat();

?>
<html>
<head>
    <meta http-equiv='Content-Type' content='text/html; charset=iso-8859-1'>
    <link href='estilos.css' rel='stylesheet' type='text/css'>
    <script language='JavaScript' type='text/javascript' src='scripts/scripts.js'></script>
</head>
<body>
<form name="form2" method="post" action="" class="container">
    <fieldset>
        <legend>Dados para Pesquisa</legend>
        <table width="45%" border="0" align="center" cellspacing="3" class="form-container">
            <tr>
                <td><label>Código:</label></td>
                <td><?php db_input("faxx_i_codigo",5,$Ifaxx_i_codigo,true,"text",4,"","chave_faxx_i_codigo"); ?></td>
            </tr>
            <tr>
                <td><label>Descrição:</label></td>
                <td width="96%" align="left" nowrap>
                    <?php
                    db_input ( "faxx_i_desc", 40, @$Ifaxx_i_desc, true, "text", 4, "", "chave_faxx_i_desc" );
                    ?>
                </td>
            </tr>
        </table>
    </fieldset>
    <input name="pesquisar" type="submit" id="pesquisar2" value="Pesquisar">
    <input name="limpar" type="reset" id="limpar" value="Limpar" >
    <input name="Fechar" type="button" id="fechar" value="Fechar" onClick="parent.db_iframe_far_catmat.hide();">
</form>
<?php

if (!isset($fa01_i_catmat) && !isset($codigo_barras))  {
    if (isset ( $chave_faxx_i_codigo ) && (trim ( $chave_faxx_i_codigo ) != "")) {
        $sql = $clMaterCatMat->sqlQueryCatmat('faxx_i_codigo,faxx_i_desc','far_matercatmat.faxx_i_codigo = '.$chave_faxx_i_codigo);
    } else if (isset ( $chave_faxx_i_desc ) && (trim ( $chave_faxx_i_desc ) != "")) {
        $sql = $clMaterCatMat->sqlQueryCatmat('faxx_i_codigo,faxx_i_desc','far_matercatmat.faxx_i_desc like \'%'.$chave_faxx_i_desc.'%\'');
    }else{
        $sql = $clMaterCatMat->sqlQueryAllCatMat('faxx_i_codigo,faxx_i_desc');
    }

    echo '<div class="container">';
    echo '  <fieldset>';
    echo '    <legend>Resultado da Pesquisa</legend>';
    db_loveloq($sql,15,"()","",$funcao_js,"","NoMe",$repassa);
    echo '  </fieldset>';
    echo '</div>';

}
?>
</body>
</html>
<?php
if(isset($fa01_i_catmat)){

    $result = $clMaterCatMat->getFirstCatMat('faxx_i_codigo,faxx_i_desc','far_matercatmat.faxx_i_codigo = '.$fa01_i_catmat);

    if($result){
        echo "<script>".$funcao_js."('$result->faxx_i_desc',false);</script>";
    }else{
        echo "<script>".$funcao_js."('Chave(".$pesquisa_chave.") não Encontrado',true);</script>";
    }
}
?>
<script>
    js_tabulacaoforms("form2","chave_faxx_i_codigo",true,1,"chave_faxx_i_codigo",true);
</script>
