<?php 
/*
Licencia MIT

Copyright 2019 	ASz <asznet66@gmail.com>

Permission is hereby granted, free of charge, to any person obtaining a copy of this software 
and associated documentation files (the "Software"), to deal in the Software without restriction, 
including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, 
and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, 
subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or 
substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, 
INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE 
AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, 
DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, 
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.

*/

/*
 * 
 * Conexion: SQL Server
 * Driver: sql_srv
 * Autor:   ASz <asznet66@gmail.com>
 * 
 */

/*
* No hay necesidad de abrir la conexion.
* Cuando se crea la instancia del objeto, el construnctor lo hace    
* Obligatorio hacer la llamada al metódo Cerrar() al finalizar..   
*/




class SQLConnect{
    
//Valores default;
private $CONFIG=array(
    "HOST"          =>      "localhost", //Establece el nombre de la Instacia Sql
    "PORT"          =>      "1433", // Establece el Puerto del Servidor
    "USERDB"        =>      "sa", //Establece el Usuario del Servidor
    "PASSWORD"      =>      "12345678", // Establece la Clave del Servidor
    "DATABASE"      =>      "master", // Establece la base de datos;
    "DEBUG"         =>      false // Muestra cada sentencia Ejecutada
);

private $Conexion=null;

//inicia las variables
function __construct($HOST,$PORT,$USERDB,$PASSWORD,$DATABASE,$DEBUG=false) {

    $this->CONFIG["HOST"] = $HOST;
    $this->CONFIG["PORT"]=$PORT;
    $this->CONFIG["USERDB"]=$USERDB;
    $this->CONFIG["PASSWORD"]=$PASSWORD;
    $this->CONFIG["DATABASE"]=$DATABASE;
    $this->CONFIG["DEBUG"]=$DEBUG;
    
    $this->Conexion = $this->Open();
}

//Muestra los Query ejectados en Modo Debug
private function PrintMsg($salida=""){
$out="<div style='background: #eee; color:#000; with:100%; height: 30px; font-size: 12px;'>";
$out.="<p>";
$out.= $salida;
$out.="</p>";
$out.="</div>";
print($out);
}

//Abre la conexion y se asigna a la variable
private function Open(){

 $conndb = array( "Database"=>$this->CONFIG["DATABASE"], "UID"=>$this->CONFIG["USERDB"], "PWD"=>$this->CONFIG["PASSWORD"]);
 $conn = sqlsrv_connect($this->CONFIG["HOST"],$conndb);

    if($conn ) {
                    if ($this->CONFIG["DEBUG"] == true) {
                        $this->PrintMsg("Conexión establecida.");
                    }
    }else{
                    if ($this->CONFIG["DEBUG"] == true) {
                        $this->PrintMsg("La Conexión no se pudo establecer.");
                    }
                    die( print_r(sqlsrv_errors($this->Conexion), true));
    }
    return $conn;
}

 // Inicia una transaccion
function Begin(){

    if (sqlsrv_begin_transaction($this->Conexion) === false) 
    {
        die( print_r(sqlsrv_errors($this->Conexion), true ));
    }
    
}

 //Finaliza la Transaccion
function Commit()
{
    if(sqlsrv_commit($this->Conexion) === false)
    {
        if(sqlsrv_rollback($this->Conexion)==false){
            die( print_r(sqlsrv_errors($this->Conexion), true ));
        }
    }
}

//DESCONECTA DE LA BASE
function Close(){
   $close = sqlsrv_close($this->Conexion);
if($close) {
}else{
    if ($this->CONFIG["DEBUG"] == true) {
         $this->PrintMsg("La Conexión no se pudo Cerrar.");
     }
    die( print_r( sqlsrv_errors($this->Conexion), true));
}
}

// Ejecuta un query que no devuelve datos;
function ExecSql($query,$resultsert=false){
    if ($this->CONFIG["DEBUG"] == true) {
                $this->PrintMsg($query);
            }
$sql = sqlsrv_prepare($this->Conexion, $query);
if( !$sql ) {
    die(print_r(sqlsrv_errors($this->Conexion), true));

} 
    if($resultsert === true){
$ret =sqlsrv_execute($sql);
    if($ret === false) {
          die( print_r(sqlsrv_errors($this->Conexion), true));
    } 
    
return $ret;
    
}else{
    
    if(sqlsrv_execute($sql) === false ) {
          die( print_r(sqlsrv_errors($this->Conexion), true));
    }  
}

}


//DEVUELVE UN SOLO DATO

 function OneDato($query){ 
     if ($this->CONFIG["DEBUG"] == true) {
                $this->PrintMsg($query);
            }
            $result = sqlsrv_query($this->Conexion, $query);
if($result === false ) {
     die( print_r(sqlsrv_errors($this->Conexion), true));
}else{
 
    if(sqlsrv_fetch($result) === false) {
     die(print_r(sqlsrv_errors($this->Conexion), true));
    }
      $datos = sqlsrv_get_field($result, 0);   
}
// Liberar resultados
sqlsrv_free_stmt($result);
if(isset($datos)){ return $datos; }
else{ return "-1"; }

}

//Cancela una transaccion
 function Rollback() {
    if(sqlsrv_rollback($this->Conexion)== false){
          die(print_r(sqlsrv_errors($this->Conexion), true));
    }
 }

 /// Devuelve linie por linea los resultados;
 // FETCH_ASSOC
function SqlFetch($result){
   return sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC);
        
}

//Libera un resultset
function SqlFree($result){
        sqlsrv_free_stmt($result); 
}

//Devuelve el numero de filas afectadas
function SqlNumRows($res){
   return  sqlsrv_num_rows($res);
}
   
//Muestra el resultado de un select
function SqlShow($query){
    if ($this->CONFIG["DEBUG"] == true) {
                $this->PrintMsg($query);
            }
$sql = sqlsrv_query($this->Conexion, $query);
if( $sql === false) {
   die( print_r(sqlsrv_errors($this->Conexion), true));
}

$numFields = sqlsrv_num_fields($sql);

while( sqlsrv_fetch($sql)) {
   // for para obtener los datos
    echo "<table border='1' style='margin: 0px; padding:opx;'><tr style='margin: 0px; padding:opx;'>";
   for($i = 0; $i < $numFields; $i++) {
       echo "<td style='margin: 0px; padding:opx;'>";
     echo sqlsrv_get_field($sql, $i,SQLSRV_PHPTYPE_STRING("UTF-8"))." ";
            echo "</td>";

   }
   echo "</tr></table>";
}

sqlsrv_free_stmt($sql);   
    
}
    
 //ResultSet//Ejecuta un sentencia que devuelve datos;
function ExecQuery($query){
    if ($this->CONFIG["DEBUG"] === true) {
                $this->PrintMsg($query);
            }
            $sql = sqlsrv_query($this->Conexion, $query);
if( $sql === false) {
   die( print_r(sqlsrv_errors($this->Conexion), true));
}

return $sql;
sqlsrv_free_stmt($sql);   
    
}

//Si la Ejecucion da Error
function Error(){
    $error = false;
 if( ($errors = sqlsrv_errors($this->Conexion) ) != null) {
        foreach( $errors as $error ) {
            echo "SQLSTATE: ".$error[ 'SQLSTATE']."<br />";
            echo "code: ".$error[ 'code']."<br />";
            echo "message: ".$error[ 'message']."<br />";
            $error = true;
        }   
 }
    return $error;
}



}


?> 