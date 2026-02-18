<?php
//MODULO: empenho
//CLASSE DA ENTIDADE empagemov
class cl_empagemov { 
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
   var $e81_codmov = 0; 
   var $e81_codage = 0; 
   var $e81_numemp = 0; 
   var $e81_valor = 0; 
   var $e81_cancelado_dia = null; 
   var $e81_cancelado_mes = null; 
   var $e81_cancelado_ano = null; 
   var $e81_cancelado = null; 
   // cria propriedade com as variaveis do arquivo 
   var $campos = "
                 e81_codmov = int4 = Movimento 
                 e81_codage = int4 = Agenda 
                 e81_numemp = int4 = Número 
                 e81_valor = float8 = Valor 
                 e81_cancelado = date = Data cancelado 
                 ";
   //funcao construtor da classe 
   function cl_empagemov() { 
     //classes dos rotulos dos campos
     $this->rotulo = new rotulo("empagemov"); 
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
       $this->e81_codmov = ($this->e81_codmov == ""?@$_POST["e81_codmov"]:$this->e81_codmov);
       $this->e81_codage = ($this->e81_codage == ""?@$_POST["e81_codage"]:$this->e81_codage);
       $this->e81_numemp = ($this->e81_numemp == ""?@$_POST["e81_numemp"]:$this->e81_numemp);
       $this->e81_valor = ($this->e81_valor == ""?@$_POST["e81_valor"]:$this->e81_valor);
       if($this->e81_cancelado == ""){
         $this->e81_cancelado_dia = ($this->e81_cancelado_dia == ""?@$_POST["e81_cancelado_dia"]:$this->e81_cancelado_dia);
         $this->e81_cancelado_mes = ($this->e81_cancelado_mes == ""?@$_POST["e81_cancelado_mes"]:$this->e81_cancelado_mes);
         $this->e81_cancelado_ano = ($this->e81_cancelado_ano == ""?@$_POST["e81_cancelado_ano"]:$this->e81_cancelado_ano);
         if($this->e81_cancelado_dia != ""){
            $this->e81_cancelado = $this->e81_cancelado_ano."-".$this->e81_cancelado_mes."-".$this->e81_cancelado_dia;
         }
       }
     }else{
       $this->e81_codmov = ($this->e81_codmov == ""?@$_POST["e81_codmov"]:$this->e81_codmov);
     }
   }
   // funcao para inclusao
   function incluir ($e81_codmov){ 
      $this->atualizacampos();
     if($this->e81_codage == null ){ 
       $this->erro_sql = " Campo Agenda nao Informado.";
       $this->erro_campo = "e81_codage";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->e81_numemp == null ){ 
       $this->erro_sql = " Campo Número nao Informado.";
       $this->erro_campo = "e81_numemp";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->e81_valor == null ){ 
       $this->erro_sql = " Campo Valor nao Informado.";
       $this->erro_campo = "e81_valor";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->e81_cancelado == null ){ 
       $this->e81_cancelado = "null";
     }
     if($e81_codmov == "" || $e81_codmov == null ){
       $result = pg_query("select nextval('empagemov_e81_codmov_seq')"); 
       if($result==false){
         $this->erro_banco = str_replace("\n","",@pg_last_error());
         $this->erro_sql   = "Verifique o cadastro da sequencia: empagemov_e81_codmov_seq do campo: e81_codmov"; 
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false; 
       }
       $this->e81_codmov = pg_result($result,0,0); 
     }else{
       $result = @pg_query("select last_value from empagemov_e81_codmov_seq");
       if(($result != false) && (pg_result($result,0,0) < $e81_codmov)){
         $this->erro_sql = " Campo e81_codmov maior que último número da sequencia.";
         $this->erro_banco = "Sequencia menor que este número.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }else{
         $this->e81_codmov = $e81_codmov; 
       }
     }
     if(($this->e81_codmov == null) || ($this->e81_codmov == "") ){ 
       $this->erro_sql = " Campo e81_codmov nao declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     $sql = "insert into empagemov(
                                       e81_codmov 
                                      ,e81_codage 
                                      ,e81_numemp 
                                      ,e81_valor 
                                      ,e81_cancelado 
                       )
                values (
                                $this->e81_codmov 
                               ,$this->e81_codage 
                               ,$this->e81_numemp 
                               ,$this->e81_valor 
                               ,".($this->e81_cancelado == "null" || $this->e81_cancelado == ""?"null":"'".$this->e81_cancelado."'")." 
                      )";
     $result = @pg_exec($sql); 
     if($result==false){ 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
         $this->erro_sql   = "Movimentos agenda ($this->e81_codmov) nao Incluído. Inclusao Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_banco = "Movimentos agenda já Cadastrado";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }else{
         $this->erro_sql   = "Movimentos agenda ($this->e81_codmov) nao Incluído. Inclusao Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }
       $this->erro_status = "0";
       $this->numrows_incluir= 0;
       return false;
     }
     $this->erro_banco = "";
     $this->erro_sql = "Inclusao efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$this->e81_codmov;
     $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
     $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
     $this->erro_status = "1";
     $this->numrows_incluir= pg_affected_rows($result);
     $resaco = $this->sql_record($this->sql_query_file($this->e81_codmov));
     if(($resaco!=false)||($this->numrows!=0)){
       $resac = pg_query("select nextval('db_acount_id_acount_seq') as acount");
       $acount = pg_result($resac,0,0);
       $resac = pg_query("insert into db_acountkey values($acount,6172,'$this->e81_codmov','I')");
       $resac = pg_query("insert into db_acount values($acount,995,6172,'','".AddSlashes(pg_result($resaco,0,'e81_codmov'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = pg_query("insert into db_acount values($acount,995,6173,'','".AddSlashes(pg_result($resaco,0,'e81_codage'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = pg_query("insert into db_acount values($acount,995,6174,'','".AddSlashes(pg_result($resaco,0,'e81_numemp'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = pg_query("insert into db_acount values($acount,995,6175,'','".AddSlashes(pg_result($resaco,0,'e81_valor'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = pg_query("insert into db_acount values($acount,995,6176,'','".AddSlashes(pg_result($resaco,0,'e81_cancelado'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
     }
     return true;
   } 
   // funcao para alteracao
   function alterar ($e81_codmov=null) { 
      $this->atualizacampos();
     $sql = " update empagemov set ";
     $virgula = "";
     if(trim($this->e81_codmov)!="" || isset($_POST["e81_codmov"])){ 
       $sql  .= $virgula." e81_codmov = $this->e81_codmov ";
       $virgula = ",";
       if(trim($this->e81_codmov) == null ){ 
         $this->erro_sql = " Campo Movimento nao Informado.";
         $this->erro_campo = "e81_codmov";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->e81_codage)!="" || isset($_POST["e81_codage"])){ 
       $sql  .= $virgula." e81_codage = $this->e81_codage ";
       $virgula = ",";
       if(trim($this->e81_codage) == null ){ 
         $this->erro_sql = " Campo Agenda nao Informado.";
         $this->erro_campo = "e81_codage";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->e81_numemp)!="" || isset($_POST["e81_numemp"])){ 
       $sql  .= $virgula." e81_numemp = $this->e81_numemp ";
       $virgula = ",";
       if(trim($this->e81_numemp) == null ){ 
         $this->erro_sql = " Campo Número nao Informado.";
         $this->erro_campo = "e81_numemp";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->e81_valor)!="" || isset($_POST["e81_valor"])){ 
       $sql  .= $virgula." e81_valor = $this->e81_valor ";
       $virgula = ",";
       if(trim($this->e81_valor) == null ){ 
         $this->erro_sql = " Campo Valor nao Informado.";
         $this->erro_campo = "e81_valor";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->e81_cancelado)!="" || isset($_POST["e81_cancelado_dia"]) &&  ($_POST["e81_cancelado_dia"] !="") ){ 
       $sql  .= $virgula." e81_cancelado = '$this->e81_cancelado' ";
       $virgula = ",";
     }     else{ 
       if(isset($_POST["e81_cancelado_dia"])){ 
         $sql  .= $virgula." e81_cancelado = null ";
         $virgula = ",";
       }
     }
     $sql .= " where ";
     if($e81_codmov!=null){
       $sql .= " e81_codmov = $this->e81_codmov";
     }
     $resaco = $this->sql_record($this->sql_query_file($this->e81_codmov));
     if($this->numrows>0){
       for($conresaco=0;$conresaco<$this->numrows;$conresaco++){
         $resac = pg_query("select nextval('db_acount_id_acount_seq') as acount");
         $acount = pg_result($resac,0,0);
         $resac = pg_query("insert into db_acountkey values($acount,6172,'$this->e81_codmov','A')");
         if(isset($_POST["e81_codmov"]))
           $resac = pg_query("insert into db_acount values($acount,995,6172,'".AddSlashes(pg_result($resaco,$conresaco,'e81_codmov'))."','$this->e81_codmov',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($_POST["e81_codage"]))
           $resac = pg_query("insert into db_acount values($acount,995,6173,'".AddSlashes(pg_result($resaco,$conresaco,'e81_codage'))."','$this->e81_codage',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($_POST["e81_numemp"]))
           $resac = pg_query("insert into db_acount values($acount,995,6174,'".AddSlashes(pg_result($resaco,$conresaco,'e81_numemp'))."','$this->e81_numemp',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($_POST["e81_valor"]))
           $resac = pg_query("insert into db_acount values($acount,995,6175,'".AddSlashes(pg_result($resaco,$conresaco,'e81_valor'))."','$this->e81_valor',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($_POST["e81_cancelado"]))
           $resac = pg_query("insert into db_acount values($acount,995,6176,'".AddSlashes(pg_result($resaco,$conresaco,'e81_cancelado'))."','$this->e81_cancelado',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       }
     }
     //echo "<BR><BR>$sql";
     $result = @pg_exec($sql);
     if($result==false){ 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Movimentos agenda nao Alterado. Alteracao Abortada.\\n";
         $this->erro_sql .= "Valores : ".$this->e81_codmov;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_alterar = 0;
       return false;
     }else{
       if(pg_affected_rows($result)==0){
         $this->erro_banco = "";
         $this->erro_sql = "Movimentos agenda nao foi Alterado. Alteracao Executada.\\n";
         $this->erro_sql .= "Valores : ".$this->e81_codmov;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = 0;
         return true;
       }else{
         $this->erro_banco = "";
         $this->erro_sql = "Alteração efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$this->e81_codmov;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = pg_affected_rows($result);
         return true;
       } 
     } 
   } 
   // funcao para exclusao 
   function excluir ($e81_codmov=null,$dbwhere=null) { 
     if($dbwhere==null || $dbwhere==""){
       $resaco = $this->sql_record($this->sql_query_file($e81_codmov));
     }else{ 
       $resaco = $this->sql_record($this->sql_query_file(null,"*",null,$dbwhere));
     }
     if(($resaco!=false)||($this->numrows!=0)){
       for($iresaco=0;$iresaco<$this->numrows;$iresaco++){
         $resac = pg_query("select nextval('db_acount_id_acount_seq') as acount");
         $acount = pg_result($resac,0,0);
         $resac = pg_query("insert into db_acountkey values($acount,6172,'$this->e81_codmov','E')");
         $resac = pg_query("insert into db_acount values($acount,995,6172,'','".AddSlashes(pg_result($resaco,$iresaco,'e81_codmov'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = pg_query("insert into db_acount values($acount,995,6173,'','".AddSlashes(pg_result($resaco,$iresaco,'e81_codage'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = pg_query("insert into db_acount values($acount,995,6174,'','".AddSlashes(pg_result($resaco,$iresaco,'e81_numemp'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = pg_query("insert into db_acount values($acount,995,6175,'','".AddSlashes(pg_result($resaco,$iresaco,'e81_valor'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = pg_query("insert into db_acount values($acount,995,6176,'','".AddSlashes(pg_result($resaco,$iresaco,'e81_cancelado'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       }
     }
     $sql = " delete from empagemov
                    where ";
     $sql2 = "";
     if($dbwhere==null || $dbwhere ==""){
        if($e81_codmov != ""){
          if($sql2!=""){
            $sql2 .= " and ";
          }
          $sql2 .= " e81_codmov = $e81_codmov ";
        }
     }else{
       $sql2 = $dbwhere;
     }
     $result = @pg_exec($sql.$sql2);
     if($result==false){ 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Movimentos agenda nao Excluído. Exclusão Abortada.\\n";
       $this->erro_sql .= "Valores : ".$e81_codmov;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_excluir = 0;
       return false;
     }else{
       if(pg_affected_rows($result)==0){
         $this->erro_banco = "";
         $this->erro_sql = "Movimentos agenda nao Encontrado. Exclusão não Efetuada.\\n";
         $this->erro_sql .= "Valores : ".$e81_codmov;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_excluir = 0;
         return true;
       }else{
         $this->erro_banco = "";
         $this->erro_sql = "Exclusão efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$e81_codmov;
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
     $result = @pg_query($sql);
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
        $this->erro_sql   = "Record Vazio na Tabela:empagemov";
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "0";
        return false;
      }
     return $result;
   }
   // funcao do sql 
   function sql_query ( $e81_codmov=null,$campos="*",$ordem=null,$dbwhere=""){ 
     $sql = "select ";
     if($campos != "*" ){
       $campos_sql = explode("#",$campos);
       $virgula = "";
       for($i=0;$i<sizeof($campos_sql);$i++){
         $sql .= $virgula.$campos_sql[$i];
         $virgula = ",";
       }
     }else{
       $sql .= $campos;
     }
     $sql .= " from empagemov ";
     $sql .= "      inner join empempenho  on  empempenho.e60_numemp = empagemov.e81_numemp";
     $sql .= "      inner join empage  on  empage.e80_codage = empagemov.e81_codage";
     $sql .= "      inner join cgm  on  cgm.z01_numcgm = empempenho.e60_numcgm";
     $sql .= "      inner join db_config  on  db_config.codigo = empempenho.e60_instit";
     $sql .= "      inner join orcdotacao  on  orcdotacao.o58_anousu = empempenho.e60_anousu and  orcdotacao.o58_coddot = empempenho.e60_coddot";
     $sql .= "      inner join pctipocompra  on  pctipocompra.pc50_codcom = empempenho.e60_codcom";
     $sql .= "      inner join emptipo  on  emptipo.e41_codtipo = empempenho.e60_codtipo";
     $sql2 = "";
     if($dbwhere==""){
       if($e81_codmov!=null ){
         $sql2 .= " where empagemov.e81_codmov = $e81_codmov "; 
       } 
     }else if($dbwhere != ""){
       $sql2 = " where $dbwhere";
     }
     $sql2 .= ($sql2!=""?"":" where db_config.codigo = " . db_getsession("DB_instit"));
     $sql .= $sql2;
     if($ordem != null ){
       $sql .= " order by ";
       $campos_sql = explode("#",$ordem);
       $virgula = "";
       for($i=0;$i<sizeof($campos_sql);$i++){
         $sql .= $virgula.$campos_sql[$i];
         $virgula = ",";
       }
     }
     return $sql;
  }
   // funcao do sql 
   function sql_query_file ( $e81_codmov=null,$campos="*",$ordem=null,$dbwhere=""){ 
     $sql = "select ";
     if($campos != "*" ){
       $campos_sql = explode("#",$campos);
       $virgula = "";
       for($i=0;$i<sizeof($campos_sql);$i++){
         $sql .= $virgula.$campos_sql[$i];
         $virgula = ",";
       }
     }else{
       $sql .= $campos;
     }
     $sql .= " from empagemov ";
     $sql2 = "";
     if($dbwhere==""){
       if($e81_codmov!=null ){
         $sql2 .= " where empagemov.e81_codmov = $e81_codmov "; 
       } 
     }else if($dbwhere != ""){
       $sql2 = " where $dbwhere";
     }
     $sql .= $sql2;
     if($ordem != null ){
       $sql .= " order by ";
       $campos_sql = explode("#",$ordem);
       $virgula = "";
       for($i=0;$i<sizeof($campos_sql);$i++){
         $sql .= $virgula.$campos_sql[$i];
         $virgula = ",";
       }
     }
     return $sql;
  }
   function sql_query_tipo ( $e81_codmov=null,$campos="*",$ordem=null,$dbwhere=""){
     $sql = "select ";
     if($campos != "*" ){
       $campos_sql = explode("#",$campos);
       $virgula = "";
       for($i=0;$i<sizeof($campos_sql);$i++){
         $sql .= $virgula.$campos_sql[$i];
         $virgula = ",";
       }
     }else{
       $sql .= $campos;
     }
     $sql .= " from empagemov ";
     $sql .= "      inner join empagepag  on  empagepag.e85_codmov = empagemov.e81_codmov";
     $sql .= "      inner join empagetipo  on  empagetipo.e83_codtipo = empagepag.e85_codtipo";
     $sql2 = "";
     if($dbwhere==""){
       if($e81_codmov!=null ){
         $sql2 .= " where empagemov.e81_codmov = $e81_codmov ";
       }
     }else if($dbwhere != ""){
       $sql2 = " where $dbwhere";
     }
     $sql .= $sql2;
     if($ordem != null ){
       $sql .= " order by ";
       $campos_sql = explode("#",$ordem);
       $virgula = "";
       for($i=0;$i<sizeof($campos_sql);$i++){
         $sql .= $virgula.$campos_sql[$i];
         $virgula = ",";
       }
     }
     return $sql;
  }
   function sql_query_emp ( $e81_codmov=null,$campos="*",$ordem=null,$dbwhere=""){
     $sql = "select ";
     if($campos != "*" ){
       $campos_sql = explode("#",$campos);
       $virgula = "";
       for($i=0;$i<sizeof($campos_sql);$i++){
         $sql .= $virgula.$campos_sql[$i];
         $virgula = ",";
       }
     }else{
       $sql .= $campos;
     }
     $sql .= " from empagemov ";
     $sql .= "      inner join empempenho  on  empempenho.e60_numemp = empagemov.e81_numemp";
     $sql .= "      inner join empage  on  empage.e80_codage = empagemov.e81_codage";
     $sql .= "      inner join empord  on  empord.e82_codmov = empagemov.e81_codmov";
     $sql .= "      inner join cgm  on  cgm.z01_numcgm = empempenho.e60_numcgm";
     $sql .= "      inner join empagepag  on  empagepag.e85_codmov = empagemov.e81_codmov";
     $sql .= "      inner join empagetipo  on  empagetipo.e83_codtipo = empagepag.e85_codtipo";
     $sql2 = "";
     if($dbwhere==""){
       if($e81_codmov!=null ){
         $sql2 .= " where empagemov.e81_codmov = $e81_codmov ";
       }
     }else if($dbwhere != ""){
       $sql2 = " where $dbwhere";
     }
     $sql .= $sql2;
     if($ordem != null ){
       $sql .= " order by ";
       $campos_sql = explode("#",$ordem);
       $virgula = "";
       for($i=0;$i<sizeof($campos_sql);$i++){
         $sql .= $virgula.$campos_sql[$i];
         $virgula = ",";
       }
     }
     return $sql;
  }
   function sql_query_ord ( $e81_codmov=null,$campos="*",$ordem=null,$dbwhere=""){
     $sql = "select ";
     if($campos != "*" ){
       $campos_sql = explode("#",$campos);
       $virgula = "";
       for($i=0;$i<sizeof($campos_sql);$i++){
         $sql .= $virgula.$campos_sql[$i];
         $virgula = ",";
       }
     }else{
       $sql .= $campos;
     }
     $sql .= " from empagemov ";
     $sql .= "      inner join empord  on  empord.e82_codmov = empagemov.e81_codmov";
     $sql2 = "";
     if($dbwhere==""){
       if($e81_codmov!=null ){
         $sql2 .= " where empagemov.e81_codmov = $e81_codmov ";
       }
     }else if($dbwhere != ""){
       $sql2 = " where $dbwhere";
     }
 $sql .= $sql2;
     if($ordem != null ){
       $sql .= " order by ";
       $campos_sql = explode("#",$ordem);
       $virgula = "";
       for($i=0;$i<sizeof($campos_sql);$i++){
         $sql .= $virgula.$campos_sql[$i];
         $virgula = ",";
       }
     }
     return $sql;
  }
   function sql_query_slip ( $e81_codmov=null,$campos="*",$ordem=null,$dbwhere=""){
     $sql = "select ";
     if($campos != "*" ){
       $campos_sql = explode("#",$campos);
       $virgula = "";
       for($i=0;$i<sizeof($campos_sql);$i++){
         $sql .= $virgula.$campos_sql[$i];
         $virgula = ",";
       }
     }else{
       $sql .= $campos;
     }
     $sql .= " from empagemov ";
     $sql .= "      inner join empageslip  on  empageslip.e89_codmov = empagemov.e81_codmov";
     $sql2 = "";
     if($dbwhere==""){
       if($e81_codmov!=null ){
         $sql2 .= " where empagemov.e81_codmov = $e81_codmov ";
       }
     }else if($dbwhere != ""){
       $sql2 = " where $dbwhere";
     }
 $sql .= $sql2;
     if($ordem != null ){
       $sql .= " order by ";
       $campos_sql = explode("#",$ordem);
       $virgula = "";
       for($i=0;$i<sizeof($campos_sql);$i++){
         $sql .= $virgula.$campos_sql[$i];
         $virgula = ",";
       }
     }
     return $sql;
  }
   function sql_query_txt ( $e81_codmov=null,$campos="*",$ordem=null,$dbwhere=""){
     $sql = "select ";
     if($campos != "*" ){
       $campos_sql = explode("#",$campos);
       $virgula = "";
       for($i=0;$i<sizeof($campos_sql);$i++){
         $sql .= $virgula.$campos_sql[$i];
         $virgula = ",";
       }
     }else{
       $sql .= $campos;
     }
     $sql .= " from empagemov ";
     $sql .= "      inner join empage  on  empage.e80_codage = empagemov.e81_codage ";
     $sql .= "      inner join empagepag  on  empagepag.e85_codmov = empagemov.e81_codmov ";
     $sql .= "      inner join empagetipo  on  empagetipo.e83_codtipo = empagepag.e85_codtipo ";
     $sql .= "      inner join conplanoreduz on conplanoreduz.c61_reduz = empagetipo.e83_conta ";
     $sql .= "      inner join conplanoconta on conplanoconta.c63_codcon = conplanoreduz.c61_codcon ";
     $sql .= "      inner join empageconf  on empageconf.e86_codmov = empagemov.e81_codmov ";
     $sql .= "      inner join empempenho  on  empempenho.e60_numemp = empagemov.e81_numemp ";
     $sql .= "      inner join orcdotacao on orcdotacao.o58_coddot = empempenho.e60_coddot  ";
     $sql .= "             and orcdotacao.o58_anousu = empempenho.e60_anousu ";
     $sql .= "      inner join orctiporec on orctiporec.o15_codigo = orcdotacao.o58_codigo ";
     $sql .= "      inner join empord  on  empord.e82_codmov = empagemov.e81_codmov ";
     $sql .= "      inner join pagordem on pagordem.e50_codord = empord.e82_codord ";
     $sql .= "      inner join cgm  on  cgm.z01_numcgm = empempenho.e60_numcgm ";
     $sql .= "      inner join empagemovconta on empagemovconta.e98_codmov = empagemov.e81_codmov ";
     $sql .= "      inner join pcfornecon on pcfornecon.pc63_contabanco = empagemovconta.e98_contabanco ";
     $sql .= "      left  join empageconfgera on empageconfgera.e90_codmov = empagemov.e81_codmov ";
     $sql .= "      inner join saltes  on  saltes.k13_conta = empagetipo.e83_conta ";
     $sql2 = "";
     if($dbwhere==""){
       if($e81_codmov!=null ){
         $sql2 .= " where empagemov.e81_codmov = $e81_codmov ";
       }
     }else if($dbwhere != ""){
       $sql2 = " where $dbwhere";
     }
     $sql .= $sql2;
     if($ordem != null ){
       $sql .= " order by ";
       $campos_sql = explode("#",$ordem);
       $virgula = "";
       for($i=0;$i<sizeof($campos_sql);$i++){
         $sql .= $virgula.$campos_sql[$i];
         $virgula = ",";
       }
     }
     return $sql;
  }
   function sql_query_lay ( $e81_codmov=null,$campos="*",$ordem=null,$dbwhere=""){
     $sql = "select ";
     if($campos != "*" ){
       $campos_sql = explode("#",$campos);
       $virgula = "";
       for($i=0;$i<sizeof($campos_sql);$i++){
         $sql .= $virgula.$campos_sql[$i];
         $virgula = ",";
       }
     }else{
       $sql .= $campos;
     }
     $sql .= " from empagemov ";
     $sql .= "      inner join empage  on  empage.e80_codage = empagemov.e81_codage";
     $sql .= "      inner join empagepag  on  empagepag.e85_codmov = empagemov.e81_codmov";
     $sql .= "      inner join empagetipo  on  empagetipo.e83_codtipo = empagepag.e85_codtipo";
     $sql .= "      inner join empageconf  on empageconf.e86_codmov = empagemov.e81_codmov";
     $sql .= "      inner join empempenho  on  empempenho.e60_numemp = empagemov.e81_numemp";
     $sql .= "      left  join empord  on  empord.e82_codmov = empagemov.e81_codmov";
     $sql .= "      inner join cgm  on  cgm.z01_numcgm = empempenho.e60_numcgm";
     $sql .= "      inner join saltes  on  saltes.k13_conta = empagetipo.e83_conta";
     $sql2 = "";
     if($dbwhere==""){
       if($e81_codmov!=null ){
         $sql2 .= " where empagemov.e81_codmov = $e81_codmov ";
       }
     }else if($dbwhere != ""){
       $sql2 = " where $dbwhere";
     }
     $sql .= $sql2;
     if($ordem != null ){
       $sql .= " order by ";
       $campos_sql = explode("#",$ordem);
       $virgula = "";
       for($i=0;$i<sizeof($campos_sql);$i++){
         $sql .= $virgula.$campos_sql[$i];
         $virgula = ",";
       }
     }
     return $sql;
  }
   function sql_query_conf ( $e81_codmov=null,$campos="*",$ordem=null,$dbwhere=""){
     $sql = "select ";
     if($campos != "*" ){
       $campos_sql = explode("#",$campos);
       $virgula = "";
       for($i=0;$i<sizeof($campos_sql);$i++){
         $sql .= $virgula.$campos_sql[$i];
         $virgula = ",";
       }
     }else{
       $sql .= $campos;
     }
     $sql .= " from empagemov ";
     $sql .= "      inner join empage  on  empage.e80_codage = empagemov.e81_codage";
     $sql .= "      inner join empagepag  on  empagepag.e85_codmov = empagemov.e81_codmov";
     $sql .= "      inner join empagetipo  on  empagetipo.e83_codtipo = empagepag.e85_codtipo";
     $sql .= "      inner join empageconf  on empageconf.e86_codmov = empagemov.e81_codmov";
     $sql .= "      inner join empageconfche  on  e91_codmov = empagemov.e81_codmov";
     $sql .= "      inner join empempenho  on  empempenho.e60_numemp = empagemov.e81_numemp";
     $sql .= "      left  join empord  on  empord.e82_codmov = empagemov.e81_codmov";
     $sql .= "      inner join cgm  on  cgm.z01_numcgm = empempenho.e60_numcgm";
     $sql .= "      inner join saltes  on  saltes.k13_conta = empagetipo.e83_conta";
     $sql2 = "";
     if($dbwhere==""){
       if($e81_codmov!=null ){
         $sql2 .= " where empagemov.e81_codmov = $e81_codmov ";
       }
     }else if($dbwhere != ""){
       $sql2 = " where $dbwhere";
     }
     $sql .= $sql2;
     if($ordem != null ){
       $sql .= " order by ";
       $campos_sql = explode("#",$ordem);
       $virgula = "";
       for($i=0;$i<sizeof($campos_sql);$i++){
         $sql .= $virgula.$campos_sql[$i];
         $virgula = ",";
       }
     }
     return $sql;
  }
   function sql_query_gera ( $e81_codmov=null,$campos="*",$ordem=null,$dbwhere=""){
     $sql = "select ";
     if($campos != "*" ){
       $campos_sql = explode("#",$campos);
       $virgula = "";
       for($i=0;$i<sizeof($campos_sql);$i++){
         $sql .= $virgula.$campos_sql[$i];
         $virgula = ",";
       }
     }else{
       $sql .= $campos;
     }
     $sql .= " from empagemov ";
     $sql .= "      inner join empage  		on empage.e80_codage = empagemov.e81_codage";
     $sql .= "      inner join empagepag  	on empagepag.e85_codmov = empagemov.e81_codmov";
     $sql .= "      inner join empagetipo  	on empagetipo.e83_codtipo = empagepag.e85_codtipo";
     $sql .= "      inner join empageconf  	on empageconf.e86_codmov = empagemov.e81_codmov";
     $sql .= "      inner join empempenho  	on empempenho.e60_numemp = empagemov.e81_numemp";
     $sql .= "      left  join empord  		on empord.e82_codmov = empagemov.e81_codmov";
     $sql .= "      inner join cgm  		on cgm.z01_numcgm = empempenho.e60_numcgm";
     $sql .= "      inner join saltes  		on saltes.k13_conta = empagetipo.e83_conta";
     $sql .= "      inner join empageconfgera	on empageconfgera.e90_codmov = empagemov.e81_codmov";
     $sql .= "      inner join empagegera	on empagegera.e87_codgera = empageconfgera.e90_codgera";
     $sql .= "      inner join pagordemele 	on pagordemele.e53_codord = empord.e82_codord";
     $sql2 = "";
     if($dbwhere==""){
       if($e81_codmov!=null ){
         $sql2 .= " where empagemov.e81_codmov = $e81_codmov ";
       }
     }else if($dbwhere != ""){
       $sql2 = " where $dbwhere";
     }
     $sql .= $sql2;
     if($ordem != null ){
       $sql .= " order by ";
       $campos_sql = explode("#",$ordem);
       $virgula = "";
       for($i=0;$i<sizeof($campos_sql);$i++){
         $sql .= $virgula.$campos_sql[$i];
         $virgula = ",";
       }
     }
     return $sql;
  }
}
?>
