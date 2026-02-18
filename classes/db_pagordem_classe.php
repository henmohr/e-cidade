<?php
/*
 *     E-cidade Software Publico para Gestao Municipal
 *  Copyright (C) 2012  DBselller Servicos de Informatica
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

//MODULO: empenho
//CLASSE DA ENTIDADE pagordem
class cl_pagordem {
   // cria variaveis de erro
   var $rotulo     = null;
   var $query_sql  = null;
   var $numrows    = 0;
   var $numrows_incluir = 0;
   var $numrows_alterar = 0;
   var $numrows_excluir = 0;
   var $erro_status= null;
   var $erro_sql   = null;
   var $erro_banco = null;
   var $erro_msg   = null;
   var $erro_campo = null;
   var $pagina_retorno = null;
   // cria variaveis do arquivo
   var $e50_codord = 0;
   var $e50_numemp = 0;
   var $e50_data_dia = null;
   var $e50_data_mes = null;
   var $e50_data_ano = null;
   var $e50_data = null;
   var $e50_obs = null;
   var $e50_id_usuario = 0;
   var $e50_hora = null;
   var $e50_anousu = 0;
   var $e50_compdesp     = null;
   var $e50_compdesp_dia = null;
   var $e50_compdesp_mes = null;
   var $e50_compdesp_ano = null;
   var $e50_contapag = null;
   var $e50_cattrabalhador = null;
   var $e50_empresadesconto = null;
   var $e50_contribuicaoPrev = null;
   var $e50_cattrabalhadorremurenacao = null;
   var $e50_valorremuneracao = null;
   var $e50_valordesconto = null;
   var $e50_datacompetencia = null;
   var $e50_retencaoir = null;
   var $e50_naturezabemservico = null;
   var $e50_dtvencimento = null;
   var $e50_dtvencimento_dia = null;
   var $e50_dtvencimento_mes = null;
   var $e50_dtvencimento_ano = null;
   var $e50_numliquidacao = null;
   var $e50_contafornecedor = 0;
   var $e50_numcgmordenador = null;
   // cria propriedade com as variaveis do arquivo
   var $campos = "
                 e50_codord = int4 = Ordem
                 e50_numemp = int4 = Empenho
                 e50_data = date = Emissão
                 e50_obs = text = Observação
                 e50_id_usuario = int4 = Usuario
                 e50_hora = char(5) = Hora
                 e50_anousu = int4 = Ano da Ordem
                 e50_compdesp = date = Competencia da Despesa
                 e50_contapag = int4 = Conta Pagadora
                 e50_cattrabalhador = int4 = Categoria do Trabalhador
                 e50_empresadesconto = int4 = Empresa que Efetuou o Desconto
                 e50_contribuicaoPrev = char = Indicador de Desconto da Contribuição Previdenciária
                 e50_cattrabalhadorremurenacao = int4 = Categoria do trabalhador na qual houve a remuneração
                 e50_valorremuneracao = float8 = Valor da Remuneração
                 e50_valordesconto = float8 = Valor do desconto
                 e50_datacompetencia = date = Competência
                 e50_retencaoir = bool = Incide Retenção do Imposto de Renda
                 e50_naturezabemservico = int4 = Codigo de Natureza de Bem ou Serviço
                 e50_dtvencimento = date = Vencimento
                 e50_numliquidacao = int8 = Número da Liquidação
                 e50_contafornecedor = int8 = Conta do Fornecedor
                 e50_numcgmordenador = int8 = Numero do CGM do Ordenador
                 ";
   //funcao construtor da classe
   function cl_pagordem() {
     //classes dos rotulos dos campos
     $this->rotulo = new rotulo("pagordem");
     $this->pagina_retorno =  basename($_SERVER["PHP_SELF"]);
   }
   //funcao erro
   function erro($mostra,$retorna) {
     if(($this->erro_status == "0") || ($mostra == true && $this->erro_status != null )){
        echo "<script>alert(\"".$this->erro_msg."\");</script>";
        if($retorna==true){
           echo "<script>location.href='".$this->pagina_retorno."'</script>";
        }
     }
   }
   // funcao para atualizar campos
   function atualizacampos($exclusao=false) {
     if($exclusao==false){
       $this->e50_codord = ($this->e50_codord == ""?@$_POST["e50_codord"]:$this->e50_codord);
       $this->e50_numemp = ($this->e50_numemp == ""?@$_POST["e50_numemp"]:$this->e50_numemp);
       if($this->e50_data == ""){
         $this->e50_data_dia = ($this->e50_data_dia == ""?@$_POST["e50_data_dia"]:$this->e50_data_dia);
         $this->e50_data_mes = ($this->e50_data_mes == ""?@$_POST["e50_data_mes"]:$this->e50_data_mes);
         $this->e50_data_ano = ($this->e50_data_ano == ""?@$_POST["e50_data_ano"]:$this->e50_data_ano);
         if($this->e50_data_dia != ""){
            $this->e50_data = $this->e50_data_ano."-".$this->e50_data_mes."-".$this->e50_data_dia;
         }
       }
       if($this->e50_dtvencimento == ""){
        $this->e50_dtvencimento_dia = ($this->e50_dtvencimento_dia == ""?@$_POST["e50_dtvencimento_dia"]:$this->e50_dtvencimento_dia);
        $this->e50_dtvencimento_mes = ($this->e50_dtvencimento_mes == ""?@$_POST["e50_dtvencimento_mes"]:$this->e50_dtvencimento_mes);
        $this->e50_dtvencimento_ano = ($this->e50_dtvencimento_ano == ""?@$_POST["e50_dtvencimento_ano"]:$this->e50_dtvencimento_ano);
        if($this->e50_dtvencimento_dia != ""){
           $this->e50_data = $this->e50_dtvencimento_ano."-".$this->e50_dtvencimento_mes."-".$this->e50_dtvencimento_dia;
        }
      }
       $this->e50_obs = ($this->e50_obs == ""?@$_POST["e50_obs"]:$this->e50_obs);
       $this->e50_id_usuario = ($this->e50_id_usuario == ""?@$_POST["e50_id_usuario"]:$this->e50_id_usuario);
       $this->e50_hora = ($this->e50_hora == ""?@$_POST["e50_hora"]:$this->e50_hora);
       $this->e50_anousu = ($this->e50_anousu == ""?@$_POST["e50_anousu"]:$this->e50_anousu);
       $this->e50_contapag = ($this->e50_contapag == ""?@$_POST["e50_contapag"]:$this->e50_contapag);
       $this->e50_cattrabalhador = ($this->e50_cattrabalhador == ""?@$_POST["e50_cattrabalhador"]:$this->e50_cattrabalhador);
       $this->e50_empresadesconto = ($this->e50_empresadesconto == ""?@$_POST["e50_empresadesconto"]:$this->e50_empresadesconto);
       $this->e50_contribuicaoPrev = ($this->e50_contribuicaoPrev == ""?@$_POST["e50_contribuicaoPrev"]:$this->e50_contribuicaoPrev);
       $this->e50_cattrabalhadorremurenacao = ($this->e50_cattrabalhadorremurenacao == ""?@$_POST["e50_cattrabalhadorremurenacao"]:$this->e50_cattrabalhadorremurenacao);
       $this->e50_valorremuneracao = ($this->e50_valorremuneracao == ""?@$_POST["e50_valorremuneracao"]:$this->e50_valorremuneracao);
       $this->e50_valordesconto = ($this->e50_valordesconto == ""?@$_POST["e50_valordesconto"]:$this->e50_valordesconto);
       $this->e50_datacompetencia = ($this->e50_datacompetencia == ""?@$_POST["e50_datacompetencia"]:$this->e50_datacompetencia);
       $this->e50_retencaoir = ($this->e50_retencaoir == ""?@$_POST["e50_retencaoir"]:$this->e50_retencaoir) == 'sim' ? 1 : 0; 
       $this->e50_naturezabemservico = ($this->e50_naturezabemservico == ""?@$_POST["e50_naturezabemservico"]:$this->e50_naturezabemservico);
       $this->e50_numliquidacao = ($this->e50_numliquidacao == ""?@$_POST["e50_numliquidacao"]:$this->e50_numliquidacao);       
       $this->e50_numcgmordenador = ($this->e50_numcgmordenador == ""?@$_POST["e50_numcgmordenador"]:$this->e50_numcgmordenador);       
       if($this->e50_compdesp == ""){ 
        $this->e50_compdesp_dia = ($this->e50_compdesp_dia == ""?@$_POST["e50_compdesp_dia"]:$this->e50_compdesp_dia);
        $this->e50_compdesp_mes = ($this->e50_compdesp_mes == ""?@$_POST["e50_compdesp_mes"]:$this->e50_compdesp_mes);
        $this->e50_compdesp_ano = ($this->e50_compdesp_ano == ""?@$_POST["e50_compdesp_ano"]:$this->e50_compdesp_ano);
        if($this->e50_compdesp_dia != ""){
           $this->e50_compdesp = $this->e50_compdesp_ano."-".$this->e50_compdesp_mes."-".$this->e50_compdesp_dia;
        }
        $this->e50_contafornecedor = ($this->e50_contafornecedor == ""?@$_POST["e50_contafornecedor"]:$this->e50_contafornecedor);
      }
     }else{
       $this->e50_codord = ($this->e50_codord == ""?@$_POST["e50_codord"]:$this->e50_codord);
     }
   }
   // funcao para inclusao
   function incluir ($e50_codord){
      $this->atualizacampos();
     if($this->e50_numemp == null ){
       $this->erro_sql = " Campo Empenho nao Informado.";
       $this->erro_campo = "e50_numemp";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->e50_data == null ){
       $this->erro_sql = " Campo Emissão nao Informado.";
       $this->erro_campo = "e50_data_dia";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->e50_id_usuario == null ){
       $this->erro_sql = " Campo Usuario nao Informado.";
       $this->erro_campo = "e50_id_usuario";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->e50_hora == null ){
       $this->erro_sql = " Campo Hora nao Informado.";
       $this->erro_campo = "e50_hora";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->e50_anousu == null ){
       $this->erro_sql = " Campo Ano da Ordem nao Informado.";
       $this->erro_campo = "e50_anousu";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($e50_codord == "" || $e50_codord == null ){
       $result = db_query("select nextval('pagordem_e50_codord_seq')");
       if($result==false){
         $this->erro_banco = str_replace("\n","",@pg_last_error());
         $this->erro_sql   = "Verifique o cadastro da sequencia: pagordem_e50_codord_seq do campo: e50_codord";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
       $this->e50_codord = pg_result($result,0,0);
     }else{
       $result = db_query("select last_value from pagordem_e50_codord_seq");
       if(($result != false) && (pg_result($result,0,0) < $e50_codord)){
         $this->erro_sql = " Campo e50_codord maior que último número da sequencia.";
         $this->erro_banco = "Sequencia menor que este número.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }else{
         $this->e50_codord = $e50_codord;
       }
     }
     if(($this->e50_codord == null) || ($this->e50_codord == "") ){
       $this->erro_sql = " Campo e50_codord nao declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->e50_numliquidacao == null ){
      $this->erro_sql = " Campo Numero da Liquidação nao Informado.";
      $this->erro_campo = "e50_numliquidacao";
      $this->erro_banco = "";
      $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
      $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
      $this->erro_status = "0";
      return false;
    }
    if($this->e50_numcgmordenador == null ){
      $this->erro_sql = " Campo Ordenador da Liquidação.";
      $this->erro_campo = "e50_numcgmordenador";
      $this->erro_banco = "";
      $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
      $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
      $this->erro_status = "0";
      return false;
    }

    if($this->e50_contafornecedor == null ||  $this->e50_contafornecedor == ""){
      $this->e50_contafornecedor = 0;
    }

     $sql = "insert into pagordem(
                                       e50_codord
                                      ,e50_numemp
                                      ,e50_data
                                      ,e50_obs
                                      ,e50_id_usuario
                                      ,e50_hora
                                      ,e50_anousu
                                      ,e50_compdesp
                                      ,e50_contapag
                                      ,e50_cattrabalhador
                                      ,e50_empresadesconto
                                      ,e50_contribuicaoPrev
                                      ,e50_cattrabalhadorremurenacao
                                      ,e50_valorremuneracao
                                      ,e50_valordesconto
                                      ,e50_datacompetencia
                                      ,e50_retencaoir
                                      ,e50_naturezabemservico
                                      ,e50_dtvencimento
                                      ,e50_numliquidacao
                                      ,e50_contafornecedor
                                      ,e50_numcgmordenador
                       )
                values (
                                $this->e50_codord
                               ,$this->e50_numemp
                               ,".($this->e50_data == "null" || $this->e50_data == ""?"null":"'".$this->e50_data."'")."
                               ,'$this->e50_obs'
                               ,$this->e50_id_usuario
                               ,'$this->e50_hora'
                               ,$this->e50_anousu
                               ,".($this->e50_compdesp == "null" || $this->e50_compdesp == ""?"null":"'".$this->e50_compdesp."'")."
                               ,".($this->e50_contapag == "null" || $this->e50_contapag == ""?"null":$this->e50_contapag)."
                               ,$this->e50_cattrabalhador
                               ,$this->e50_empresadesconto
                               ,$this->e50_contribuicaoPrev
                               ,$this->e50_cattrabalhadorremurenacao
                               ,$this->e50_valorremuneracao
                               ,$this->e50_valordesconto
                               ,".($this->e50_datacompetencia == "null" || $this->e50_datacompetencia == ""?"null":"'".$this->e50_datacompetencia."'")."                               
                               ,$this->e50_retencaoir::bool
                               ,".($this->e50_naturezabemservico == "null" || $this->e50_naturezabemservico == ""?"null":"'".$this->e50_naturezabemservico."'")."
                               ,".($this->e50_dtvencimento == "null" || $this->e50_dtvencimento == ""?"null":"'".$this->e50_dtvencimento."'")."
                               ,$this->e50_numliquidacao
                               ,$this->e50_contafornecedor
                               ,$this->e50_numcgmordenador
                      )";
                
     $result = db_query($sql);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
         $this->erro_sql   = "Ordens de pagamento ($this->e50_codord) nao Incluído. Inclusao Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_banco = "Ordens de pagamento já Cadastrado";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }else{
         $this->erro_sql   = "Ordens de pagamento ($this->e50_codord) nao Incluído. Inclusao Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }
       $this->erro_status = "0";
       $this->numrows_incluir= 0;
       return false;
     }
     $this->erro_banco = "";
     $this->erro_sql = "Inclusao efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$this->e50_codord;
     $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
     $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
     $this->erro_status = "1";
     $this->numrows_incluir= pg_affected_rows($result);
     $resaco = $this->sql_record($this->sql_query_file($this->e50_codord));
     if(($resaco!=false)||($this->numrows!=0)){
       $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
       $acount = pg_result($resac,0,0);
       $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
       $resac = db_query("insert into db_acountkey values($acount,5437,'$this->e50_codord','I')");
       $resac = db_query("insert into db_acount values($acount,808,5437,'','".AddSlashes(pg_result($resaco,0,'e50_codord'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = db_query("insert into db_acount values($acount,808,5438,'','".AddSlashes(pg_result($resaco,0,'e50_numemp'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = db_query("insert into db_acount values($acount,808,5439,'','".AddSlashes(pg_result($resaco,0,'e50_data'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = db_query("insert into db_acount values($acount,808,5441,'','".AddSlashes(pg_result($resaco,0,'e50_obs'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = db_query("insert into db_acount values($acount,808,9190,'','".AddSlashes(pg_result($resaco,0,'e50_id_usuario'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = db_query("insert into db_acount values($acount,808,9191,'','".AddSlashes(pg_result($resaco,0,'e50_hora'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = db_query("insert into db_acount values($acount,808,11134,'','".AddSlashes(pg_result($resaco,0,'e50_anousu'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
     }
     return true;
   }
   // funcao para alteracao
   function alterar ($e50_codord=null,$o56_elemento=null) {
      $this->atualizacampos();
     $sql = " update pagordem set ";
     $virgula = "";
     if(trim($this->e50_codord)!="" || isset($_POST["e50_codord"])){
       $sql  .= $virgula." e50_codord = $this->e50_codord ";
       $virgula = ",";
       if(trim($this->e50_codord) == null ){
         $this->erro_sql = " Campo Ordem nao Informado.";
         $this->erro_campo = "e50_codord";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->e50_numemp)!="" || isset($_POST["e50_numemp"])){
       $sql  .= $virgula." e50_numemp = $this->e50_numemp ";
       $virgula = ",";
       if(trim($this->e50_numemp) == null ){
         $this->erro_sql = " Campo Empenho nao Informado.";
         $this->erro_campo = "e50_numemp";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->e50_data)!="" || isset($_POST["e50_data_dia"]) &&  ($_POST["e50_data_dia"] !="") ){
       $sql  .= $virgula." e50_data = '$this->e50_data' ";
       $virgula = ",";
       if(trim($this->e50_data) == null ){
         $this->erro_sql = " Campo Emissão nao Informado.";
         $this->erro_campo = "e50_data_dia";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }else{
       if(isset($_POST["e50_data_dia"])){
         $sql  .= $virgula." e50_data = null ";
         $virgula = ",";
         if(trim($this->e50_data) == null ){
           $this->erro_sql = " Campo Emissão nao Informado.";
           $this->erro_campo = "e50_data_dia";
           $this->erro_banco = "";
           $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
           $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
           $this->erro_status = "0";
           return false;
         }
       }
     }
     if(trim($this->e50_obs)!="" || isset($_POST["e50_obs"])){
       $sql  .= $virgula." e50_obs = '$this->e50_obs' ";
       $virgula = ",";
     }
     if(trim($this->e50_id_usuario)!="" || isset($_POST["e50_id_usuario"])){
       $sql  .= $virgula." e50_id_usuario = $this->e50_id_usuario ";
       $virgula = ",";
       if(trim($this->e50_id_usuario) == null ){
         $this->erro_sql = " Campo Usuario nao Informado.";
         $this->erro_campo = "e50_id_usuario";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->e50_hora)!="" || isset($_POST["e50_hora"])){
       $sql  .= $virgula." e50_hora = '$this->e50_hora' ";
       $virgula = ",";
       if(trim($this->e50_hora) == null ){
         $this->erro_sql = " Campo Hora nao Informado.";
         $this->erro_campo = "e50_hora";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->e50_anousu)!="" || isset($_POST["e50_anousu"])){
       $sql  .= $virgula." e50_anousu = $this->e50_anousu ";
       $virgula = ",";
       if(trim($this->e50_anousu) == null ){
         $this->erro_sql = " Campo Ano da Ordem nao Informado.";
         $this->erro_campo = "e50_anousu";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->e50_compdesp)!="" || isset($_POST["e50_compdesp_dia"]) &&  ($_POST["e50_compdesp_dia"] !="") ){
       $sql  .= $virgula." e50_compdesp = '$this->e50_compdesp' ";
       $virgula = ",";
       if(trim($this->e50_compdesp) == null ){
         $this->erro_sql = " Campo Competência da Despesa nao Informado.";
         $this->erro_campo = "e50_compdesp_dia";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }else{
       if(isset($_POST["e50_compdesp_dia"])){
         $sql  .= $virgula." e50_compdesp = null ";
         $virgula = ",";
         if(trim($this->e50_compdesp) == null && in_array(substr($o56_elemento,0,7),array("3319092","3319192","3319592","3319692"))){
           $this->erro_sql = " Campo Competência da Despesa nao Informado.";
           $this->erro_campo = "e50_compdesp_dia";
           $this->erro_banco = "";
           $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
           $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
           $this->erro_status = "0";
           return false;
         }
       }
     }
     if(trim($this->e50_contapag)!="" || isset($_POST["e50_contapag"])){
        $sql  .= $virgula." e50_contapag = $this->e50_contapag ";
        $virgula = ",";
      }
      if(trim($this->e50_retencaoir)!="" || isset($_POST["e50_retencaoir"])){
        $sql  .= $virgula." e50_retencaoir = $this->e50_retencaoir::bool ";
        $virgula = ",";
      }
      if(trim($this->e50_naturezabemservico)!="" || isset($_POST["e50_naturezabemservico"])){
        $sql  .= $virgula." e50_naturezabemservico = $this->e50_naturezabemservico ";
        $virgula = ",";
      }
      if(trim($this->e50_cattrabalhador)!="" || isset($_POST["e50_cattrabalhador"])){
        $sql  .= $virgula." e50_cattrabalhador = $this->e50_cattrabalhador ";
        $virgula = ",";
      } else {
        $sql  .= $virgula." e50_cattrabalhador = null ";
        $virgula = ",";
      }
      if(trim($this->e50_cattrabalhadorremurenacao)!="" || isset($_POST["e50_cattrabalhadorremurenacao"])){
        $sql  .= $virgula." e50_cattrabalhadorremurenacao = $this->e50_cattrabalhadorremurenacao ";
        $virgula = ",";
      } else {
        $sql  .= $virgula." e50_cattrabalhadorremurenacao = null ";
        $virgula = ",";
      }
      if(trim($this->e50_empresadesconto)!="" || isset($_POST["e50_empresadesconto"])){
        $sql  .= $virgula." e50_empresadesconto = $this->e50_empresadesconto ";
        $virgula = ",";
      } else {
        $sql  .= $virgula." e50_empresadesconto = null ";
        $virgula = ",";
      }
      if(trim($this->e50_contribuicaoPrev)!="" || isset($_POST["e50_contribuicaoPrev"])){
        $sql  .= $virgula." e50_contribuicaoPrev = $this->e50_contribuicaoPrev ";
        $virgula = ",";
      } else {
        $sql  .= $virgula." e50_contribuicaoPrev = null ";
        $virgula = ",";
      }
      if(trim($this->e50_valorremuneracao)!="" || isset($_POST["e50_valorremuneracao"])){
        $sql  .= $virgula." e50_valorremuneracao = $this->e50_valorremuneracao ";
        $virgula = ",";
      } else {
        $sql  .= $virgula." e50_valorremuneracao = null ";
        $virgula = ",";
      }
      if(trim($this->e50_valordesconto)!="" || isset($_POST["e50_valordesconto"])){
        $sql  .= $virgula." e50_valordesconto = $this->e50_valordesconto ";
        $virgula = ",";
      }else {
        $sql  .= $virgula." e50_valordesconto = null ";
        $virgula = ",";
      }
      if(trim($this->e50_datacompetencia)!="" || isset($_POST["e50_datacompetencia"])){
        $sql  .= $virgula." e50_datacompetencia = '$this->e50_datacompetencia' ";
        $virgula = ",";
      } else {
        $sql  .= $virgula." e50_datacompetencia = null ";
        $virgula = ",";
      }
      if(trim($this->e50_contafornecedor)!="" || isset($_POST["e50_contafornecedor"])){
        $sql  .= $virgula." e50_contafornecedor = $this->e50_contafornecedor ";
        $virgula = ",";
      }
      if(trim($this->e50_numcgmordenador)!="" || isset($_POST["e50_numcgmordenador"]) &&  ($_POST["e50_numcgmordenador"] != "")){
        $sql  .= $virgula." e50_numcgmordenador = '$this->e50_numcgmordenador' ";
        $virgula = ",";
      } else {
        $sql  .= $virgula." e50_numcgmordenador = null ";
        $virgula = ",";
      }
     $sql .= " where ";
     if($e50_codord!=null){
       $e50_codord  = $this->e50_codord == '' ? $e50_codord : $this->e50_codord;
       $sql .= " e50_codord = $e50_codord";
     }
     $resaco = $this->sql_record($this->sql_query_file($e50_codord));
     if($this->numrows>0){
       for($conresaco=0;$conresaco<$this->numrows;$conresaco++){
         $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
         $acount = pg_result($resac,0,0);
         $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
         $resac = db_query("insert into db_acountkey values($acount,5437,'$this->e50_codord','A')");
         if(isset($_POST["e50_codord"]) || $this->e50_codord != "")
           $resac = db_query("insert into db_acount values($acount,808,5437,'".AddSlashes(pg_result($resaco,$conresaco,'e50_codord'))."','$this->e50_codord',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($_POST["e50_numemp"]) || $this->e50_numemp != "")
           $resac = db_query("insert into db_acount values($acount,808,5438,'".AddSlashes(pg_result($resaco,$conresaco,'e50_numemp'))."','$this->e50_numemp',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($_POST["e50_data"]) || $this->e50_data != "")
           $resac = db_query("insert into db_acount values($acount,808,5439,'".AddSlashes(pg_result($resaco,$conresaco,'e50_data'))."','$this->e50_data',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($_POST["e50_obs"]) || $this->e50_obs != "")
           $resac = db_query("insert into db_acount values($acount,808,5441,'".AddSlashes(pg_result($resaco,$conresaco,'e50_obs'))."','$this->e50_obs',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($_POST["e50_id_usuario"]) || $this->e50_id_usuario != "")
           $resac = db_query("insert into db_acount values($acount,808,9190,'".AddSlashes(pg_result($resaco,$conresaco,'e50_id_usuario'))."','$this->e50_id_usuario',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($_POST["e50_hora"]) || $this->e50_hora != "")
           $resac = db_query("insert into db_acount values($acount,808,9191,'".AddSlashes(pg_result($resaco,$conresaco,'e50_hora'))."','$this->e50_hora',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($_POST["e50_anousu"]) || $this->e50_anousu != "")
           $resac = db_query("insert into db_acount values($acount,808,11134,'".AddSlashes(pg_result($resaco,$conresaco,'e50_anousu'))."','$this->e50_anousu',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       }
     } 
     $result = db_query($sql);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = $sql."Ordens de pagamento nao Alterado. Alteracao Abortada.\\n";
         $this->erro_sql .= "Valores : ".$this->e50_codord;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_alterar = 0;
       return false;
     }else{
       if(pg_affected_rows($result)==0){
         $this->erro_banco = "";
         $this->erro_sql = "Ordens de pagamento nao foi Alterado. Alteracao Executada.\\n";
         $this->erro_sql .= "Valores : ".$this->e50_codord;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = 0;
         return true;
       }else{
         $this->erro_banco = "";
         $this->erro_sql = $sql."Alteração efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$this->e50_codord;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = pg_affected_rows($result);
         return true;
       }
     }
   }
   // funcao para exclusao
   function excluir ($e50_codord=null,$dbwhere=null) {
     if($dbwhere==null || $dbwhere==""){
       $resaco = $this->sql_record($this->sql_query_file($e50_codord));
     }else{
       $resaco = $this->sql_record($this->sql_query_file(null,"*",null,$dbwhere));
     }
     if(($resaco!=false)||($this->numrows!=0)){
       for($iresaco=0;$iresaco<$this->numrows;$iresaco++){
         $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
         $acount = pg_result($resac,0,0);
         $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
         $resac = db_query("insert into db_acountkey values($acount,5437,'$e50_codord','E')");
         $resac = db_query("insert into db_acount values($acount,808,5437,'','".AddSlashes(pg_result($resaco,$iresaco,'e50_codord'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,808,5438,'','".AddSlashes(pg_result($resaco,$iresaco,'e50_numemp'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,808,5439,'','".AddSlashes(pg_result($resaco,$iresaco,'e50_data'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,808,5441,'','".AddSlashes(pg_result($resaco,$iresaco,'e50_obs'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,808,9190,'','".AddSlashes(pg_result($resaco,$iresaco,'e50_id_usuario'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,808,9191,'','".AddSlashes(pg_result($resaco,$iresaco,'e50_hora'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,808,11134,'','".AddSlashes(pg_result($resaco,$iresaco,'e50_anousu'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       }
     }
     $sql = " delete from pagordem
                    where ";
     $sql2 = "";
     if($dbwhere==null || $dbwhere ==""){
        if($e50_codord != ""){
          if($sql2!=""){
            $sql2 .= " and ";
          }
          $sql2 .= " e50_codord = $e50_codord ";
        }
     }else{
       $sql2 = $dbwhere;
     }
     $result = db_query($sql.$sql2);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Ordens de pagamento nao Excluído. Exclusão Abortada.\\n";
       $this->erro_sql .= "Valores : ".$e50_codord;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_excluir = 0;
       return false;
     }else{
       if(pg_affected_rows($result)==0){
         $this->erro_banco = "";
         $this->erro_sql = "Ordens de pagamento nao Encontrado. Exclusão não Efetuada.\\n";
         $this->erro_sql .= "Valores : ".$e50_codord;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_excluir = 0;
         return true;
       }else{
         $this->erro_banco = "";
         $this->erro_sql = "Exclusão efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$e50_codord;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_excluir = pg_affected_rows($result);
         return true;
       }
     }
   }
   // funcao do recordset
   function sql_record($sql) {
     $result = db_query($sql);
     if($result==false){
       $this->numrows    = 0;
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Erro ao selecionar os registros.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     $this->numrows = pg_numrows($result);
      if($this->numrows==0){
        $this->erro_banco = "";
        $this->erro_sql   = "Record Vazio na Tabela:pagordem";
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "0";
        return false;
      }
     return $result;
   }
   // funcao do sql
   function sql_query ( $e50_codord=null,$campos="*",$ordem=null,$dbwhere=""){
     $sql = "select ";
     if($campos != "*" ){
       $campos_sql = split("#",$campos);
       $virgula = "";
       for($i=0;$i<sizeof($campos_sql);$i++){
         $sql .= $virgula.$campos_sql[$i];
         $virgula = ",";
       }
     }else{
       $sql .= $campos;
     }
     $sql .= " from pagordem ";
     $sql .= "      inner join db_usuarios  on  db_usuarios.id_usuario = pagordem.e50_id_usuario";
     $sql .= "      inner join empempenho  on  empempenho.e60_numemp = pagordem.e50_numemp";
     $sql .= "      inner join cgm  on  cgm.z01_numcgm = empempenho.e60_numcgm";
     $sql .= "      inner join db_config  on  db_config.codigo = empempenho.e60_instit";
     $sql .= "      inner join orcdotacao  on  orcdotacao.o58_anousu = empempenho.e60_anousu and  orcdotacao.o58_coddot = empempenho.e60_coddot";
     $sql .= "      inner join pctipocompra  on  pctipocompra.pc50_codcom = empempenho.e60_codcom";
     $sql .= "      inner join emptipo  on  emptipo.e41_codtipo = empempenho.e60_codtipo";
     $sql .= "      left  join concarpeculiar  on  concarpeculiar.c58_sequencial = empempenho.e60_concarpeculiar";
     $sql2 = "";
     if($dbwhere==""){
       if($e50_codord!=null ){
         $sql2 .= " where pagordem.e50_codord = $e50_codord ";
       }
     }else if($dbwhere != ""){
       $sql2 = " where $dbwhere";
     }
     $sql .= $sql2;
     if($ordem != null ){
       $sql .= " order by ";
       $campos_sql = split("#",$ordem);
       $virgula = "";
       for($i=0;$i<sizeof($campos_sql);$i++){
         $sql .= $virgula.$campos_sql[$i];
         $virgula = ",";
       }
     }
     return $sql;
  }
   // funcao do sql
   function sql_query_file ( $e50_codord=null,$campos="*",$ordem=null,$dbwhere=""){
     $sql = "select ";
     if($campos != "*" ){
       $campos_sql = split("#",$campos);
       $virgula = "";
       for($i=0;$i<sizeof($campos_sql);$i++){
         $sql .= $virgula.$campos_sql[$i];
         $virgula = ",";
       }
     }else{
       $sql .= $campos;
     }
     $sql .= " from pagordem ";
     $sql2 = "";
     if($dbwhere==""){
       if($e50_codord!=null ){
         $sql2 .= " where pagordem.e50_codord = $e50_codord ";
       }
     }else if($dbwhere != ""){
       $sql2 = " where $dbwhere";
     }
     $sql .= $sql2;
     if($ordem != null ){
       $sql .= " order by ";
       $campos_sql = split("#",$ordem);
       $virgula = "";
       for($i=0;$i<sizeof($campos_sql);$i++){
         $sql .= $virgula.$campos_sql[$i];
         $virgula = ",";
       }
     }
     return $sql;
  }
  function sql_query_boxesocial ( $e50_codord=null,$campos="*",$ordem=null,$dbwhere=""){
    $sql = "select ";
    if($campos != "*" ){
      $campos_sql = split("#",$campos);
      $virgula = "";
      for($i=0;$i<sizeof($campos_sql);$i++){
        $sql .= $virgula.$campos_sql[$i];
        $virgula = ",";
      }
    }else{
      $sql .= $campos;
    }
    $sql .= " from pagordem ";
    $sql .= " left join categoriatrabalhador on e50_cattrabalhador = ct01_codcategoria ";
    $sql .= " left join cgm on z01_numcgm = e50_empresadesconto ";
    $sql2 = "";
    if($dbwhere==""){
      if($e50_codord!=null ){
        $sql2 .= " where pagordem.e50_codord = $e50_codord ";
      }
    }else if($dbwhere != ""){
      $sql2 = " where $dbwhere";
    }
    $sql .= $sql2;
    if($ordem != null ){
      $sql .= " order by ";
      $campos_sql = split("#",$ordem);
      $virgula = "";
      for($i=0;$i<sizeof($campos_sql);$i++){
        $sql .= $virgula.$campos_sql[$i];
        $virgula = ",";
      }
    }  
    return $sql;
 }
   function sql_query_cheques( $e50_codord=null,$campos="*",$ordem=null,$dbwhere="", $sJoin = ""){
     $sql = "select ";
     if($campos != "*" ){
       $campos_sql = split("#",$campos);
       $virgula = "";
       for($i=0;$i<sizeof($campos_sql);$i++){
         $sql .= $virgula.$campos_sql[$i];
         $virgula = ",";
       }
     }else{
       $sql .= $campos;
     }
     $sql .= " from pagordem ";
     $sql .= "      inner join pagordemele   on  pagordemele.e53_codord = pagordem.e50_codord";
     $sql .= "      inner join empempenho    on  empempenho.e60_numemp  = pagordem.e50_numemp";
     $sql .= "      inner join cgm           on  cgm.z01_numcgm   = empempenho.e60_numcgm";
     $sql .= "      inner join db_config     on  db_config.codigo = empempenho.e60_instit";
     $sql .= "      inner join orcdotacao    on  orcdotacao.o58_anousu = empempenho.e60_anousu and  orcdotacao.o58_coddot = empempenho.e60_coddot";
     $sql .= "      inner join orctiporec    on  orctiporec.o15_codigo =  orcdotacao.o58_codigo    ";
     $sql .= "      left join emptipo       on  emptipo.e41_codtipo = empempenho.e60_codtipo";
     $sql .= "      left join empord        on  empord.e82_codord   = pagordem.e50_codord";
     $sql .= "      left join empagemov     on  empagemov.e81_codmov   = empord.e82_codmov";
     $sql .= "      left join empagemovforma on  e81_codmov   = e97_codmov";
     $sql .= "      left join empage				 on  empage.e80_codage = empagemov.e81_codage";
     $sql .= "      left join corempagemov   on corempagemov.k12_codmov = empagemov.e81_codmov";
     $sql .= "      left join empageconf     on  empageconf.e86_codmov  = empord.e82_codmov";
     $sql .= "      left join empageconfche  on  empageconf.e86_codmov  = e91_codmov and e91_ativo is true ";
     $sql .= "      left join pagordemconta  on e49_codord   = e82_codord ";
     $sql .= "      left join cgm a          on a.z01_numcgm = e49_numcgm ";
     $sql .= "      left join empageconfgera on empageconfgera.e90_codmov = empagemov.e81_codmov ";
     if ($sJoin != "") {
      $sql .= $sJoin;
     }
     $sql2 = "";
     if($dbwhere==""){
       if($e50_codord!=null ){
         $sql2 .= " where pagordem.e50_codord = $e50_codord ";
       }
     }else if($dbwhere != ""){
       $sql2 = " where $dbwhere";
     }
     $sql .= $sql2;
     if($ordem != null ){
       $sql .= " order by ";
       $campos_sql = split("#",$ordem);
       $virgula = "";
       for($i=0;$i<sizeof($campos_sql);$i++){
      $sql .= $virgula.$campos_sql[$i];
         $virgula = ",";
       }
     }
     return $sql;
  }
   function sql_query_emp ( $e50_codord=null,$campos="*",$ordem=null,$dbwhere=""){
    $sql = "select ";
    if($campos != "*" ){
      $campos_sql = split("#",$campos);
      $virgula = "";
      for($i=0;$i<sizeof($campos_sql);$i++){
        $sql .= $virgula.$campos_sql[$i];
        $virgula = ",";
      }
    }else{
      $sql .= $campos;
    }
    $sql .= " from pagordem ";
    $sql .= "      inner join empempenho on  empempenho.e60_numemp = pagordem.e50_numemp";
    $sql .= "      inner join cgm        on  cgm.z01_numcgm = empempenho.e60_numcgm";
		$sql .= "      left join pagordemele on  pagordemele.e53_codord   = pagordem.e50_codord ";
    $sql2 = "";
    if($dbwhere==""){
      if($e50_codord!=null ){
        $sql2 .= " where pagordem.e50_codord = $e50_codord ";
      }
    }else if($dbwhere != ""){
      $sql2 = " where $dbwhere";
    }
    $sql .= $sql2;
    if($ordem != null ){
      $sql .= " order by ";
      $campos_sql = split("#",$ordem);
      $virgula = "";
      for($i=0;$i<sizeof($campos_sql);$i++){
        $sql .= $virgula.$campos_sql[$i];
        $virgula = ",";
      }
    }
    return $sql;
  }
   function sql_query_empagemovforma( $e50_codord=null,$campos="*",$ordem=null,$dbwhere="", $sJoin = ""){
     $sql = "select ";
     if($campos != "*" ){
       $campos_sql = split("#",$campos);
       $virgula = "";
       for($i=0;$i<sizeof($campos_sql);$i++){
         $sql .= $virgula.$campos_sql[$i];
         $virgula = ",";
       }
     }else{
       $sql .= $campos;
     }

     /*$sql .= " from pagordem ";
     $sql .= "      inner join pagordemele   on  pagordemele.e53_codord = pagordem.e50_codord";
     $sql .= "      inner join empempenho    on  empempenho.e60_numemp  = pagordem.e50_numemp";
     $sql .= "      inner join cgm           on  cgm.z01_numcgm   = empempenho.e60_numcgm";
     $sql .= "      inner join db_config     on  db_config.codigo = empempenho.e60_instit";
     $sql .= "      inner join orcdotacao    on  orcdotacao.o58_anousu = empempenho.e60_anousu and  orcdotacao.o58_coddot = empempenho.e60_coddot";
     $sql .= "      inner join orctiporec    on  orctiporec.o15_codigo =  orcdotacao.o58_codigo    ";
     $sql .= "      inner join emptipo       on  emptipo.e41_codtipo = empempenho.e60_codtipo";
     $sql .= "      inner join empord        on  empord.e82_codord   = pagordem.e50_codord";
     $sql .= "      inner join empagemov     on  empagemov.e81_codmov   = empord.e82_codmov";
     $sql .= "      inner join empage				 on  empage.e80_codage = empagemov.e81_codage";*/

     $sql .= " from empage ";
     $sql .= "      inner join empagemov       on empagemov.e81_codage      = empage.e80_codage      ";
     $sql .= "      inner join empord          on empord.e82_codmov         = empagemov.e81_codmov   ";
     $sql .= "      inner join pagordem        on pagordem.e50_codord       = empord.e82_codord      ";
     $sql .= "      inner join pagordemele     on pagordemele.e53_codord    = pagordem.e50_codord    ";
     $sql .= "      inner join empempenho      on empempenho.e60_numemp     = pagordem.e50_numemp    ";
     $sql .= "      inner join cgm             on cgm.z01_numcgm            = empempenho.e60_numcgm  ";
     $sql .= "      inner join db_config       on db_config.codigo          = empempenho.e60_instit  ";
     $sql .= "      inner join orcdotacao      on orcdotacao.o58_anousu     = empempenho.e60_anousu  ";
     $sql .= "                                and orcdotacao.o58_coddot     = empempenho.e60_coddot  ";
     $sql .= "      inner join orctiporec      on orctiporec.o15_codigo     = orcdotacao.o58_codigo  ";
     $sql .= "      inner join emptipo         on emptipo.e41_codtipo       = empempenho.e60_codtipo ";
     $sql .= "      left join empageconcarpeculiar on empageconcarpeculiar.e79_empagemov = empagemov.e81_codmov ";
     $sql .= "      left join corempagemov     on corempagemov.k12_codmov   = empagemov.e81_codmov";
     $sql .= "      left join empagemovconta   on  empagemov.e81_codmov     = e98_codmov";
     $sql .= "      left join empageconf       on  empageconf.e86_codmov    = empord.e82_codmov";
     $sql .= "      left join empageconfche    on  empageconf.e86_codmov    = e91_codmov and e91_ativo is true ";
     $sql .= "      left join pagordemconta    on e49_codord                = e82_codord ";
     $sql .= "      left join empagemovforma   on e97_codmov                = e81_codmov ";
     $sql .= "      left join empagepag        on e85_codmov                = e81_codmov ";
     $sql .= "      left join empagetipo       on e85_codtipo               = e83_codtipo ";
     $sql .= "      left join  pagordemnota    on e71_codord                = e50_codord ";
     $sql .= "      left join cgm a            on a.z01_numcgm              = e49_numcgm ";
     $sql .= "      left join empageconfgera   on empageconfgera.e90_codmov = empagemov.e81_codmov ";
     $sql .= "                                and  empageconfgera.e90_cancelado is false ";
     $sql .= "      left join corgrupocorrente on k105_data                 = corempagemov.k12_data ";
     $sql .= "                                and k105_autent               = corempagemov.k12_autent ";
     $sql .= "                                and k105_id                   = corempagemov.k12_id ";
     if ($sJoin != "") {
      $sql .= $sJoin;
     }
     $sql2 = "";
     if($dbwhere==""){
       if($e50_codord!=null ){
         $sql2 .= " where pagordem.e50_codord = $e50_codord ";
       }
     }else if($dbwhere != ""){
       $sql2 = " where $dbwhere";
     }
     $sql .= $sql2;
     if($ordem != null ){
       $sql .= " order by ";
       $campos_sql = split("#",$ordem);
       $virgula = "";
       for($i=0;$i<sizeof($campos_sql);$i++){
      $sql .= $virgula.$campos_sql[$i];
         $virgula = ",";
       }
     }
     return $sql;
  }
   function sql_query_impconsulta ( $e50_codord=null,$campos="*",$ordem=null,$dbwhere=""){
    $sql = "select ";
    if($campos != "*" ){
      $campos_sql = split("#",$campos);
      $virgula = "";
      for($i=0;$i<sizeof($campos_sql);$i++){
        $sql .= $virgula.$campos_sql[$i];
        $virgula = ",";
      }
    }else{
      $sql .= $campos;
    }
    $sql .= " from pagordem ";
    $sql .= "      inner join empempenho  on  empempenho.e60_numemp = pagordem.e50_numemp";
    $sql .= "      inner join empempitem  on  empempitem.e62_numemp = empempenho.e60_numemp";
    $sql .= "      inner join pcmater     on  pcmater.pc01_codmater = empempitem.e62_item";
    $sql .= "      inner join cgm  on  cgm.z01_numcgm = empempenho.e60_numcgm";
    $sql .= "      inner join orcdotacao  on  orcdotacao.o58_anousu = empempenho.e60_anousu and  orcdotacao.o58_coddot = empempenho.e60_coddot";
    $sql .= "      inner join pctipocompra  on  pctipocompra.pc50_codcom = empempenho.e60_codcom";
    $sql2 = "";
    if($dbwhere==""){
      if($e50_codord!=null ){
        $sql2 .= " where pagordem.e50_codord = $e50_codord ";
      }
    }else if($dbwhere != ""){
      $sql2 = " where $dbwhere";
    }
    $sql .= $sql2;
    if($ordem != null ){
      $sql .= " order by ";
      $campos_sql = split("#",$ordem);
      $virgula = "";
      for($i=0;$i<sizeof($campos_sql);$i++){
        $sql .= $virgula.$campos_sql[$i];
        $virgula = ",";
      }
    }
    return $sql;
  }
   function sql_query_notaliquidacao ( $e50_codord=null,$campos="*",$ordem=null,$dbwhere=""){
    $sql = "select ";
    if($campos != "*" ){
      $campos_sql = split("#",$campos);
      $virgula = "";
      for($i=0;$i<sizeof($campos_sql);$i++){
        $sql .= $virgula.$campos_sql[$i];
        $virgula = ",";
      }
    }else{
      $sql .= $campos;
    }
    $sql .= " from pagordem ";
    $sql .= "      left  join db_usuarios  on  db_usuarios.id_usuario = pagordem.e50_id_usuario";
    $sql .= "      inner join empempenho  on  empempenho.e60_numemp = pagordem.e50_numemp";
    $sql .= "      inner join cgm  on  cgm.z01_numcgm = empempenho.e60_numcgm";
    $sql .= "      inner join db_config  on  db_config.codigo = empempenho.e60_instit";
    $sql .= "      inner join orcdotacao  on  orcdotacao.o58_anousu = empempenho.e60_anousu and  orcdotacao.o58_coddot = empempenho.e60_coddot";
    $sql .= "      inner join pctipocompra  on  pctipocompra.pc50_codcom = empempenho.e60_codcom";
    $sql .= "      inner join emptipo  on  emptipo.e41_codtipo = empempenho.e60_codtipo";
    $sql .= "      inner join concarpeculiar  on  concarpeculiar.c58_sequencial = empempenho.e60_concarpeculiar";
    $sql .= "      inner join pagordemnota  on  e71_codord = e50_codord";
    $sql .= "      inner join empnota       on  e71_codnota = e69_codnota";
    $sql2 = "";
    if($dbwhere==""){
      if($e50_codord!=null ){
        $sql2 .= " where pagordem.e50_codord = $e50_codord ";
      }
    }else if($dbwhere != ""){
      $sql2 = " where $dbwhere";
    }
    $sql .= $sql2;
    if($ordem != null ){
      $sql .= " order by ";
      $campos_sql = split("#",$ordem);
      $virgula = "";
      for($i=0;$i<sizeof($campos_sql);$i++){
        $sql .= $virgula.$campos_sql[$i];
        $virgula = ",";
      }
    }
    return $sql;
  }
   function sql_query_pag ( $e50_codord=null,$campos="*",$ordem=null,$dbwhere=""){
    $sql = "select ";
    if($campos != "*" ){
      $campos_sql = split("#",$campos);
      $virgula = "";
      for($i=0;$i<sizeof($campos_sql);$i++){
        $sql .= $virgula.$campos_sql[$i];
        $virgula = ",";
      }
    }else{
      $sql .= $campos;
    }
    $sql .= " from pagordem ";
    $sql .= "      inner join empempenho  on  empempenho.e60_numemp = pagordem.e50_numemp";
    $sql .= "      inner join cgm  on  cgm.z01_numcgm = empempenho.e60_numcgm";
    $sql .= "      inner join db_config  on  db_config.codigo = empempenho.e60_instit";
    $sql .= "      inner join orcdotacao  on  orcdotacao.o58_anousu = empempenho.e60_anousu and  orcdotacao.o58_coddot = empempenho.e60_coddot";
    $sql .= "      inner join pctipocompra  on  pctipocompra.pc50_codcom = empempenho.e60_codcom";
    $sql .= "      inner join emptipo  on  emptipo.e41_codtipo = empempenho.e60_codtipo";
    $sql .= "      left join conlancamord on c80_codord = pagordem.e50_codord ";
    $sql .= "      left join conlancampag on c82_codlan = conlancamord.c80_codlan";
    $sql .= "      left join conlancam   on c70_codlan   = conlancamord.c80_codlan ";
    $sql .= "      left join conlancamdoc  on c71_codlan   = conlancam.c70_codlan ";
    $sql .= "      left join conhistdoc on c53_coddoc  = conlancamdoc.c71_coddoc ";
    $sql .= "      left join conplanoreduz on c61_reduz  = conlancampag.c82_reduz and c82_anousu = c61_anousu";
    $sql .= "      left join conplano  on c60_codcon = conplanoreduz.c61_codcon and c60_anousu=c61_anousu";
    $sql .= "      left  join pagordemconta on pagordemconta.e49_codord = pagordem.e50_codord";

    $sql2 = "";
    if($dbwhere==""){
      if($e50_codord!=null ){
        $sql2 .= " where pagordem.e50_codord = $e50_codord ";
      }
    }else if($dbwhere != ""){
      $sql2 = " where $dbwhere";
    }
    $sql .= $sql2;
    if($ordem != null ){
      $sql .= " order by ";
      $campos_sql = split("#",$ordem);
      $virgula = "";
      for($i=0;$i<sizeof($campos_sql);$i++){
        $sql .= $virgula.$campos_sql[$i];
        $virgula = ",";
      }
    }
    return $sql;
  }
   function sql_query_pagordemagenda ( $e50_codord=null,$campos="*",$ordem=null,$dbwhere=""){
    $sql = "select ";
    if($campos != "*" ){
      $campos_sql = split("#",$campos);
      $virgula = "";
      for($i=0;$i<sizeof($campos_sql);$i++){
        $sql .= $virgula.$campos_sql[$i];
        $virgula = ",";
      }
    }else{
      $sql .= $campos;
    }
    $sql .= " from pagordem ";
    $sql .= "      inner join pagordemele  on pagordemele.e53_codord   = pagordem.e50_codord";
    $sql .= "      inner join empempenho   on empempenho.e60_numemp    = pagordem.e50_numemp";
    $sql .= "      inner join cgm          on cgm.z01_numcgm           = empempenho.e60_numcgm";
    $sql .= "      inner join db_config    on db_config.codigo         = empempenho.e60_instit";
    $sql .= "      inner join orcdotacao   on orcdotacao.o58_anousu    = empempenho.e60_anousu and";
    $sql .= "                                 orcdotacao.o58_coddot    = empempenho.e60_coddot";
    $sql .= "      inner join pctipocompra on pctipocompra.pc50_codcom = empempenho.e60_codcom";
    $sql .= "      inner join emptipo      on emptipo.e41_codtipo      = empempenho.e60_codtipo";
    $sql .= "      left  join empord       on empord.e82_codord        = pagordem.e50_codord";
    $sql .= "      left  join empagemov    on empagemov.e81_codmov     = empord.e82_codmov";
    $sql .= "      left  join empage       on empage.e80_codage        = empagemov.e81_codage";
    $sql2 = "";
    if($dbwhere==""){
      if($e50_codord!=null ){
        $sql2 .= " where pagordem.e50_codord = $e50_codord ";
      }
    }else if($dbwhere != ""){
      $sql2 = " where $dbwhere";
    }
    $sql .= $sql2;
    if($ordem != null ){
      $sql .= " order by ";
      $campos_sql = split("#",$ordem);
      $virgula = "";
      for($i=0;$i<sizeof($campos_sql);$i++){
        $sql .= $virgula.$campos_sql[$i];
        $virgula = ",";
      }
    }
    return $sql;
  }
   function sql_query_pagordemele ( $e50_codord=null,$campos="*",$ordem=null,$dbwhere=""){
    $sql = "select ";
    if($campos != "*" ){
      $campos_sql = split("#",$campos);
      $virgula = "";
      for($i=0;$i<sizeof($campos_sql);$i++){
        $sql .= $virgula.$campos_sql[$i];
        $virgula = ",";
      }
    }else{
      $sql .= $campos;
    }
    $sql .= " FROM pagordem ";
    $sql .= " INNER JOIN pagordemele ON pagordemele.e53_codord = pagordem.e50_codord";
    $sql .= " INNER JOIN empempenho ON empempenho.e60_numemp = pagordem.e50_numemp";
    $sql .= " INNER JOIN cgm ON cgm.z01_numcgm = empempenho.e60_numcgm";
    $sql .= " INNER JOIN db_config ON db_config.codigo = empempenho.e60_instit";
    $sql .= " INNER JOIN orcdotacao ON orcdotacao.o58_anousu = empempenho.e60_anousu and  orcdotacao.o58_coddot = empempenho.e60_coddot";
    $sql .= " INNER JOIN orctiporec ON orctiporec.o15_codigo = orcdotacao.o58_codigo";
    $sql .= " INNER JOIN emptipo ON emptipo.e41_codtipo = empempenho.e60_codtipo";
    $sql .= " LEFT JOIN orcelemento ON (o56_codele, o56_anousu) = (e53_codele, e60_anousu)";

    $sql2 = "";
    if($dbwhere==""){
      if($e50_codord!=null ){
        $sql2 .= " where pagordem.e50_codord = $e50_codord ";
      }
    }else if($dbwhere != ""){
      $sql2 = " where $dbwhere";
    }
    $sql .= $sql2;
    if($ordem != null ){
      $sql .= " order by ";
      $campos_sql = split("#",$ordem);
      $virgula = "";
      for($i=0;$i<sizeof($campos_sql);$i++){
        $sql .= $virgula.$campos_sql[$i];
        $virgula = ",";
      }
    }
    //     echo $sql."<br><br>";
    return $sql;
  }
   function sql_query_pagordemele2 ( $e50_codord=null,$campos="*",$ordem=null,$dbwhere=""){
    $sql = "select ";
    if($campos != "*" ){
      $campos_sql = split("#",$campos);
      $virgula = "";
      for($i=0;$i<sizeof($campos_sql);$i++){
        $sql .= $virgula.$campos_sql[$i];
        $virgula = ",";
      }
    }else{
      $sql .= $campos;
    }
    $sql .= " from pagordem ";
    $sql .= "      inner join pagordemele  on  pagordemele.e53_codord = pagordem.e50_codord";
    $sql .= "      inner join empempenho  on  empempenho.e60_numemp = pagordem.e50_numemp";
    $sql .= "      left  join empord on empord.e82_codord = pagordem.e50_codord ";
    $sql .= "      inner join cgm  on  cgm.z01_numcgm = empempenho.e60_numcgm";
    $sql .= "      inner join db_config  on  db_config.codigo = empempenho.e60_instit";
    $sql .= "      inner join orcdotacao  on  orcdotacao.o58_anousu = empempenho.e60_anousu and  orcdotacao.o58_coddot = empempenho.e60_coddot";
    $sql .= "      inner join orctiporec on  orctiporec.o15_codigo =  orcdotacao.o58_codigo    ";
    $sql .= "      inner join emptipo  on  emptipo.e41_codtipo = empempenho.e60_codtipo";
    $sql .= "      left join (
      select e82_codord,
             sum(e81_valor) as e81_valor,
             max(e81_codmov) as e81_codmov,
             e81_codage,
             e81_cancelado
               from   empord
               inner join empagemov on e82_codmov = e81_codmov
               group by
               e82_codord,
             e81_codage,
             e81_cancelado
               ) as xxx on xxx.e82_codord = pagordemele.e53_codord";
    $sql .= "      left join empageconf on empageconf.e86_codmov = xxx.e81_codmov ";
    $sql .= "      left join empageconfgera on empageconfgera.e90_codmov = xxx.e81_codmov";
    $sql2 = "";
    if($dbwhere==""){
      if($e50_codord!=null ){
        $sql2 .= " where pagordem.e50_codord = $e50_codord ";
      }
    }else if($dbwhere != ""){
      $sql2 = " where $dbwhere";
    }
    $sql .= $sql2;
    if($ordem != null ){
      $sql .= " order by ";
      $campos_sql = split("#",$ordem);
      $virgula = "";
      for($i=0;$i<sizeof($campos_sql);$i++){
        $sql .= $virgula.$campos_sql[$i];
        $virgula = ",";
      }
    }
    return $sql;
  }
   function sql_query_pagordemeleempage ( $e50_codord=null,$campos="*",$ordem=null,$dbwhere=""){
    $sql = "select ";
    if($campos != "*" ){
      $campos_sql = split("#",$campos);
      $virgula = "";
      for($i=0;$i<sizeof($campos_sql);$i++){
        $sql .= $virgula.$campos_sql[$i];
        $virgula = ",";
      }
    }else{
      $sql .= $campos;
    }
    $sql .= " from pagordem ";
    $sql .= "      inner join pagordemele  on  pagordemele.e53_codord = pagordem.e50_codord";
    $sql .= "      inner join empempenho  on  empempenho.e60_numemp = pagordem.e50_numemp";
    $sql .= "      inner join cgm  on  cgm.z01_numcgm = empempenho.e60_numcgm";
    $sql .= "      inner join db_config  on  db_config.codigo = empempenho.e60_instit";
    $sql .= "      inner join orcdotacao  on  orcdotacao.o58_anousu = empempenho.e60_anousu and  orcdotacao.o58_coddot = empempenho.e60_coddot";
    $sql .= "      inner join orctiporec on  orctiporec.o15_codigo =  orcdotacao.o58_codigo    ";
    $sql .= "      inner join emptipo  on  emptipo.e41_codtipo = empempenho.e60_codtipo";
    $sql .= "      left join (select e82_codord as codord, sum(e81_valor) as e81_valor from empord inner join empagemov on e82_codmov = e81_codmov where e81_cancelado is null group by e82_codord) as xxx on xxx.codord = pagordemele.e53_codord";
    $sql .= "      inner join empord  on  empord.e82_codord = pagordem.e50_codord";
    $sql .= "      inner join empagemov  on  empagemov.e81_codmov = empord.e82_codmov";
    $sql .= "      left join empageconf  on  empageconf.e86_codmov = empord.e82_codmov";
    $sql .= "      left join pagordemconta on e49_codord = e82_codord ";
    $sql .= "      left join cgm a on a.z01_numcgm = e49_numcgm ";
    $sql .= "      left join empageconfgera on empageconfgera.e90_codmov = empagemov.e81_codmov ";

    $sql2 = "";
    if($dbwhere==""){
      if($e50_codord!=null ){
        $sql2 .= " where pagordem.e50_codord = $e50_codord ";
      }
    }else if($dbwhere != ""){
      $sql2 = " where $dbwhere";
    }
    $sql .= $sql2;
    if($ordem != null ){
      $sql .= " order by ";
      $campos_sql = split("#",$ordem);
      $virgula = "";
      for($i=0;$i<sizeof($campos_sql);$i++){
        $sql .= $virgula.$campos_sql[$i];
        $virgula = ",";
      }
    }
    return $sql;
  }

  function sql_query_pagDiversos( $e50_codord=null,$campos="*",$ordem=null,$dbwhere="") {
    $sql = "select ";
    if($campos != "*" ) {

      $campos_sql = split("#",$campos);
      $virgula = "";
      for($i=0;$i<sizeof($campos_sql);$i++) {

        $sql .= $virgula.$campos_sql[$i];
        $virgula = ",";
      }
    }else{
      $sql .= $campos;
    }
    $sql .= " from pagordem ";
    $sql .= "      inner join empempenho     on empempenho.e60_numemp     = pagordem.e50_numemp              ";
    $sql .= "      inner join cgm            on cgm.z01_numcgm 			      = empempenho.e60_numcgm            ";
    $sql .= "      inner join db_config      on db_config.codigo          = empempenho.e60_instit            ";
    $sql .= "      inner join orcdotacao     on orcdotacao.o58_anousu     = empempenho.e60_anousu            ";
    $sql .= "                               and orcdotacao.o58_coddot     = empempenho.e60_coddot            ";
    $sql .= "      inner join pctipocompra   on pctipocompra.pc50_codcom  = empempenho.e60_codcom            ";
    $sql .= "      inner join emptipo        on emptipo.e41_codtipo 		  = empempenho.e60_codtipo           ";
    $sql .= "      inner join conlancamord   on c80_codord                = pagordem.e50_codord              ";
    $sql .= "      inner join conlancampag   on c82_codlan                = conlancamord.c80_codlan          ";
    $sql .= "      inner join conlancam      on c70_codlan                = conlancamord.c80_codlan          ";
    $sql .= "      inner join conlancamdoc   on c71_codlan                = conlancam.c70_codlan             ";
    $sql .= "      inner join conhistdoc     on c53_coddoc                = conlancamdoc.c71_coddoc          ";
    $sql .= "      inner join conplanoreduz  on c61_reduz                 = conlancampag.c82_reduz           ";
    $sql .= "       											  and c82_anousu                = c61_anousu							         ";
    $sql .= "      inner join conplano       on c60_codcon                = conplanoreduz.c61_codcon         ";
    $sql .= "      												  and c60_anousu                = c61_anousu                       ";
    $sql .= "      left  join pagordemconta  on pagordemconta.e49_codord  = pagordem.e50_codord              ";
    $sql .= "      inner join pagordemele    on pagordemele.e53_codord    = pagordem.e50_codord              ";

    $sql2 = "";
    if($dbwhere=="") {
      if($e50_codord!=null ) {
        $sql2 .= " where pagordem.e50_codord = $e50_codord ";
      }
    } else if($dbwhere != "") {
      $sql2 = " where $dbwhere";
    }
    $sql .= $sql2;
    if($ordem != null ) {

      $sql .= " order by ";
      $campos_sql = split("#",$ordem);
      $virgula = "";
      for($i=0;$i<sizeof($campos_sql);$i++) {

        $sql .= $virgula.$campos_sql[$i];
        $virgula = ",";
      }
    }
    return $sql;
  }


  function sql_query_movimento($e50_codord = null, $campos = "*", $ordem = null, $dbwhere = "") {

    $sql = "select ";
    if($campos != "*" ) {

      $campos_sql = split("#",$campos);
      $virgula = "";
      for($i=0;$i<sizeof($campos_sql);$i++) {

        $sql .= $virgula.$campos_sql[$i];
        $virgula = ",";
      }
    }else{
      $sql .= $campos;
    }
    $sql .= " from pagordem ";
    $sql .= "      inner join  empord    on empord.e82_codord    = pagordem.e50_codord  ";
    $sql .= "      inner join  empagemov on empagemov.e81_codmov = empord.e82_codmov";

    $sql2 = "";
    if($dbwhere=="") {
      if($e50_codord!=null ) {
        $sql2 .= " where pagordem.e50_codord = $e50_codord ";
      }
    } else if($dbwhere != "") {
      $sql2 = " where $dbwhere";
    }
    $sql .= $sql2;
    if($ordem != null ) {

      $sql .= " order by ";
      $campos_sql = split("#",$ordem);
      $virgula = "";
      for($i=0;$i<sizeof($campos_sql);$i++) {

        $sql .= $virgula.$campos_sql[$i];
        $virgula = ",";
      }
    }
    return $sql;
  }

  public function alteraDataOp($e50_codord, $atualDataOp, $novaDataOp, $mesAtual, $ordemCompraTipo){
        $atualDataOpMes = date('m', strtotime($atualDataOp));
        $novaDataOpMes = date('m', strtotime($novaDataOp));
        $novaDataOpAno = date('Y', strtotime($novaDataOp));
        $mesAtual = date('m',db_getsession('DB_datausu'));

        if ($novaDataOpMes > $atualDataOpMes){
            $mesMenor = $atualDataOpMes;
        } else {
            $mesMenor = $novaDataOpMes;
        }

        $sSql = "SELECT fc_startsession();";

        $sSql .= " CREATE TEMP TABLE pgto_emp ON COMMIT DROP AS";
        $sSql .= " SELECT c80_codord,";
        $sSql .= "        c80_codlan,";
        $sSql .= "        c80_data";
        $sSql .= " FROM conlancamord";
        $sSql .= " JOIN conlancamdoc ON c71_codlan = c80_codlan";
        $sSql .= " JOIN conhistdoc ON c53_coddoc = c71_coddoc";
        $sSql .= " WHERE c80_codord = {$e50_codord}";
        $sSql .= "   AND c53_tipo = 20;";
        
        $sSql .= " CREATE TEMP TABLE saldo_ctas ON COMMIT DROP AS";
        $sSql .= " SELECT DISTINCT conplanoexesaldo.*,";
        $sSql .= "        deb.c69_data c69_data";
        $sSql .= " FROM conplanoexesaldo";
        $sSql .= " JOIN conlancamval deb ON (deb.c69_debito, deb.c69_anousu, EXTRACT (MONTH FROM deb.c69_data)::integer) = (c68_reduz, c68_anousu, c68_mes)";
        $sSql .= " WHERE deb.c69_codlan IN (SELECT c80_codlan FROM pgto_emp)";
        $sSql .= " UNION ALL";
        $sSql .= " SELECT DISTINCT conplanoexesaldo.*,";
        $sSql .= "        cred.c69_data c69_data";
        $sSql .= " FROM conplanoexesaldo";
        $sSql .= " JOIN conlancamval cred ON (cred.c69_credito, cred.c69_anousu, EXTRACT (MONTH FROM cred.c69_data)::integer) = (c68_reduz, c68_anousu, c68_mes)";
        $sSql .= " WHERE cred.c69_codlan IN (SELECT c80_codlan FROM pgto_emp);";
        
        $sSql .= " CREATE TEMP TABLE nota_altera ON COMMIT DROP AS";
        $sSql .= " SELECT c75_numemp AS empenho,";
        $sSql .= "        c80_codlan AS lancamento,";
        $sSql .= "        c80_codord AS ord_pag,";
        $sSql .= "        m72_codnota AS ordem_compra,";
        $sSql .= "        c66_codnota AS nota_liq,";
        $sSql .= "        c80_data AS data_nota";
        $sSql .= " FROM conlancamemp";
        $sSql .= " JOIN conlancamord ON c80_codlan = c75_codlan";
        $sSql .= " JOIN conlancamnota ON c66_codlan = c75_codlan";
        $sSql .= " JOIN conlancamdoc ON c71_codlan = c75_codlan";
        $sSql .= " JOIN empnotaord ON c66_codnota = m72_codnota";
        $sSql .= " JOIN conhistdoc ON c71_coddoc = c53_coddoc";
        $sSql .= " WHERE c80_codlan IN (SELECT c80_codlan FROM pgto_emp);";
        
        if($ordemCompraTipo === 'virtual'){

          $sSql .= " CREATE TEMPORARY TABLE w_matordem ON COMMIT DROP AS";
          $sSql .= " SELECT m51_codordem FROM conlancamnota";
          $sSql .= " JOIN empnotaord ON c66_codnota = m72_codnota";
          $sSql .= " JOIN matordem ON m72_codordem = m51_codordem";
          $sSql .= " WHERE c66_codlan IN (SELECT c80_codlan FROM pgto_emp);";

          // -- Ordens de Compra
          // -- Confirmar se alterar a OP para data posterior a data da Ordem de Compra, essa Ordem de Compra tb devera ser alterada.
          
          $sSql .= " UPDATE matordemanu SET m53_data = '{$novaDataOp}'";
          $sSql .= " WHERE m53_codordem IN";
          $sSql .= "     (SELECT m51_codordem";
          $sSql .= "      FROM w_matordem);";
          
          $sSql .= " UPDATE matordem SET m51_data = '{$novaDataOp}'";
          $sSql .= " WHERE m51_codordem IN";
          $sSql .= "      (SELECT m51_codordem";
          $sSql .= "       FROM w_matordem);";
        }
          
        // -- Fim Ordens de Compra
        
        $sSql .= " UPDATE empnota";
        $sSql .= " SET e69_dtnota = '{$novaDataOp}',";
        $sSql .= "     e69_dtrecebe = '{$novaDataOp}',";
        $sSql .= "     e69_dtinclusao = '{$novaDataOp}'";
        $sSql .= " WHERE e69_codnota IN";
        $sSql .= "     (SELECT nota_liq FROM nota_altera);";
        
        $sSql .= " UPDATE pagordem";
        $sSql .= " SET e50_data = '{$novaDataOp}'";
        $sSql .= " WHERE e50_codord IN";
        $sSql .= "     (SELECT ord_pag FROM nota_altera);";
        
        $sSql .= " ALTER TABLE conlancamval DISABLE TRIGGER ALL;";
        
        $sSql .= " UPDATE conlancamval";
        $sSql .= " SET c69_data = '{$novaDataOp}'";
        $sSql .= " WHERE c69_codlan IN (SELECT lancamento FROM nota_altera);";
        
        $sSql .= " ALTER TABLE conlancamval ENABLE TRIGGER ALL;";
        
        $sSql .= " UPDATE conlancamemp";
        $sSql .= " SET c75_data = '{$novaDataOp}'";
        $sSql .= " WHERE c75_codlan IN (SELECT lancamento FROM nota_altera);";
        
        $sSql .= " UPDATE conlancamdoc";
        $sSql .= " SET c71_data = '{$novaDataOp}'";
        $sSql .= " WHERE c71_codlan IN (SELECT lancamento FROM nota_altera);";
        
        $sSql .= " UPDATE conlancamdot";
        $sSql .= " SET c73_data = '{$novaDataOp}'";
        $sSql .= " WHERE c73_codlan IN (SELECT lancamento FROM nota_altera);";
        
        $sSql .= " UPDATE conlancamord";
        $sSql .= " SET c80_data = '{$novaDataOp}'";
        $sSql .= " WHERE c80_codlan IN (SELECT lancamento FROM nota_altera);";
        
        $sSql .= " UPDATE conlancamcgm";
        $sSql .= " SET c76_data = '{$novaDataOp}'";
        $sSql .= " WHERE c76_codlan IN (SELECT lancamento FROM nota_altera);";
        
        $sSql .= " UPDATE conlancam";
        $sSql .= " SET c70_data = '{$novaDataOp}'";
        $sSql .= " WHERE c70_codlan IN (SELECT lancamento FROM nota_altera);";

        for($i = $mesMenor; $i <= $mesAtual; $i++){
            $sSql .= " DELETE FROM conplanoexesaldo";
            $sSql .= " USING saldo_ctas";
            $sSql .= " WHERE (saldo_ctas.c68_reduz, saldo_ctas.c68_anousu) = (conplanoexesaldo.c68_reduz, conplanoexesaldo.c68_anousu)";
            $sSql .= " AND conplanoexesaldo.c68_mes = {$i};";

            $sSql .= " CREATE TEMP TABLE landeb".$i." ON COMMIT DROP AS";
            $sSql .= " SELECT c69_anousu,";
            $sSql .= "     c69_debito,";
            $sSql .= "     to_char(conlancamval.c69_data,'MM')::integer,";
            $sSql .= "     sum(round(c69_valor,2)),0::float8";
            $sSql .= " FROM conlancamval";
            $sSql .= " JOIN saldo_ctas ON (saldo_ctas.c68_reduz, saldo_ctas.c68_anousu) = (conlancamval.c69_debito, conlancamval.c69_anousu)";
            $sSql .= " WHERE conlancamval.c69_anousu = {$novaDataOpAno}";
            $sSql .= " AND EXTRACT (MONTH FROM conlancamval.c69_data)::integer = {$i}";
            $sSql .= " GROUP BY conlancamval.c69_anousu, conlancamval.c69_debito, to_char(conlancamval.c69_data,'MM')::integer;";
            
            $sSql .= " CREATE TEMP TABLE lancre".$i." ON COMMIT DROP AS";
            $sSql .= " SELECT c69_anousu,";
            $sSql .= "     c69_credito,";
            $sSql .= "     to_char(conlancamval.c69_data,'MM')::integer as c69_data,";
            $sSql .= "     0::float8,";
            $sSql .= "     sum(round(c69_valor,2))";
            $sSql .= " FROM conlancamval";
            $sSql .= " JOIN saldo_ctas ON (saldo_ctas.c68_reduz, saldo_ctas.c68_anousu) = (conlancamval.c69_credito, conlancamval.c69_anousu)";
            $sSql .= " WHERE conlancamval.c69_anousu = {$novaDataOpAno}";
            $sSql .= " AND EXTRACT (MONTH FROM conlancamval.c69_data)::integer = {$i}";
            $sSql .= " GROUP BY conlancamval.c69_anousu, conlancamval.c69_credito, to_char(conlancamval.c69_data,'MM')::integer;";
            
            $sSql .= " INSERT INTO conplanoexesaldo";
            $sSql .= " SELECT * FROM landeb".$i."";
            $sSql .= " WHERE c69_anousu = {$novaDataOpAno};";
            
            $sSql .= " UPDATE conplanoexesaldo";
            $sSql .= " SET c68_credito = lancre".$i.".sum";
            $sSql .= " FROM lancre".$i."";
            $sSql .= " WHERE c68_anousu = lancre".$i.".c69_anousu";
            $sSql .= " AND c68_reduz = lancre".$i.".c69_credito";
            $sSql .= " AND c68_mes = lancre".$i.".c69_data";
            $sSql .= " AND c68_anousu = {$novaDataOpAno};";
            
            $sSql .= " DELETE FROM lancre".$i."";
            $sSql .= " USING conplanoexesaldo";
            $sSql .= " WHERE lancre".$i.".c69_anousu = conplanoexesaldo.c68_anousu";
            $sSql .= " AND conplanoexesaldo.c68_reduz = lancre".$i.".c69_credito";
            $sSql .= " AND conplanoexesaldo.c68_mes = lancre".$i.".c69_data";
            $sSql .= " AND c68_anousu = {$novaDataOpAno};";
            
            $sSql .= " INSERT INTO conplanoexesaldo";
            $sSql .= " SELECT * FROM lancre".$i."";
            $sSql .= " WHERE c69_anousu = {$novaDataOpAno};";
        }
        $sSql .= " CREATE TEMP TABLE cod_mov ON COMMIT DROP AS";
        $sSql .= " SELECT e81_codmov as codmov FROM empage ";
        $sSql .= " JOIN empagemov ON e80_codage = e81_codage";
        $sSql .= " JOIN empord ON e81_codmov = e82_codmov";
        $sSql .= " WHERE e82_codord = {$e50_codord};";

        $sSql .= " CREATE TEMP TABLE cod_age ON COMMIT DROP AS";
        $sSql .= " SELECT e80_codage AS codage ";
        $sSql .= " FROM empage ";
        $sSql .= " WHERE e80_data = '{$novaDataOp}'";
        $sSql .= " AND e80_instit = ".db_getsession('DB_instit')." limit 1;";

        $sSql .= " WITH nova_agenda AS (";
        $sSql .= "     INSERT INTO empage (e80_codage, e80_data, e80_instit) ";
        $sSql .= "     VALUES ((SELECT nextval('empage_e80_codage_seq')),'{$novaDataOp}', ".db_getsession('DB_instit').") ";
        $sSql .= "     RETURNING e80_codage)";
        $sSql .= " UPDATE empagemov";
        $sSql .= " SET e81_codage = ";
        $sSql .= "     CASE";
        $sSql .= "         WHEN (SELECT COUNT(*) FROM cod_age) > 0 THEN (SELECT codage FROM cod_age)";
        $sSql .= "         ELSE (select e80_codage from nova_agenda)";
        $sSql .= "     END";
        $sSql .= " WHERE e81_codmov = (SELECT codmov FROM cod_mov); ";

        return $sSql;
  }

  public function alteraDataEstorno($e50_codord, $atualDataEstorno, $novaDataEstorno, $mesAtual){
    $atualDataEstornoMes = date('m', strtotime($atualDataEstorno));
    $novaDataEstornoMes = date('m', strtotime($novaDataEstorno));
    $novaDataEmpenhoAno = date('Y', strtotime($novaDataEstorno));
    $mesAtual = date('m',db_getsession('DB_datausu'));

    if ($novaDataEstornoMes > $atualDataEstornoMes){
        $mesMenor = $atualDataEstornoMes;
    } else {
        $mesMenor = $novaDataEstornoMes;
    }

    $sSql = "SELECT fc_startsession();";

    $sSql .= " CREATE TEMP TABLE pgto_emp ON COMMIT DROP AS";
    $sSql .= " SELECT c80_codord,";
    $sSql .= "        c80_codlan,";
    $sSql .= "        c80_data";
    $sSql .= " FROM conlancamord";
    $sSql .= " JOIN conlancamdoc ON c71_codlan = c80_codlan";
    $sSql .= " JOIN conhistdoc ON c53_coddoc = c71_coddoc";
    $sSql .= " WHERE c80_codord = {$e50_codord}";
    $sSql .= "   AND c53_tipo = 21;";
    
    $sSql .= " CREATE TEMP TABLE saldo_ctas ON COMMIT DROP AS";
    $sSql .= " SELECT DISTINCT conplanoexesaldo.*,";
    $sSql .= "        deb.c69_data c69_data";
    $sSql .= " FROM conplanoexesaldo";
    $sSql .= " JOIN conlancamval deb ON (deb.c69_debito, deb.c69_anousu, EXTRACT (MONTH FROM deb.c69_data)::integer) = (c68_reduz, c68_anousu, c68_mes)";
    $sSql .= " WHERE deb.c69_codlan IN (SELECT c80_codlan FROM pgto_emp)";
    $sSql .= " UNION ALL";
    $sSql .= " SELECT DISTINCT conplanoexesaldo.*,";
    $sSql .= "        cred.c69_data c69_data";
    $sSql .= " FROM conplanoexesaldo";
    $sSql .= " JOIN conlancamval cred ON (cred.c69_credito, cred.c69_anousu, EXTRACT (MONTH FROM cred.c69_data)::integer) = (c68_reduz, c68_anousu, c68_mes)";
    $sSql .= " WHERE cred.c69_codlan IN (SELECT c80_codlan FROM pgto_emp);     ";
    
    $sSql .= " CREATE TEMP TABLE nota_altera ON COMMIT DROP AS";
    $sSql .= " SELECT c75_numemp AS empenho,";
    $sSql .= "        c80_codlan AS lancamento,";
    $sSql .= "        c80_codord AS ord_pag,";
    $sSql .= "        m72_codnota AS ordem_compra,";
    $sSql .= "        c66_codnota AS nota_liq,";
    $sSql .= "        c80_data AS data_nota";
    $sSql .= " FROM conlancamemp";
    $sSql .= " JOIN conlancamord ON c80_codlan = c75_codlan";
    $sSql .= " JOIN conlancamnota ON c66_codlan = c75_codlan";
    $sSql .= " JOIN conlancamdoc ON c71_codlan = c75_codlan";
    $sSql .= " JOIN empnotaord ON c66_codnota = m72_codnota";
    $sSql .= " JOIN conhistdoc ON c71_coddoc = c53_coddoc";
    $sSql .= " WHERE c80_codlan IN (SELECT c80_codlan FROM pgto_emp);";

    $sSql .= " ALTER TABLE conlancamval DISABLE TRIGGER ALL;";

    $sSql .= " UPDATE conlancamval";
    $sSql .= " SET c69_data = '{$novaDataEstorno}'";
    $sSql .= " WHERE c69_codlan IN (SELECT lancamento FROM nota_altera);";
    
    $sSql .= " ALTER TABLE conlancamval ENABLE TRIGGER ALL;";
    
    $sSql .= " UPDATE conlancamemp";
    $sSql .= " SET c75_data = '{$novaDataEstorno}'";
    $sSql .= " WHERE c75_codlan IN (SELECT lancamento FROM nota_altera);  ";
    
    $sSql .= " UPDATE conlancamdoc";
    $sSql .= " SET c71_data = '{$novaDataEstorno}'";
    $sSql .= " WHERE c71_codlan IN (SELECT lancamento FROM nota_altera);";
    
    $sSql .= " UPDATE conlancamdot";
    $sSql .= " SET c73_data = '{$novaDataEstorno}'";
    $sSql .= " WHERE c73_codlan IN (SELECT lancamento FROM nota_altera);";
    
    $sSql .= " UPDATE conlancamord";
    $sSql .= " SET c80_data = '{$novaDataEstorno}'";
    $sSql .= " WHERE c80_codlan IN (SELECT lancamento FROM nota_altera);";
    
    $sSql .= " UPDATE conlancamcgm";
    $sSql .= " SET c76_data = '{$novaDataEstorno}'";
    $sSql .= " WHERE c76_codlan IN (SELECT lancamento FROM nota_altera);";
    
    $sSql .= " UPDATE conlancam";
    $sSql .= " SET c70_data = '{$novaDataEstorno}'";
    $sSql .= " WHERE c70_codlan IN (SELECT lancamento FROM nota_altera);";

    for($i = $mesMenor; $i <= $mesAtual; $i++){
        $sSql .= " DELETE FROM conplanoexesaldo";
        $sSql .= " USING saldo_ctas";
        $sSql .= " WHERE (saldo_ctas.c68_reduz, saldo_ctas.c68_anousu) = (conplanoexesaldo.c68_reduz, conplanoexesaldo.c68_anousu)";
        $sSql .= " AND conplanoexesaldo.c68_mes = {$i};";

        $sSql .= " CREATE TEMP TABLE landeb".$i." ON COMMIT DROP AS";
        $sSql .= " SELECT c69_anousu,";
        $sSql .= "     c69_debito,";
        $sSql .= "     to_char(conlancamval.c69_data,'MM')::integer,";
        $sSql .= "     sum(round(c69_valor,2)),0::float8";
        $sSql .= " FROM conlancamval";
        $sSql .= " JOIN saldo_ctas ON (saldo_ctas.c68_reduz, saldo_ctas.c68_anousu) = (conlancamval.c69_debito, conlancamval.c69_anousu)";
        $sSql .= " WHERE conlancamval.c69_anousu = {$novaDataEmpenhoAno}";
        $sSql .= " AND EXTRACT (MONTH FROM conlancamval.c69_data)::integer = {$i}";
        $sSql .= " GROUP BY conlancamval.c69_anousu, conlancamval.c69_debito, to_char(conlancamval.c69_data,'MM')::integer;";
        
        $sSql .= " CREATE TEMP TABLE lancre".$i." ON COMMIT DROP AS";
        $sSql .= " SELECT c69_anousu,";
        $sSql .= "     c69_credito,";
        $sSql .= "     to_char(conlancamval.c69_data,'MM')::integer as c69_data,";
        $sSql .= "     0::float8,";
        $sSql .= "     sum(round(c69_valor,2))";
        $sSql .= " FROM conlancamval";
        $sSql .= " JOIN saldo_ctas ON (saldo_ctas.c68_reduz, saldo_ctas.c68_anousu) = (conlancamval.c69_credito, conlancamval.c69_anousu)";
        $sSql .= " WHERE conlancamval.c69_anousu = {$novaDataEmpenhoAno}";
        $sSql .= " AND EXTRACT (MONTH FROM conlancamval.c69_data)::integer = {$i}";
        $sSql .= " GROUP BY conlancamval.c69_anousu, conlancamval.c69_credito, to_char(conlancamval.c69_data,'MM')::integer;";
        
        $sSql .= " INSERT INTO conplanoexesaldo";
        $sSql .= " SELECT * FROM landeb".$i."";
        $sSql .= " WHERE c69_anousu = {$novaDataEmpenhoAno};";
        
        $sSql .= " UPDATE conplanoexesaldo";
        $sSql .= " SET c68_credito = lancre".$i.".sum";
        $sSql .= " FROM lancre".$i."";
        $sSql .= " WHERE c68_anousu = lancre".$i.".c69_anousu";
        $sSql .= " AND c68_reduz = lancre".$i.".c69_credito";
        $sSql .= " AND c68_mes = lancre".$i.".c69_data";
        $sSql .= " AND c68_anousu = {$novaDataEmpenhoAno};";
        
        $sSql .= " DELETE FROM lancre".$i."";
        $sSql .= " USING conplanoexesaldo";
        $sSql .= " WHERE lancre".$i.".c69_anousu = conplanoexesaldo.c68_anousu";
        $sSql .= " AND conplanoexesaldo.c68_reduz = lancre".$i.".c69_credito";
        $sSql .= " AND conplanoexesaldo.c68_mes = lancre".$i.".c69_data";
        $sSql .= " AND c68_anousu = {$novaDataEmpenhoAno};";
        
        $sSql .= " INSERT INTO conplanoexesaldo";
        $sSql .= " SELECT * FROM lancre".$i."";
        $sSql .= " WHERE c69_anousu = {$novaDataEmpenhoAno};";
    }
    return $sSql;
}

  function pesquisaNumeroOP($iNumemp){
    $sSql = "select max(e50_numliquidacao) as e50_numliquidacao from pagordem where e50_numemp = {$iNumemp}";
    $result = db_utils::fieldsMemory(db_query($sSql), 0)->e50_numliquidacao;
    if($result != null){
      $e50_numliquidacao = $result;
    }else{
      $e50_numliquidacao = 0;
    }
    return $e50_numliquidacao;
  }

  function getContratoDataByEmpenho($iNumEmp) {
    
    $sql = "SELECT distinct
                acordo.ac16_numero,
                acordo.ac16_sequencial,
                acordo.ac16_anousu
            FROM 
                pagordem
            INNER JOIN 
                empempaut on (empempaut.e61_numemp = pagordem.e50_numemp)
            INNER JOIN 
                acordoempautoriza on (acordoempautoriza.ac45_empautoriza = empempaut.e61_autori)
            INNER JOIN 
                acordo on (acordo.ac16_sequencial = acordoempautoriza.ac45_acordo)
            WHERE
                pagordem.e50_numemp IN ({$iNumEmp})
          ";

    $result = pg_exec($sql);

    return $result;
  }

  function consultaNotaDespesa ( $e50_codord=null, $campos="*", $ordem=null, $dbwhere=""){
    $sql = "select ";
    if($campos != "*" ){
      $campos_sql = split("#",$campos);
      $virgula = "";
      for($i=0;$i<sizeof($campos_sql);$i++){
        $sql .= $virgula.$campos_sql[$i];
        $virgula = ",";
      }
    }else{
      $sql .= $campos;
    }
    $sql .= "    from";
    $sql .= "    pagordem";
    $sql .= "  left join db_usuarios on";
    $sql .= "    db_usuarios.id_usuario = pagordem.e50_id_usuario";
    $sql .= "  inner join empempenho on";
    $sql .= "    empempenho.e60_numemp = pagordem.e50_numemp";
    $sql .= "  inner join cgm on";
    $sql .= "    cgm.z01_numcgm = empempenho.e60_numcgm";
    $sql .= "  inner join db_config on";
    $sql .= "    db_config.codigo = empempenho.e60_instit";
    $sql .= "  inner join orcdotacao on";
    $sql .= "    orcdotacao.o58_anousu = empempenho.e60_anousu";
    $sql .= "    and orcdotacao.o58_coddot = empempenho.e60_coddot";
    $sql .= "  inner join pctipocompra on";
    $sql .= "    pctipocompra.pc50_codcom = empempenho.e60_codcom";
    $sql .= "  inner join emptipo on";
    $sql .= "    emptipo.e41_codtipo = empempenho.e60_codtipo";
    $sql .= "  inner join concarpeculiar on";
    $sql .= "    concarpeculiar.c58_sequencial = empempenho.e60_concarpeculiar";
    $sql .= "  inner join pagordemnota on";
    $sql .= "    e71_codord = e50_codord";
    $sql .= "  inner join empnota on";
    $sql .= "    e71_codnota = e69_codnota";
    $sql .= "  inner join empord on";
    $sql .= "    e50_codord = e82_codord";
    $sql .= "  inner join empagemov on";
    $sql .= "    e82_codmov = e81_codmov";
    $sql .= "  inner join empageconf on";
    $sql .= "    e86_codmov = e81_codmov";
    $sql .= "  left join (select distinct on (k12_codmov) * from corempagemov order by k12_codmov, k12_autent) as corempagemov on ";
    $sql .= "    empagemov.e81_codmov = corempagemov.k12_codmov";
    $sql2 = "";
    if($dbwhere==""){
      if($e50_codord!=null ){
        $sql2 .= " where pagordem.e50_codord = $e50_codord ";
      }
    }else if($dbwhere != ""){
      $sql2 = " where $dbwhere";
    }
    $sql .= $sql2;
    if($ordem != null ){
      $sql .= " order by ";
      $campos_sql = split("#",$ordem);
      $virgula = "";
      for($i=0;$i<sizeof($campos_sql);$i++){
        $sql .= $virgula.$campos_sql[$i];
        $virgula = ",";
      }
    }
    return $sql;
  }

  function alteraContaFornecedor($e50_codord, $e50_contafornecedor){
    $sql = "UPDATE pagordem SET e50_contafornecedor = {$e50_contafornecedor} WHERE e50_codord = {$e50_codord}";
    $result = db_query($sql);

    if($result==false){
      $this->erro_banco = str_replace("\n","",@pg_last_error());
      $this->erro_sql   = $sql."Ordens de pagamento nao Alterado. Alteracao Abortada.\\n";
      $this->erro_sql .= "Valores : ".$e50_codord;
      $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
      $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
      $this->erro_status = "0";
      $this->numrows_alterar = 0;
      return false;
    }else{
      if(pg_affected_rows($result)==0){
        $this->erro_banco = "";
        $this->erro_sql = "Ordens de pagamento nao foi Alterado. Alteracao Executada.\\n";
        $this->erro_sql .= "Valores : ".$e50_codord;
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "0";
        $this->numrows_alterar = 0;
        return true;
      }else{
        $this->erro_banco = "";
        $this->erro_sql = $sql."Alteração efetuada com Sucesso\\n";
        $this->erro_sql .= "Valores : ".$e50_codord;
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "1";
        $this->numrows_alterar = pg_affected_rows($result);
        return true;
      }
    }
  }
}
?>