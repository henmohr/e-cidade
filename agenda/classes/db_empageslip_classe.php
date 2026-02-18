<?php
//MODULO: empenho
//CLASSE DA ENTIDADE empageslip
class cl_empageslip { 
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
   var $e89_codmov = 0; 
   var $e89_codigo = 0; 
   // cria propriedade com as variaveis do arquivo 
   var $campos = "
                 e89_codmov = int4 = Movimento 
                 e89_codigo = int4 = Ordem 
                 ";
   //funcao construtor da classe 
   function cl_empageslip() { 
     //classes dos rotulos dos campos
     $this->rotulo = new rotulo("empageslip"); 
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
       $this->e89_codmov = ($this->e89_codmov == ""?@$_POST["e89_codmov"]:$this->e89_codmov);
       $this->e89_codigo = ($this->e89_codigo == ""?@$_POST["e89_codigo"]:$this->e89_codigo);
     }else{
       $this->e89_codmov = ($this->e89_codmov == ""?@$_POST["e89_codmov"]:$this->e89_codmov);
       $this->e89_codigo = ($this->e89_codigo == ""?@$_POST["e89_codigo"]:$this->e89_codigo);
     }
   }
   // funcao para inclusao
   function incluir ($e89_codmov,$e89_codigo){ 
      $this->atualizacampos();
       $this->e89_codmov = $e89_codmov; 
       $this->e89_codigo = $e89_codigo; 
     if(($this->e89_codmov == null) || ($this->e89_codmov == "") ){ 
       $this->erro_sql = " Campo e89_codmov nao declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if(($this->e89_codigo == null) || ($this->e89_codigo == "") ){ 
       $this->erro_sql = " Campo e89_codigo nao declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     $sql = "insert into empageslip(
                                       e89_codmov 
                                      ,e89_codigo 
                       )
                values (
                                $this->e89_codmov 
                               ,$this->e89_codigo 
                      )";
     $result = @pg_exec($sql); 
     if($result==false){ 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
         $this->erro_sql   = "Moviemto ordem ($this->e89_codmov."-".$this->e89_codigo) nao Incluído. Inclusao Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_banco = "Moviemto ordem já Cadastrado";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }else{
         $this->erro_sql   = "Moviemto ordem ($this->e89_codmov."-".$this->e89_codigo) nao Incluído. Inclusao Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }
       $this->erro_status = "0";
       $this->numrows_incluir= 0;
       return false;
     }
     $this->erro_banco = "";
     $this->erro_sql = "Inclusao efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$this->e89_codmov."-".$this->e89_codigo;
     $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
     $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
     $this->erro_status = "1";
     $this->numrows_incluir= pg_affected_rows($result);
     return true;
   } 
   // funcao para alteracao
   function alterar ($e89_codmov=null,$e89_codigo=null) { 
      $this->atualizacampos();
     $sql = " update empageslip set ";
     $virgula = "";
     if(trim($this->e89_codmov)!="" || isset($_POST["e89_codmov"])){ 
       $sql  .= $virgula." e89_codmov = $this->e89_codmov ";
       $virgula = ",";
       if(trim($this->e89_codmov) == null ){ 
         $this->erro_sql = " Campo Movimento nao Informado.";
         $this->erro_campo = "e89_codmov";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->e89_codigo)!="" || isset($_POST["e89_codigo"])){ 
       $sql  .= $virgula." e89_codigo = $this->e89_codigo ";
       $virgula = ",";
       if(trim($this->e89_codigo) == null ){ 
         $this->erro_sql = " Campo Ordem nao Informado.";
         $this->erro_campo = "e89_codigo";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     $sql .= " where ";
     if($e89_codmov!=null){
       $sql .= " e89_codmov = $this->e89_codmov";
     }
     if($e89_codigo!=null){
       $sql .= " and  e89_codigo = $this->e89_codigo";
     }
     $result = @pg_exec($sql);
     if($result==false){ 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Moviemto ordem nao Alterado. Alteracao Abortada.\\n";
         $this->erro_sql .= "Valores : ".$this->e89_codmov."-".$this->e89_codigo;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_alterar = 0;
       return false;
     }else{
       if(pg_affected_rows($result)==0){
         $this->erro_banco = "";
         $this->erro_sql = "Moviemto ordem nao foi Alterado. Alteracao Executada.\\n";
         $this->erro_sql .= "Valores : ".$this->e89_codmov."-".$this->e89_codigo;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = 0;
         return true;
       }else{
         $this->erro_banco = "";
         $this->erro_sql = "Alteração efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$this->e89_codmov."-".$this->e89_codigo;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = pg_affected_rows($result);
         return true;
       } 
     } 
   } 
   // funcao para exclusao 
   function excluir ($e89_codmov=null,$e89_codigo=null,$dbwhere=null) { 
     $sql = " delete from empageslip
                    where ";
     $sql2 = "";
     if($dbwhere==null || $dbwhere ==""){
        if($e89_codmov != ""){
          if($sql2!=""){
            $sql2 .= " and ";
          }
          $sql2 .= " e89_codmov = $e89_codmov ";
        }
        if($e89_codigo != ""){
          if($sql2!=""){
            $sql2 .= " and ";
          }
          $sql2 .= " e89_codigo = $e89_codigo ";
        }
     }else{
       $sql2 = $dbwhere;
     }
     $result = @pg_exec($sql.$sql2);
     if($result==false){ 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Moviemto ordem nao Excluído. Exclusão Abortada.\\n";
       $this->erro_sql .= "Valores : ".$e89_codmov."-".$e89_codigo;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_excluir = 0;
       return false;
     }else{
       if(pg_affected_rows($result)==0){
         $this->erro_banco = "";
         $this->erro_sql = "Moviemto ordem nao Encontrado. Exclusão não Efetuada.\\n";
         $this->erro_sql .= "Valores : ".$e89_codmov."-".$e89_codigo;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_excluir = 0;
         return true;
       }else{
         $this->erro_banco = "";
         $this->erro_sql = "Exclusão efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$e89_codmov."-".$e89_codigo;
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
        $this->erro_sql   = "Record Vazio na Tabela:empageslip";
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "0";
        return false;
      }
     return $result;
   }
   // funcao do sql 
   function sql_query_descr ( $e89_codmov=null,$e89_codigo=null,$campos="*",$ordem=null,$dbwhere=""){ 
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
     $sql .= " from empageslip ";
     $sql .= "      inner join empagemov  on  empagemov.e81_codmov = empageslip.e89_codmov";
     $sql .= "      inner join empage   b on   b.e80_codage = empagemov.e81_codage";
     $sql .= "      inner join empagepag   on   e85_codmov = empagemov.e81_codmov";
     $sql .= "      inner join empagetipo  on  empagetipo.e83_codtipo = empagepag.e85_codtipo";
     $sql .= "      inner join slip s on   e89_codigo = s.k17_codigo";
     $sql .= "      inner join emphist on s.k17_hist = e40_codhist";
     $sql .= "	    inner join conplanoreduz x on x.c61_reduz = s.k17_debito";
     $sql .= "	    inner join conplano z on z.c60_codcon = x.c61_codcon";
     $sql .= "	    left join slipnum o on o.k17_codigo = s.k17_codigo";
     $sql .= "	    left join cgm on z01_numcgm = o.k17_numcgm";
     $sql2 = "";
     if($dbwhere==""){
       if($e89_codmov!=null ){
         $sql2 .= " where empageslip.e89_codmov = $e89_codmov "; 
       } 
       if($e89_codigo!=null ){
         if($sql2!=""){
            $sql2 .= " and ";
         }else{
            $sql2 .= " where ";
         } 
         $sql2 .= " empageslip.e89_codigo = $e89_codigo "; 
       } 
     }else if($dbwhere != ""){
       $sql2 = " where $dbwhere";
     }
     $sql2 .= ($sql2!=""?" and ":" where ") . " k17_instit = " . db_getsession("DB_instit");
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
   function sql_query_file ( $e89_codmov=null,$e89_codigo=null,$campos="*",$ordem=null,$dbwhere=""){ 
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
     $sql .= " from empageslip ";
     $sql2 = "";
     if($dbwhere==""){
       if($e89_codmov!=null ){
         $sql2 .= " where empageslip.e89_codmov = $e89_codmov "; 
       } 
       if($e89_codigo!=null ){
         if($sql2!=""){
            $sql2 .= " and ";
         }else{
            $sql2 .= " where ";
         } 
         $sql2 .= " empageslip.e89_codigo = $e89_codigo "; 
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
   function sql_query_credito ( $conta=null,$campos="*",$ordem=null,$dbwhere=""){ 
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
     $sql .= " from empageslip ";
     $sql .= "      inner join empagemov  on  empagemov.e81_codmov = empageslip.e89_codmov";
     $sql .= "      inner join empage  as b on   b.e80_codage = empagemov.e81_codage";
     $sql .= "      inner join slip  on e89_codigo = slip.k17_codigo and slip.k17_autent is null";
     $sql .= " where slip.k17_credito = $conta";
     $sql .= " and k17_instit = " . db_getsession("DB_instit");
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

   function sql_query_conf ( $e89_codmov=null,$e89_codigo=null,$campos="*",$ordem=null,$dbwhere=""){ 
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
     $sql .= " from empageslip ";
     $sql .= "      inner join empagemov  on  empagemov.e81_codmov = empageslip.e89_codmov";
     $sql .= "      inner join empage   b on   b.e80_codage = empagemov.e81_codage";
     $sql .= "      inner join empageconf on   e86_codmov = e81_codmov";
     $sql .= "      inner join empagepag   on   e85_codmov = empagemov.e81_codmov";
     $sql .= "      inner join empagetipo  on  empagetipo.e83_codtipo = empagepag.e85_codtipo";
     $sql .= "      inner join slip s on   e89_codigo = s.k17_codigo";
     $sql .= "	    left join slipnum o on o.k17_codigo = s.k17_codigo";
     $sql .= "	    left join cgm on z01_numcgm = o.k17_numcgm";
     $sql2 = "";
     if($dbwhere==""){
       if($e89_codmov!=null ){
         $sql2 .= " where empageslip.e89_codmov = $e89_codmov "; 
       } 
       if($e89_codigo!=null ){
         if($sql2!=""){
            $sql2 .= " and ";
         }else{
            $sql2 .= " where ";
         } 
         $sql2 .= " empageslip.e89_codigo = $e89_codigo "; 
       } 
     }else if($dbwhere != ""){
       $sql2 = " where $dbwhere";
     }
     $sql2 .= ($sql2!=""?" and ":" where ") . " k17_instit = " . db_getsession("DB_instit");
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
