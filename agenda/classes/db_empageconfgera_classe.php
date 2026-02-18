<?php
//MODULO: empenho
//CLASSE DA ENTIDADE empageconfgera
class cl_empageconfgera { 
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
   var $e90_codmov = 0; 
   var $e90_codgera = 0; 
   var $e90_correto = 'f'; 
   var $e90_dataret_dia = null; 
   var $e90_dataret_mes = null; 
   var $e90_dataret_ano = null; 
   var $e90_dataret = null; 
   var $e90_codret = null; 
   // cria propriedade com as variaveis do arquivo 
   var $campos = "
                 e90_codmov = int4 = Movimento 
                 e90_codgera = int4 = Código 
                 e90_correto = bool = Correto 
                 e90_dataret = date = Data retorno 
                 e90_codret = varchar(5) = Código de retorno 
                 ";
   //funcao construtor da classe 
   function cl_empageconfgera() { 
     //classes dos rotulos dos campos
     $this->rotulo = new rotulo("empageconfgera"); 
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
       $this->e90_codmov = ($this->e90_codmov == ""?@$_POST["e90_codmov"]:$this->e90_codmov);
       $this->e90_codgera = ($this->e90_codgera == ""?@$_POST["e90_codgera"]:$this->e90_codgera);
       $this->e90_correto = ($this->e90_correto == "f"?@$_POST["e90_correto"]:$this->e90_correto);
       if($this->e90_dataret == ""){
         $this->e90_dataret_dia = ($this->e90_dataret_dia == ""?@$_POST["e90_dataret_dia"]:$this->e90_dataret_dia);
         $this->e90_dataret_mes = ($this->e90_dataret_mes == ""?@$_POST["e90_dataret_mes"]:$this->e90_dataret_mes);
         $this->e90_dataret_ano = ($this->e90_dataret_ano == ""?@$_POST["e90_dataret_ano"]:$this->e90_dataret_ano);
         if($this->e90_dataret_dia != ""){
            $this->e90_dataret = $this->e90_dataret_ano."-".$this->e90_dataret_mes."-".$this->e90_dataret_dia;
         }
       }
       $this->e90_codret = ($this->e90_codret == ""?@$_POST["e90_codret"]:$this->e90_codret);
     }else{
       $this->e90_codmov = ($this->e90_codmov == ""?@$_POST["e90_codmov"]:$this->e90_codmov);
       $this->e90_codgera = ($this->e90_codgera == ""?@$_POST["e90_codgera"]:$this->e90_codgera);
     }
   }
   // funcao para inclusao
   function incluir ($e90_codmov,$e90_codgera){ 
      $this->atualizacampos();
     if($this->e90_correto == null ){ 
       $this->erro_sql = " Campo Correto nao Informado.";
       $this->erro_campo = "e90_correto";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->e90_dataret == null ){ 
       $this->e90_dataret = "null";
     }
       $this->e90_codmov = $e90_codmov; 
       $this->e90_codgera = $e90_codgera; 
     if(($this->e90_codmov == null) || ($this->e90_codmov == "") ){ 
       $this->erro_sql = " Campo e90_codmov nao declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if(($this->e90_codgera == null) || ($this->e90_codgera == "") ){ 
       $this->erro_sql = " Campo e90_codgera nao declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     $sql = "insert into empageconfgera(
                                       e90_codmov 
                                      ,e90_codgera 
                                      ,e90_correto 
                                      ,e90_dataret 
                                      ,e90_codret 
                       )
                values (
                                $this->e90_codmov 
                               ,$this->e90_codgera 
                               ,'$this->e90_correto' 
                               ,".($this->e90_dataret == "null" || $this->e90_dataret == ""?"null":"'".$this->e90_dataret."'")." 
                               ,'$this->e90_codret' 
                      )";
     $result = @pg_exec($sql); 
     if($result==false){ 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
         $this->erro_sql   = "empageconfgera ($this->e90_codmov."-".$this->e90_codgera) nao Incluído. Inclusao Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_banco = "empageconfgera já Cadastrado";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }else{
         $this->erro_sql   = "empageconfgera ($this->e90_codmov."-".$this->e90_codgera) nao Incluído. Inclusao Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }
       $this->erro_status = "0";
       $this->numrows_incluir= 0;
       return false;
     }
     $this->erro_banco = "";
     $this->erro_sql = "Inclusao efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$this->e90_codmov."-".$this->e90_codgera;
     $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
     $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
     $this->erro_status = "1";
     $this->numrows_incluir= pg_affected_rows($result);
     $resaco = $this->sql_record($this->sql_query_file($this->e90_codmov,$this->e90_codgera));
     if(($resaco!=false)||($this->numrows!=0)){
       $resac = pg_query("select nextval('db_acount_id_acount_seq') as acount");
       $acount = pg_result($resac,0,0);
       $resac = pg_query("insert into db_acountkey values($acount,6210,'$this->e90_codmov','I')");
       $resac = pg_query("insert into db_acountkey values($acount,6211,'$this->e90_codgera','I')");
       $resac = pg_query("insert into db_acount values($acount,1005,6210,'','".AddSlashes(pg_result($resaco,0,'e90_codmov'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = pg_query("insert into db_acount values($acount,1005,6211,'','".AddSlashes(pg_result($resaco,0,'e90_codgera'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = pg_query("insert into db_acount values($acount,1005,7233,'','".AddSlashes(pg_result($resaco,0,'e90_correto'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = pg_query("insert into db_acount values($acount,1005,7232,'','".AddSlashes(pg_result($resaco,0,'e90_dataret'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = pg_query("insert into db_acount values($acount,1005,7234,'','".AddSlashes(pg_result($resaco,0,'e90_codret'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
     }
     return true;
   } 
   // funcao para alteracao
   function alterar ($e90_codmov=null,$e90_codgera=null) { 
      $this->atualizacampos();
     $sql = " update empageconfgera set ";
     $virgula = "";
     if(trim($this->e90_codmov)!="" || isset($_POST["e90_codmov"])){ 
       $sql  .= $virgula." e90_codmov = $this->e90_codmov ";
       $virgula = ",";
       if(trim($this->e90_codmov) == null ){ 
         $this->erro_sql = " Campo Movimento nao Informado.";
         $this->erro_campo = "e90_codmov";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->e90_codgera)!="" || isset($_POST["e90_codgera"])){ 
       $sql  .= $virgula." e90_codgera = $this->e90_codgera ";
       $virgula = ",";
       if(trim($this->e90_codgera) == null ){ 
         $this->erro_sql = " Campo Código nao Informado.";
         $this->erro_campo = "e90_codgera";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->e90_correto)!="" || isset($_POST["e90_correto"])){ 
       $sql  .= $virgula." e90_correto = '$this->e90_correto' ";
       $virgula = ",";
       if(trim($this->e90_correto) == null ){ 
         $this->erro_sql = " Campo Correto nao Informado.";
         $this->erro_campo = "e90_correto";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->e90_dataret)!="" || isset($_POST["e90_dataret_dia"]) &&  ($_POST["e90_dataret_dia"] !="") ){ 
       $sql  .= $virgula." e90_dataret = '$this->e90_dataret' ";
       $virgula = ",";
     }     else{ 
       if(isset($_POST["e90_dataret_dia"])){ 
         $sql  .= $virgula." e90_dataret = null ";
         $virgula = ",";
       }
     }
     if(trim($this->e90_codret)!="" || isset($_POST["e90_codret"])){ 
       $sql  .= $virgula." e90_codret = '$this->e90_codret' ";
       $virgula = ",";
     }
     $sql .= " where ";
     if($e90_codmov!=null){
       $sql .= " e90_codmov = $this->e90_codmov";
     }
     if($e90_codgera!=null){
       $sql .= " and  e90_codgera = $this->e90_codgera";
     }
     $resaco = $this->sql_record($this->sql_query_file($this->e90_codmov,$this->e90_codgera));
     if($this->numrows>0){
       for($conresaco=0;$conresaco<$this->numrows;$conresaco++){
         $resac = pg_query("select nextval('db_acount_id_acount_seq') as acount");
         $acount = pg_result($resac,0,0);
         $resac = pg_query("insert into db_acountkey values($acount,6210,'$this->e90_codmov','A')");
         $resac = pg_query("insert into db_acountkey values($acount,6211,'$this->e90_codgera','A')");
         if(isset($_POST["e90_codmov"]))
           $resac = pg_query("insert into db_acount values($acount,1005,6210,'".AddSlashes(pg_result($resaco,$conresaco,'e90_codmov'))."','$this->e90_codmov',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($_POST["e90_codgera"]))
           $resac = pg_query("insert into db_acount values($acount,1005,6211,'".AddSlashes(pg_result($resaco,$conresaco,'e90_codgera'))."','$this->e90_codgera',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($_POST["e90_correto"]))
           $resac = pg_query("insert into db_acount values($acount,1005,7233,'".AddSlashes(pg_result($resaco,$conresaco,'e90_correto'))."','$this->e90_correto',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($_POST["e90_dataret"]))
           $resac = pg_query("insert into db_acount values($acount,1005,7232,'".AddSlashes(pg_result($resaco,$conresaco,'e90_dataret'))."','$this->e90_dataret',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($_POST["e90_codret"]))
           $resac = pg_query("insert into db_acount values($acount,1005,7234,'".AddSlashes(pg_result($resaco,$conresaco,'e90_codret'))."','$this->e90_codret',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       }
     }
     $result = @pg_exec($sql);
     if($result==false){ 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "empageconfgera nao Alterado. Alteracao Abortada.\\n";
         $this->erro_sql .= "Valores : ".$this->e90_codmov."-".$this->e90_codgera;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_alterar = 0;
       return false;
     }else{
       if(pg_affected_rows($result)==0){
         $this->erro_banco = "";
         $this->erro_sql = "empageconfgera nao foi Alterado. Alteracao Executada.\\n";
         $this->erro_sql .= "Valores : ".$this->e90_codmov."-".$this->e90_codgera;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = 0;
         return true;
       }else{
         $this->erro_banco = "";
         $this->erro_sql = "Alteração efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$this->e90_codmov."-".$this->e90_codgera;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = pg_affected_rows($result);
         return true;
       } 
     } 
   } 
   // funcao para exclusao 
   function excluir ($e90_codmov=null,$e90_codgera=null,$dbwhere=null) { 
     if($dbwhere==null || $dbwhere==""){
       $resaco = $this->sql_record($this->sql_query_file($e90_codmov,$e90_codgera));
     }else{ 
       $resaco = $this->sql_record($this->sql_query_file(null,null,"*",null,$dbwhere));
     }
     if(($resaco!=false)||($this->numrows!=0)){
       for($iresaco=0;$iresaco<$this->numrows;$iresaco++){
         $resac = pg_query("select nextval('db_acount_id_acount_seq') as acount");
         $acount = pg_result($resac,0,0);
         $resac = pg_query("insert into db_acountkey values($acount,6210,'$e90_codmov','E')");
         $resac = pg_query("insert into db_acountkey values($acount,6211,'$e90_codgera','E')");
         $resac = pg_query("insert into db_acount values($acount,1005,6210,'','".AddSlashes(pg_result($resaco,$iresaco,'e90_codmov'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = pg_query("insert into db_acount values($acount,1005,6211,'','".AddSlashes(pg_result($resaco,$iresaco,'e90_codgera'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = pg_query("insert into db_acount values($acount,1005,7233,'','".AddSlashes(pg_result($resaco,$iresaco,'e90_correto'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = pg_query("insert into db_acount values($acount,1005,7232,'','".AddSlashes(pg_result($resaco,$iresaco,'e90_dataret'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = pg_query("insert into db_acount values($acount,1005,7234,'','".AddSlashes(pg_result($resaco,$iresaco,'e90_codret'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       }
     }
     $sql = " delete from empageconfgera
                    where ";
     $sql2 = "";
     if($dbwhere==null || $dbwhere ==""){
        if($e90_codmov != ""){
          if($sql2!=""){
            $sql2 .= " and ";
          }
          $sql2 .= " e90_codmov = $e90_codmov ";
        }
        if($e90_codgera != ""){
          if($sql2!=""){
            $sql2 .= " and ";
          }
          $sql2 .= " e90_codgera = $e90_codgera ";
        }
     }else{
       $sql2 = $dbwhere;
     }
     $result = @pg_exec($sql.$sql2);
     if($result==false){ 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "empageconfgera nao Excluído. Exclusão Abortada.\\n";
       $this->erro_sql .= "Valores : ".$e90_codmov."-".$e90_codgera;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_excluir = 0;
       return false;
     }else{
       if(pg_affected_rows($result)==0){
         $this->erro_banco = "";
         $this->erro_sql = "empageconfgera nao Encontrado. Exclusão não Efetuada.\\n";
         $this->erro_sql .= "Valores : ".$e90_codmov."-".$e90_codgera;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_excluir = 0;
         return true;
       }else{
         $this->erro_banco = "";
         $this->erro_sql = "Exclusão efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$e90_codmov."-".$e90_codgera;
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
        $this->erro_sql   = "Record Vazio na Tabela:empageconfgera";
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "0";
        return false;
      }
     return $result;
   }
   // funcao do sql 
   function sql_query ( $e90_codmov=null,$e90_codgera=null,$campos="*",$ordem=null,$dbwhere=""){ 
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
     $sql .= " from empageconfgera ";
     $sql .= "      inner join empagemov  on  empagemov.e81_codmov = empageconfgera.e90_codmov";
     $sql .= "      inner join empagegera  on  empagegera.e87_codgera = empageconfgera.e90_codgera";
     $sql .= "      inner join empempenho  on  empempenho.e60_numemp = empagemov.e81_numemp";
     $sql .= "      inner join empage  on  empage.e80_codage = empagemov.e81_codage";
     $sql2 = "";
     if($dbwhere==""){
       if($e90_codmov!=null ){
         $sql2 .= " where empageconfgera.e90_codmov = $e90_codmov "; 
       } 
       if($e90_codgera!=null ){
         if($sql2!=""){
            $sql2 .= " and ";
         }else{
            $sql2 .= " where ";
         } 
         $sql2 .= " empageconfgera.e90_codgera = $e90_codgera "; 
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
   // funcao do sql 
   function sql_query_file ( $e90_codmov=null,$e90_codgera=null,$campos="*",$ordem=null,$dbwhere=""){ 
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
     $sql .= " from empageconfgera ";
     $sql2 = "";
     if($dbwhere==""){
       if($e90_codmov!=null ){
         $sql2 .= " where empageconfgera.e90_codmov = $e90_codmov "; 
       } 
       if($e90_codgera!=null ){
         if($sql2!=""){
            $sql2 .= " and ";
         }else{
            $sql2 .= " where ";
         } 
         $sql2 .= " empageconfgera.e90_codgera = $e90_codgera "; 
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
   function sql_query_inf ( $e90_codmov=null,$e90_codgera=null,$campos="*",$ordem=null,$dbwhere=""){
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
     $sql .= " from empageconfgera ";
     $sql .= "      inner join empagemov  on  empagemov.e81_codmov = empageconfgera.e90_codmov";
     $sql .= "      inner join empagegera  on  empagegera.e87_codgera = empageconfgera.e90_codgera";
     $sql .= "      inner join empempenho  on  empempenho.e60_numemp = empagemov.e81_numemp";
     $sql .= "      inner join empage  on  empage.e80_codage = empagemov.e81_codage";
     $sql .= "      inner join empord  on  empord.e82_codmov = empagemov.e81_codmov";
     $sql .= "      inner join cgm  on  cgm.z01_numcgm = empempenho.e60_numcgm";
     $sql2 = "";
     if($dbwhere==""){
       if($e90_codmov!=null ){
         $sql2 .= " where empageconfgera.e90_codmov = $e90_codmov ";
       }
       if($e90_codgera!=null ){
         if($sql2!=""){
            $sql2 .= " and ";
         }else{
            $sql2 .= " where ";
         }
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
