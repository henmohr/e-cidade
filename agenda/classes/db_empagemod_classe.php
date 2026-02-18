<?php
//MODULO: empenho
//CLASSE DA ENTIDADE empagemod
class cl_empagemod { 
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
   var $e84_codmod = 0; 
   var $e84_descr = null; 
   var $e84_layout = 0; 
   var $e84_sequencia = 0; 
   // cria propriedade com as variaveis do arquivo 
   var $campos = "
                 e84_codmod = int4 = Modelo 
                 e84_descr = text = Descrição 
                 e84_layout = int4 = Layout 
                 e84_sequencia = int4 = Sequência 
                 ";
   //funcao construtor da classe 
   function cl_empagemod() { 
     //classes dos rotulos dos campos
     $this->rotulo = new rotulo("empagemod"); 
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
       $this->e84_codmod = ($this->e84_codmod == ""?@$_POST["e84_codmod"]:$this->e84_codmod);
       $this->e84_descr = ($this->e84_descr == ""?@$_POST["e84_descr"]:$this->e84_descr);
       $this->e84_layout = ($this->e84_layout == ""?@$_POST["e84_layout"]:$this->e84_layout);
       $this->e84_sequencia = ($this->e84_sequencia == ""?@$_POST["e84_sequencia"]:$this->e84_sequencia);
     }else{
       $this->e84_codmod = ($this->e84_codmod == ""?@$_POST["e84_codmod"]:$this->e84_codmod);
     }
   }
   // funcao para inclusao
   function incluir ($e84_codmod){ 
      $this->atualizacampos();
     if($this->e84_descr == null ){ 
       $this->erro_sql = " Campo Descrição nao Informado.";
       $this->erro_campo = "e84_descr";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->e84_layout == null ){ 
       $this->erro_sql = " Campo Layout nao Informado.";
       $this->erro_campo = "e84_layout";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->e84_sequencia == null ){ 
       $this->erro_sql = " Campo Sequência nao Informado.";
       $this->erro_campo = "e84_sequencia";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($e84_codmod == "" || $e84_codmod == null ){
       $result = @pg_query("select nextval('empagemod_e84_codmod_seq')"); 
       if($result==false){
         $this->erro_banco = str_replace("\n","",@pg_last_error());
         $this->erro_sql   = "Verifique o cadastro da sequencia: empagemod_e84_codmod_seq do campo: e84_codmod"; 
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false; 
       }
       $this->e84_codmod = pg_result($result,0,0); 
     }else{
       $result = @pg_query("select last_value from empagemod_e84_codmod_seq");
       if(($result != false) && (pg_result($result,0,0) < $e84_codmod)){
         $this->erro_sql = " Campo e84_codmod maior que último número da sequencia.";
         $this->erro_banco = "Sequencia menor que este número.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }else{
         $this->e84_codmod = $e84_codmod; 
       }
     }
     if(($this->e84_codmod == null) || ($this->e84_codmod == "") ){ 
       $this->erro_sql = " Campo e84_codmod nao declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     $sql = "insert into empagemod(
                                       e84_codmod 
                                      ,e84_descr 
                                      ,e84_layout 
                                      ,e84_sequencia 
                       )
                values (
                                $this->e84_codmod 
                               ,'$this->e84_descr' 
                               ,$this->e84_layout 
                               ,$this->e84_sequencia 
                      )";
     $result = @pg_exec($sql); 
     if($result==false){ 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
         $this->erro_sql   = "Modelos layout agenda ($this->e84_codmod) nao Incluído. Inclusao Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_banco = "Modelos layout agenda já Cadastrado";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }else{
         $this->erro_sql   = "Modelos layout agenda ($this->e84_codmod) nao Incluído. Inclusao Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }
       $this->erro_status = "0";
       $this->numrows_incluir= 0;
       return false;
     }
     $this->erro_banco = "";
     $this->erro_sql = "Inclusao efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$this->e84_codmod;
     $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
     $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
     $this->erro_status = "1";
     $this->numrows_incluir= pg_affected_rows($result);
     $resaco = $this->sql_record($this->sql_query_file($this->e84_codmod));
     if(($resaco!=false)||($this->numrows!=0)){
       $resac = pg_query("select nextval('db_acount_id_acount_seq') as acount");
       $acount = pg_result($resac,0,0);
       $resac = pg_query("insert into db_acountkey values($acount,6183,'$this->e84_codmod','I')");
       $resac = pg_query("insert into db_acount values($acount,998,6183,'','".AddSlashes(pg_result($resaco,0,'e84_codmod'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = pg_query("insert into db_acount values($acount,998,6184,'','".AddSlashes(pg_result($resaco,0,'e84_descr'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = pg_query("insert into db_acount values($acount,998,6185,'','".AddSlashes(pg_result($resaco,0,'e84_layout'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = pg_query("insert into db_acount values($acount,998,6251,'','".AddSlashes(pg_result($resaco,0,'e84_sequencia'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
     }
     return true;
   } 
   // funcao para alteracao
   function alterar ($e84_codmod=null) { 
      $this->atualizacampos();
     $sql = " update empagemod set ";
     $virgula = "";
     if(trim($this->e84_codmod)!="" || isset($_POST["e84_codmod"])){ 
       $sql  .= $virgula." e84_codmod = $this->e84_codmod ";
       $virgula = ",";
       if(trim($this->e84_codmod) == null ){ 
         $this->erro_sql = " Campo Modelo nao Informado.";
         $this->erro_campo = "e84_codmod";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->e84_descr)!="" || isset($_POST["e84_descr"])){ 
       $sql  .= $virgula." e84_descr = '$this->e84_descr' ";
       $virgula = ",";
       if(trim($this->e84_descr) == null ){ 
         $this->erro_sql = " Campo Descrição nao Informado.";
         $this->erro_campo = "e84_descr";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->e84_layout)!="" || isset($_POST["e84_layout"])){ 
       $sql  .= $virgula." e84_layout = $this->e84_layout ";
       $virgula = ",";
       if(trim($this->e84_layout) == null ){ 
         $this->erro_sql = " Campo Layout nao Informado.";
         $this->erro_campo = "e84_layout";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->e84_sequencia)!="" || isset($_POST["e84_sequencia"])){ 
       $sql  .= $virgula." e84_sequencia = $this->e84_sequencia ";
       $virgula = ",";
       if(trim($this->e84_sequencia) == null ){ 
         $this->erro_sql = " Campo Sequência nao Informado.";
         $this->erro_campo = "e84_sequencia";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     $sql .= " where ";
     if($e84_codmod!=null){
       $sql .= " e84_codmod = $this->e84_codmod";
     }
     $resaco = $this->sql_record($this->sql_query_file($this->e84_codmod));
     if($this->numrows>0){
       for($conresaco=0;$conresaco<$this->numrows;$conresaco++){
         $resac = pg_query("select nextval('db_acount_id_acount_seq') as acount");
         $acount = pg_result($resac,0,0);
         $resac = pg_query("insert into db_acountkey values($acount,6183,'$this->e84_codmod','A')");
         if(isset($_POST["e84_codmod"]))
           $resac = pg_query("insert into db_acount values($acount,998,6183,'".AddSlashes(pg_result($resaco,$conresaco,'e84_codmod'))."','$this->e84_codmod',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($_POST["e84_descr"]))
           $resac = pg_query("insert into db_acount values($acount,998,6184,'".AddSlashes(pg_result($resaco,$conresaco,'e84_descr'))."','$this->e84_descr',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($_POST["e84_layout"]))
           $resac = pg_query("insert into db_acount values($acount,998,6185,'".AddSlashes(pg_result($resaco,$conresaco,'e84_layout'))."','$this->e84_layout',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($_POST["e84_sequencia"]))
           $resac = pg_query("insert into db_acount values($acount,998,6251,'".AddSlashes(pg_result($resaco,$conresaco,'e84_sequencia'))."','$this->e84_sequencia',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       }
     }
     $result = @pg_exec($sql);
     if($result==false){ 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Modelos layout agenda nao Alterado. Alteracao Abortada.\\n";
         $this->erro_sql .= "Valores : ".$this->e84_codmod;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_alterar = 0;
       return false;
     }else{
       if(pg_affected_rows($result)==0){
         $this->erro_banco = "";
         $this->erro_sql = "Modelos layout agenda nao foi Alterado. Alteracao Executada.\\n";
         $this->erro_sql .= "Valores : ".$this->e84_codmod;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = 0;
         return true;
       }else{
         $this->erro_banco = "";
         $this->erro_sql = "Alteração efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$this->e84_codmod;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = pg_affected_rows($result);
         return true;
       } 
     } 
   } 
   // funcao para exclusao 
   function excluir ($e84_codmod=null,$dbwhere=null) { 
     if($dbwhere==null || $dbwhere==""){
       $resaco = $this->sql_record($this->sql_query_file($e84_codmod));
     }else{ 
       $resaco = $this->sql_record($this->sql_query_file(null,"*",null,$dbwhere));
     }
     if(($resaco!=false)||($this->numrows!=0)){
       for($iresaco=0;$iresaco<$this->numrows;$iresaco++){
         $resac = pg_query("select nextval('db_acount_id_acount_seq') as acount");
         $acount = pg_result($resac,0,0);
         $resac = pg_query("insert into db_acountkey values($acount,6183,'$this->e84_codmod','E')");
         $resac = pg_query("insert into db_acount values($acount,998,6183,'','".AddSlashes(pg_result($resaco,$iresaco,'e84_codmod'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = pg_query("insert into db_acount values($acount,998,6184,'','".AddSlashes(pg_result($resaco,$iresaco,'e84_descr'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = pg_query("insert into db_acount values($acount,998,6185,'','".AddSlashes(pg_result($resaco,$iresaco,'e84_layout'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = pg_query("insert into db_acount values($acount,998,6251,'','".AddSlashes(pg_result($resaco,$iresaco,'e84_sequencia'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       }
     }
     $sql = " delete from empagemod
                    where ";
     $sql2 = "";
     if($dbwhere==null || $dbwhere ==""){
        if($e84_codmod != ""){
          if($sql2!=""){
            $sql2 .= " and ";
          }
          $sql2 .= " e84_codmod = $e84_codmod ";
        }
     }else{
       $sql2 = $dbwhere;
     }
     $result = @pg_exec($sql.$sql2);
     if($result==false){ 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Modelos layout agenda nao Excluído. Exclusão Abortada.\\n";
       $this->erro_sql .= "Valores : ".$e84_codmod;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_excluir = 0;
       return false;
     }else{
       if(pg_affected_rows($result)==0){
         $this->erro_banco = "";
         $this->erro_sql = "Modelos layout agenda nao Encontrado. Exclusão não Efetuada.\\n";
         $this->erro_sql .= "Valores : ".$e84_codmod;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_excluir = 0;
         return true;
       }else{
         $this->erro_banco = "";
         $this->erro_sql = "Exclusão efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$e84_codmod;
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
        $this->erro_sql   = "Record Vazio na Tabela:empagemod";
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "0";
        return false;
      }
     return $result;
   }
   // funcao do sql 
   function sql_query ( $e84_codmod=null,$campos="*",$ordem=null,$dbwhere=""){ 
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
     $sql .= " from empagemod ";
     $sql2 = "";
     if($dbwhere==""){
       if($e84_codmod!=null ){
         $sql2 .= " where empagemod.e84_codmod = $e84_codmod "; 
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
   function sql_query_file ( $e84_codmod=null,$campos="*",$ordem=null,$dbwhere=""){ 
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
     $sql .= " from empagemod ";
     $sql2 = "";
     if($dbwhere==""){
       if($e84_codmod!=null ){
         $sql2 .= " where empagemod.e84_codmod = $e84_codmod "; 
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
   function sql_query_modforma ( $e84_codmod=null,$campos="*",$ordem=null,$dbwhere=""){
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
     $sql .= "      inner join empagepag  on e85_codmov   = e81_codmov ";
     $sql .= "      inner join empagetipo on e85_codtipo  = e83_codtipo ";
     $sql .= "      inner join empagemod  on e84_codmod   = e83_codmod ";
     $sql .= "      inner join empagemovforma  on e97_codmov   = e81_codmov ";
     $sql .= "       left join empageconf  on e86_codmov   = e81_codmov ";
     $sql .= " 	    inner join conplanoreduz on c61_reduz = e83_conta ";
     $sql .= " 	    inner join conplanoconta on c63_codcon = c61_codcon ";
     $sql2 = "";
     if($dbwhere==""){
       if($e84_codmod!=null ){
         $sql2 .= " where empagemod.e84_codmod = $e84_codmod ";
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
   function sql_query_mod ( $e84_codmod=null,$campos="*",$ordem=null,$dbwhere=""){
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
     $sql .= "      inner join empagepag  on e85_codmov   = e81_codmov ";
     $sql .= "      inner join empagetipo on e85_codtipo  = e83_codtipo ";
     $sql .= "      inner join empagemod  on e84_codmod   = e83_codmod ";
     $sql2 = "";
     if($dbwhere==""){
       if($e84_codmod!=null ){
         $sql2 .= " where empagemod.e84_codmod = $e84_codmod ";
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
