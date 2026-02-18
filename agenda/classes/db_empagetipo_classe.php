<?php
//MODULO: empenho
//CLASSE DA ENTIDADE empagetipo
class cl_empagetipo { 
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
   var $e83_codtipo = 0; 
   var $e83_descr = null; 
   var $e83_conta = 0; 
   var $e83_codmod = 0; 
   var $e83_convenio = null; 
   var $e83_sequencia = 0; 
   // cria propriedade com as variaveis do arquivo 
   var $campos = "
                 e83_codtipo = int4 = Tipo 
                 e83_descr = varchar(60) = Descrição 
                 e83_conta = int4 = Código Conta 
                 e83_codmod = int4 = Modelo 
                 e83_convenio = varchar(10) = Convenio 
                 e83_sequencia = int4 = Seq. Cheque 
                 ";
   //funcao construtor da classe 
   function cl_empagetipo() { 
     //classes dos rotulos dos campos
     $this->rotulo = new rotulo("empagetipo"); 
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
       $this->e83_codtipo = ($this->e83_codtipo == ""?@$_POST["e83_codtipo"]:$this->e83_codtipo);
       $this->e83_descr = ($this->e83_descr == ""?@$_POST["e83_descr"]:$this->e83_descr);
       $this->e83_conta = ($this->e83_conta == ""?@$_POST["e83_conta"]:$this->e83_conta);
       $this->e83_codmod = ($this->e83_codmod == ""?@$_POST["e83_codmod"]:$this->e83_codmod);
       $this->e83_convenio = ($this->e83_convenio == ""?@$_POST["e83_convenio"]:$this->e83_convenio);
       $this->e83_sequencia = ($this->e83_sequencia == ""?@$_POST["e83_sequencia"]:$this->e83_sequencia);
     }else{
       $this->e83_codtipo = ($this->e83_codtipo == ""?@$_POST["e83_codtipo"]:$this->e83_codtipo);
     }
   }
   // funcao para inclusao
   function incluir ($e83_codtipo){ 
      $this->atualizacampos();
     if($this->e83_descr == null ){ 
       $this->erro_sql = " Campo Descrição nao Informado.";
       $this->erro_campo = "e83_descr";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->e83_conta == null ){ 
       $this->erro_sql = " Campo Código Conta nao Informado.";
       $this->erro_campo = "e83_conta";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->e83_codmod == null ){ 
       $this->erro_sql = " Campo Modelo nao Informado.";
       $this->erro_campo = "e83_codmod";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->e83_convenio == null ){ 
       $this->erro_sql = " Campo Convenio nao Informado.";
       $this->erro_campo = "e83_convenio";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->e83_sequencia == null ){ 
       $this->erro_sql = " Campo Seq. Cheque nao Informado.";
       $this->erro_campo = "e83_sequencia";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($e83_codtipo == "" || $e83_codtipo == null ){
       $result = @pg_query("select nextval('empagetipo_e83_codtipo_seq')"); 
       if($result==false){
         $this->erro_banco = str_replace("\n","",@pg_last_error());
         $this->erro_sql   = "Verifique o cadastro da sequencia: empagetipo_e83_codtipo_seq do campo: e83_codtipo"; 
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false; 
       }
       $this->e83_codtipo = pg_result($result,0,0); 
     }else{
       $result = @pg_query("select last_value from empagetipo_e83_codtipo_seq");
       if(($result != false) && (pg_result($result,0,0) < $e83_codtipo)){
         $this->erro_sql = " Campo e83_codtipo maior que último número da sequencia.";
         $this->erro_banco = "Sequencia menor que este número.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }else{
         $this->e83_codtipo = $e83_codtipo; 
       }
     }
     if(($this->e83_codtipo == null) || ($this->e83_codtipo == "") ){ 
       $this->erro_sql = " Campo e83_codtipo nao declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     $sql = "insert into empagetipo(
                                       e83_codtipo 
                                      ,e83_descr 
                                      ,e83_conta 
                                      ,e83_codmod 
                                      ,e83_convenio 
                                      ,e83_sequencia 
                       )
                values (
                                $this->e83_codtipo 
                               ,'$this->e83_descr' 
                               ,$this->e83_conta 
                               ,$this->e83_codmod 
                               ,'$this->e83_convenio' 
                               ,$this->e83_sequencia 
                      )";
     $result = @pg_exec($sql); 
     if($result==false){ 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
         $this->erro_sql   = "Tipo agenda ($this->e83_codtipo) nao Incluído. Inclusao Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_banco = "Tipo agenda já Cadastrado";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }else{
         $this->erro_sql   = "Tipo agenda ($this->e83_codtipo) nao Incluído. Inclusao Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }
       $this->erro_status = "0";
       $this->numrows_incluir= 0;
       return false;
     }
     $this->erro_banco = "";
     $this->erro_sql = "Inclusao efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$this->e83_codtipo;
     $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
     $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
     $this->erro_status = "1";
     $this->numrows_incluir= pg_affected_rows($result);
     $resaco = $this->sql_record($this->sql_query_file($this->e83_codtipo));
     if(($resaco!=false)||($this->numrows!=0)){
       $resac = pg_query("select nextval('db_acount_id_acount_seq') as acount");
       $acount = pg_result($resac,0,0);
       $resac = pg_query("insert into db_acountkey values($acount,6179,'$this->e83_codtipo','I')");
       $resac = pg_query("insert into db_acount values($acount,997,6179,'','".AddSlashes(pg_result($resaco,0,'e83_codtipo'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = pg_query("insert into db_acount values($acount,997,6180,'','".AddSlashes(pg_result($resaco,0,'e83_descr'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = pg_query("insert into db_acount values($acount,997,6181,'','".AddSlashes(pg_result($resaco,0,'e83_conta'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = pg_query("insert into db_acount values($acount,997,6182,'','".AddSlashes(pg_result($resaco,0,'e83_codmod'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = pg_query("insert into db_acount values($acount,997,6199,'','".AddSlashes(pg_result($resaco,0,'e83_convenio'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = pg_query("insert into db_acount values($acount,997,6200,'','".AddSlashes(pg_result($resaco,0,'e83_sequencia'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
     }
     return true;
   } 
   // funcao para alteracao
   function alterar ($e83_codtipo=null) { 
      $this->atualizacampos();
     $sql = " update empagetipo set ";
     $virgula = "";
     if(trim($this->e83_codtipo)!="" || isset($_POST["e83_codtipo"])){ 
       $sql  .= $virgula." e83_codtipo = $this->e83_codtipo ";
       $virgula = ",";
       if(trim($this->e83_codtipo) == null ){ 
         $this->erro_sql = " Campo Tipo nao Informado.";
         $this->erro_campo = "e83_codtipo";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->e83_descr)!="" || isset($_POST["e83_descr"])){ 
       $sql  .= $virgula." e83_descr = '$this->e83_descr' ";
       $virgula = ",";
       if(trim($this->e83_descr) == null ){ 
         $this->erro_sql = " Campo Descrição nao Informado.";
         $this->erro_campo = "e83_descr";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->e83_conta)!="" || isset($_POST["e83_conta"])){ 
       $sql  .= $virgula." e83_conta = $this->e83_conta ";
       $virgula = ",";
       if(trim($this->e83_conta) == null ){ 
         $this->erro_sql = " Campo Código Conta nao Informado.";
         $this->erro_campo = "e83_conta";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->e83_codmod)!="" || isset($_POST["e83_codmod"])){ 
       $sql  .= $virgula." e83_codmod = $this->e83_codmod ";
       $virgula = ",";
       if(trim($this->e83_codmod) == null ){ 
         $this->erro_sql = " Campo Modelo nao Informado.";
         $this->erro_campo = "e83_codmod";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->e83_convenio)!="" || isset($_POST["e83_convenio"])){ 
       $sql  .= $virgula." e83_convenio = '$this->e83_convenio' ";
       $virgula = ",";
       if(trim($this->e83_convenio) == null ){ 
         $this->erro_sql = " Campo Convenio nao Informado.";
         $this->erro_campo = "e83_convenio";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->e83_sequencia)!="" || isset($_POST["e83_sequencia"])){ 
       $sql  .= $virgula." e83_sequencia = $this->e83_sequencia ";
       $virgula = ",";
       if(trim($this->e83_sequencia) == null ){ 
         $this->erro_sql = " Campo Seq. Cheque nao Informado.";
         $this->erro_campo = "e83_sequencia";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     $sql .= " where ";
     if($e83_codtipo!=null){
       $sql .= " e83_codtipo = $this->e83_codtipo";
     }
     $resaco = $this->sql_record($this->sql_query_file($this->e83_codtipo));
     if($this->numrows>0){
       for($conresaco=0;$conresaco<$this->numrows;$conresaco++){
         $resac = pg_query("select nextval('db_acount_id_acount_seq') as acount");
         $acount = pg_result($resac,0,0);
         $resac = pg_query("insert into db_acountkey values($acount,6179,'$this->e83_codtipo','A')");
         if(isset($_POST["e83_codtipo"]))
           $resac = pg_query("insert into db_acount values($acount,997,6179,'".AddSlashes(pg_result($resaco,$conresaco,'e83_codtipo'))."','$this->e83_codtipo',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($_POST["e83_descr"]))
           $resac = pg_query("insert into db_acount values($acount,997,6180,'".AddSlashes(pg_result($resaco,$conresaco,'e83_descr'))."','$this->e83_descr',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($_POST["e83_conta"]))
           $resac = pg_query("insert into db_acount values($acount,997,6181,'".AddSlashes(pg_result($resaco,$conresaco,'e83_conta'))."','$this->e83_conta',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($_POST["e83_codmod"]))
           $resac = pg_query("insert into db_acount values($acount,997,6182,'".AddSlashes(pg_result($resaco,$conresaco,'e83_codmod'))."','$this->e83_codmod',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($_POST["e83_convenio"]))
           $resac = pg_query("insert into db_acount values($acount,997,6199,'".AddSlashes(pg_result($resaco,$conresaco,'e83_convenio'))."','$this->e83_convenio',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($_POST["e83_sequencia"]))
           $resac = pg_query("insert into db_acount values($acount,997,6200,'".AddSlashes(pg_result($resaco,$conresaco,'e83_sequencia'))."','$this->e83_sequencia',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       }
     }
     $result = @pg_exec($sql);
     if($result==false){ 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Tipo agenda nao Alterado. Alteracao Abortada.\\n";
         $this->erro_sql .= "Valores : ".$this->e83_codtipo;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_alterar = 0;
       return false;
     }else{
       if(pg_affected_rows($result)==0){
         $this->erro_banco = "";
         $this->erro_sql = "Tipo agenda nao foi Alterado. Alteracao Executada.\\n";
         $this->erro_sql .= "Valores : ".$this->e83_codtipo;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = 0;
         return true;
       }else{
         $this->erro_banco = "";
         $this->erro_sql = "Alteração efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$this->e83_codtipo;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = pg_affected_rows($result);
         return true;
       } 
     } 
   } 
   // funcao para exclusao 
   function excluir ($e83_codtipo=null,$dbwhere=null) { 
     if($dbwhere==null || $dbwhere==""){
       $resaco = $this->sql_record($this->sql_query_file($e83_codtipo));
     }else{ 
       $resaco = $this->sql_record($this->sql_query_file(null,"*",null,$dbwhere));
     }
     if(($resaco!=false)||($this->numrows!=0)){
       for($iresaco=0;$iresaco<$this->numrows;$iresaco++){
         $resac = pg_query("select nextval('db_acount_id_acount_seq') as acount");
         $acount = pg_result($resac,0,0);
         $resac = pg_query("insert into db_acountkey values($acount,6179,'$this->e83_codtipo','E')");
         $resac = pg_query("insert into db_acount values($acount,997,6179,'','".AddSlashes(pg_result($resaco,$iresaco,'e83_codtipo'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = pg_query("insert into db_acount values($acount,997,6180,'','".AddSlashes(pg_result($resaco,$iresaco,'e83_descr'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = pg_query("insert into db_acount values($acount,997,6181,'','".AddSlashes(pg_result($resaco,$iresaco,'e83_conta'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = pg_query("insert into db_acount values($acount,997,6182,'','".AddSlashes(pg_result($resaco,$iresaco,'e83_codmod'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = pg_query("insert into db_acount values($acount,997,6199,'','".AddSlashes(pg_result($resaco,$iresaco,'e83_convenio'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = pg_query("insert into db_acount values($acount,997,6200,'','".AddSlashes(pg_result($resaco,$iresaco,'e83_sequencia'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       }
     }
     $sql = " delete from empagetipo
                    where ";
     $sql2 = "";
     if($dbwhere==null || $dbwhere ==""){
        if($e83_codtipo != ""){
          if($sql2!=""){
            $sql2 .= " and ";
          }
          $sql2 .= " e83_codtipo = $e83_codtipo ";
        }
     }else{
       $sql2 = $dbwhere;
     }
     $result = @pg_exec($sql.$sql2);
     if($result==false){ 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Tipo agenda nao Excluído. Exclusão Abortada.\\n";
       $this->erro_sql .= "Valores : ".$e83_codtipo;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_excluir = 0;
       return false;
     }else{
       if(pg_affected_rows($result)==0){
         $this->erro_banco = "";
         $this->erro_sql = "Tipo agenda nao Encontrado. Exclusão não Efetuada.\\n";
         $this->erro_sql .= "Valores : ".$e83_codtipo;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_excluir = 0;
         return true;
       }else{
         $this->erro_banco = "";
         $this->erro_sql = "Exclusão efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$e83_codtipo;
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
        $this->erro_sql   = "Record Vazio na Tabela:empagetipo";
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "0";
        return false;
      }
     return $result;
   }
   // funcao do sql 
   function sql_query ( $e83_codtipo=null,$campos="*",$ordem=null,$dbwhere=""){ 
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
     $sql .= " from empagetipo ";
     $sql .= "      inner join saltes  on  saltes.k13_conta = empagetipo.e83_conta";
     $sql .= "      inner join empagemod  on  empagemod.e84_codmod = empagetipo.e83_codmod";
     $sql2 = "";
     if($dbwhere==""){
       if($e83_codtipo!=null ){
         $sql2 .= " where empagetipo.e83_codtipo = $e83_codtipo "; 
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
   function sql_query_file ( $e83_codtipo=null,$campos="*",$ordem=null,$dbwhere=""){ 
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
     $sql .= " from empagetipo ";
     $sql2 = "";
     if($dbwhere==""){
       if($e83_codtipo!=null ){
         $sql2 .= " where empagetipo.e83_codtipo = $e83_codtipo "; 
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
   function sql_query_emprec ( $e83_codtipo=null,$campos="*",$ordem=null,$dbwhere="",$dbwhere02=""){ 
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
     $sql .= " from empagetipo ";
     $sql .= " 	    inner join conplanoreduz on e83_conta = c61_reduz ";
     $sql .= " 	    inner join (
                                   select o58_codigo,e60_numemp from empempenho";
     $sql .= "   		   inner join orcdotacao  on  orcdotacao.o58_anousu = empempenho.e60_anousu and orcdotacao.o58_coddot = empempenho.e60_coddot";
     $sql .= "   		   left join  pagordem on e50_numemp = e60_numemp ";

     if($dbwhere02 != ""){
       $sql .= " where $dbwhere02";
     }
     $sql .= "   		   group by e60_numemp,o58_codigo"; 
     $sql .= "   		)as x on x.o58_codigo = c61_codigo      ";
     $sql2 = "";
     if($dbwhere==""){
       if($e83_codtipo!=null ){
         $sql2 .= " where empagetipo.e83_codtipo = $e83_codtipo "; 
       } 
     }else if($dbwhere != ""){
       $sql2 = " where $dbwhere";
     }
     $sql2 .= ($sql2!=""?" and ":" where ") . " c61_instit = " . db_getsession("DB_instit");
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
   function sql_query_rec ( $e83_codtipo=null,$campos="*",$ordem=null,$dbwhere=""){ 
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
     $sql .= " from empagetipo ";
     $sql .= " 	    inner join conplanoreduz on e83_conta = c61_reduz ";
     $sql .= " 	    inner join conplanoconta on c63_codcon = c61_codcon ";
     $sql2 = "";
     if($dbwhere==""){
       if($e83_codtipo!=null ){
         $sql2 .= " where empagetipo.e83_codtipo = $e83_codtipo "; 
       } 
     }else if($dbwhere != ""){
       $sql2 = " where $dbwhere";
     }
     $sql2 .= ($sql2!=""?" and ":" where ") . " c61_instit = " . db_getsession("DB_instit");
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
