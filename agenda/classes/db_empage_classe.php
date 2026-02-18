<?php
//MODULO: empenho
//CLASSE DA ENTIDADE empage
class cl_empage { 
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
   var $e80_codage = 0; 
   var $e80_data_dia = null; 
   var $e80_data_mes = null; 
   var $e80_data_ano = null; 
   var $e80_data = null; 
   var $e80_cancelado_dia = null; 
   var $e80_cancelado_mes = null; 
   var $e80_cancelado_ano = null; 
   var $e80_cancelado = null; 
   // cria propriedade com as variaveis do arquivo 
   var $campos = "
                 e80_codage = int4 = Agenda 
                 e80_data = date = Data 
                 e80_cancelado = date = Data cancelamento 
                 ";
   //funcao construtor da classe 
   function cl_empage() { 
     //classes dos rotulos dos campos
     $this->rotulo = new rotulo("empage"); 
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
       $this->e80_codage = ($this->e80_codage == ""?@$_POST["e80_codage"]:$this->e80_codage);
       if($this->e80_data == ""){
         $this->e80_data_dia = ($this->e80_data_dia == ""?@$_POST["e80_data_dia"]:$this->e80_data_dia);
         $this->e80_data_mes = ($this->e80_data_mes == ""?@$_POST["e80_data_mes"]:$this->e80_data_mes);
         $this->e80_data_ano = ($this->e80_data_ano == ""?@$_POST["e80_data_ano"]:$this->e80_data_ano);
         if($this->e80_data_dia != ""){
            $this->e80_data = $this->e80_data_ano."-".$this->e80_data_mes."-".$this->e80_data_dia;
         }
       }
       if($this->e80_cancelado == ""){
         $this->e80_cancelado_dia = ($this->e80_cancelado_dia == ""?@$_POST["e80_cancelado_dia"]:$this->e80_cancelado_dia);
         $this->e80_cancelado_mes = ($this->e80_cancelado_mes == ""?@$_POST["e80_cancelado_mes"]:$this->e80_cancelado_mes);
         $this->e80_cancelado_ano = ($this->e80_cancelado_ano == ""?@$_POST["e80_cancelado_ano"]:$this->e80_cancelado_ano);
         if($this->e80_cancelado_dia != ""){
            $this->e80_cancelado = $this->e80_cancelado_ano."-".$this->e80_cancelado_mes."-".$this->e80_cancelado_dia;
         }
       }
     }else{
       $this->e80_codage = ($this->e80_codage == ""?@$_POST["e80_codage"]:$this->e80_codage);
     }
   }
   // funcao para inclusao
   function incluir ($e80_codage){ 
      $this->atualizacampos();
     if($this->e80_data == null ){ 
       $this->erro_sql = " Campo Data nao Informado.";
       $this->erro_campo = "e80_data_dia";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->e80_cancelado == null ){ 
       $this->e80_cancelado = "null";
     }
     if($e80_codage == "" || $e80_codage == null ){
       $result = @pg_query("select nextval('empage_e80_codage_seq')"); 
       if($result==false){
         $this->erro_banco = str_replace("\n","",@pg_last_error());
         $this->erro_sql   = "Verifique o cadastro da sequencia: empage_e80_codage_seq do campo: e80_codage"; 
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false; 
       }
       $this->e80_codage = pg_result($result,0,0); 
     }else{
       $result = @pg_query("select last_value from empage_e80_codage_seq");
       if(($result != false) && (pg_result($result,0,0) < $e80_codage)){
         $this->erro_sql = " Campo e80_codage maior que último número da sequencia.";
         $this->erro_banco = "Sequencia menor que este número.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }else{
         $this->e80_codage = $e80_codage; 
       }
     }
     if(($this->e80_codage == null) || ($this->e80_codage == "") ){ 
       $this->erro_sql = " Campo e80_codage nao declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     $sql = "insert into empage(
                                       e80_codage 
                                      ,e80_data 
                                      ,e80_cancelado 
                       )
                values (
                                $this->e80_codage 
                               ,".($this->e80_data == "null" || $this->e80_data == ""?"null":"'".$this->e80_data."'")." 
                               ,".($this->e80_cancelado == "null" || $this->e80_cancelado == ""?"null":"'".$this->e80_cancelado."'")." 
                      )";
     $result = @pg_exec($sql); 
     if($result==false){ 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
         $this->erro_sql   = "Agenda ($this->e80_codage) nao Incluído. Inclusao Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_banco = "Agenda já Cadastrado";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }else{
         $this->erro_sql   = "Agenda ($this->e80_codage) nao Incluído. Inclusao Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }
       $this->erro_status = "0";
       $this->numrows_incluir= 0;
       return false;
     }
     $this->erro_banco = "";
     $this->erro_sql = "Inclusao efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$this->e80_codage;
     $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
     $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
     $this->erro_status = "1";
     $this->numrows_incluir= pg_affected_rows($result);
     $resaco = $this->sql_record($this->sql_query_file($this->e80_codage));
     if(($resaco!=false)||($this->numrows!=0)){
       $resac = pg_query("select nextval('db_acount_id_acount_seq') as acount");
       $acount = pg_result($resac,0,0);
       $resac = pg_query("insert into db_acountkey values($acount,6169,'$this->e80_codage','I')");
       $resac = pg_query("insert into db_acount values($acount,994,6169,'','".AddSlashes(pg_result($resaco,0,'e80_codage'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = pg_query("insert into db_acount values($acount,994,6170,'','".AddSlashes(pg_result($resaco,0,'e80_data'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = pg_query("insert into db_acount values($acount,994,6171,'','".AddSlashes(pg_result($resaco,0,'e80_cancelado'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
     }
     return true;
   } 
   // funcao para alteracao
   function alterar ($e80_codage=null) { 
      $this->atualizacampos();
     $sql = " update empage set ";
     $virgula = "";
     if(trim($this->e80_codage)!="" || isset($_POST["e80_codage"])){ 
       $sql  .= $virgula." e80_codage = $this->e80_codage ";
       $virgula = ",";
       if(trim($this->e80_codage) == null ){ 
         $this->erro_sql = " Campo Agenda nao Informado.";
         $this->erro_campo = "e80_codage";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->e80_data)!="" || isset($_POST["e80_data_dia"]) &&  ($_POST["e80_data_dia"] !="") ){ 
       $sql  .= $virgula." e80_data = '$this->e80_data' ";
       $virgula = ",";
       if(trim($this->e80_data) == null ){ 
         $this->erro_sql = " Campo Data nao Informado.";
         $this->erro_campo = "e80_data_dia";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }     else{ 
       if(isset($_POST["e80_data_dia"])){ 
         $sql  .= $virgula." e80_data = null ";
         $virgula = ",";
         if(trim($this->e80_data) == null ){ 
           $this->erro_sql = " Campo Data nao Informado.";
           $this->erro_campo = "e80_data_dia";
           $this->erro_banco = "";
           $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
           $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
           $this->erro_status = "0";
           return false;
         }
       }
     }
     if(trim($this->e80_cancelado)!="" || isset($_POST["e80_cancelado_dia"]) &&  ($_POST["e80_cancelado_dia"] !="") ){ 
       $sql  .= $virgula." e80_cancelado = '$this->e80_cancelado' ";
       $virgula = ",";
     }     else{ 
       if(isset($_POST["e80_cancelado_dia"])){ 
         $sql  .= $virgula." e80_cancelado = null ";
         $virgula = ",";
       }
     }
     $sql .= " where ";
     if($e80_codage!=null){
       $sql .= " e80_codage = $this->e80_codage";
     }
     $resaco = $this->sql_record($this->sql_query_file($this->e80_codage));
     if($this->numrows>0){
       for($conresaco=0;$conresaco<$this->numrows;$conresaco++){
         $resac = pg_query("select nextval('db_acount_id_acount_seq') as acount");
         $acount = pg_result($resac,0,0);
         $resac = pg_query("insert into db_acountkey values($acount,6169,'$this->e80_codage','A')");
         if(isset($_POST["e80_codage"]))
           $resac = pg_query("insert into db_acount values($acount,994,6169,'".AddSlashes(pg_result($resaco,$conresaco,'e80_codage'))."','$this->e80_codage',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($_POST["e80_data"]))
           $resac = pg_query("insert into db_acount values($acount,994,6170,'".AddSlashes(pg_result($resaco,$conresaco,'e80_data'))."','$this->e80_data',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($_POST["e80_cancelado"]))
           $resac = pg_query("insert into db_acount values($acount,994,6171,'".AddSlashes(pg_result($resaco,$conresaco,'e80_cancelado'))."','$this->e80_cancelado',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       }
     }
     $result = @pg_exec($sql);
     if($result==false){ 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Agenda nao Alterado. Alteracao Abortada.\\n";
         $this->erro_sql .= "Valores : ".$this->e80_codage;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_alterar = 0;
       return false;
     }else{
       if(pg_affected_rows($result)==0){
         $this->erro_banco = "";
         $this->erro_sql = "Agenda nao foi Alterado. Alteracao Executada.\\n";
         $this->erro_sql .= "Valores : ".$this->e80_codage;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = 0;
         return true;
       }else{
         $this->erro_banco = "";
         $this->erro_sql = "Alteração efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$this->e80_codage;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = pg_affected_rows($result);
         return true;
       } 
     } 
   } 
   // funcao para exclusao 
   function excluir ($e80_codage=null,$dbwhere=null) { 
     if($dbwhere==null || $dbwhere==""){
       $resaco = $this->sql_record($this->sql_query_file($e80_codage));
     }else{ 
       $resaco = $this->sql_record($this->sql_query_file(null,"*",null,$dbwhere));
     }
     if(($resaco!=false)||($this->numrows!=0)){
       for($iresaco=0;$iresaco<$this->numrows;$iresaco++){
         $resac = pg_query("select nextval('db_acount_id_acount_seq') as acount");
         $acount = pg_result($resac,0,0);
         $resac = pg_query("insert into db_acountkey values($acount,6169,'$this->e80_codage','E')");
         $resac = pg_query("insert into db_acount values($acount,994,6169,'','".AddSlashes(pg_result($resaco,$iresaco,'e80_codage'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = pg_query("insert into db_acount values($acount,994,6170,'','".AddSlashes(pg_result($resaco,$iresaco,'e80_data'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = pg_query("insert into db_acount values($acount,994,6171,'','".AddSlashes(pg_result($resaco,$iresaco,'e80_cancelado'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       }
     }
     $sql = " delete from empage
                    where ";
     $sql2 = "";
     if($dbwhere==null || $dbwhere ==""){
        if($e80_codage != ""){
          if($sql2!=""){
            $sql2 .= " and ";
          }
          $sql2 .= " e80_codage = $e80_codage ";
        }
     }else{
       $sql2 = $dbwhere;
     }
     $result = @pg_exec($sql.$sql2);
     if($result==false){ 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Agenda nao Excluído. Exclusão Abortada.\\n";
       $this->erro_sql .= "Valores : ".$e80_codage;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_excluir = 0;
       return false;
     }else{
       if(pg_affected_rows($result)==0){
         $this->erro_banco = "";
         $this->erro_sql = "Agenda nao Encontrado. Exclusão não Efetuada.\\n";
         $this->erro_sql .= "Valores : ".$e80_codage;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_excluir = 0;
         return true;
       }else{
         $this->erro_banco = "";
         $this->erro_sql = "Exclusão efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$e80_codage;
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
        $this->erro_sql   = "Record Vazio na Tabela:empage";
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "0";
        return false;
      }
     return $result;
   }
   // funcao do sql 
   function sql_query ( $e80_codage=null,$campos="*",$ordem=null,$dbwhere=""){ 
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
     $sql .= " from empage ";
     $sql2 = "";
     if($dbwhere==""){
       if($e80_codage!=null ){
         $sql2 .= " where empage.e80_codage = $e80_codage "; 
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
   function sql_query_file ( $e80_codage=null,$campos="*",$ordem=null,$dbwhere=""){ 
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
     $sql .= " from empage ";
     $sql2 = "";
     if($dbwhere==""){
       if($e80_codage!=null ){
         $sql2 .= " where empage.e80_codage = $e80_codage "; 
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
   function sql_query_cons ( $e80_codage=null,$campos="*",$ordem=null,$dbwhere=""){ 
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
     $sql .= "      left join empempenho  on  empempenho.e60_numemp = empagemov.e81_numemp";
     $sql .= "      left join cgm  on  cgm.z01_numcgm = empempenho.e60_numcgm";
     $sql .= "      left join empagepag  on  empagepag.e85_codmov = empagemov.e81_codmov";
     $sql .= "      left join empagetipo  on  empagetipo.e83_codtipo = empagepag.e85_codtipo";
     $sql .= "      left join saltes  on  saltes.k13_conta = empagetipo.e83_conta";
     $sql .= "      left join empageconf  on empageconf.e86_codmov = empagemov.e81_codmov";
     $sql .= "      left join empageconfche  on  e91_codmov = empagemov.e81_codmov";
     $sql .= "      left join empord  on  empord.e82_codmov = empagemov.e81_codmov";
     $sql .= "      left join empageslip  on  e89_codmov = empagemov.e81_codmov";
     $sql .= "      left join pagordemconta on e49_codord = e82_codord ";
     $sql .= "      left join cgm a on a.z01_numcgm = e49_numcgm ";
     
     $sql2 = "";
     if($dbwhere==""){
       if($e80_codage!=null ){
         $sql2 .= " where empage.e80_codage = $e80_codage "; 
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
