<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
    </head>
    <body>
        <?php
   
        ///Se incluye la Clase
        include_once './msql.drive.inc.php';
        
        ///Se crea el objeto de la clase SQLConnect;
         $conn = new SQLConnect("localhost","1433","umg","12345678","EXAMEN");
         
         $sql="Select ISNULL(MAX(ID_LIBRO),0)+1 from LIBRO";
        //Funcion OneDato
       printf("<br>El ID ES: %s",$conn->OneDato($sql));
       
       $sql="Select * from LIBRO";
       //Funcion que Muestra los Datos obtenidos
       $conn->SqlShow($sql);
       
       //Funcion que ejecuta una sentencia que devuelve datos
       $resultset= $conn->ExecQuery($sql);

       //Funcion que procesa los datos obtenidos
       while ($row = $conn->SqlFetch($resultset)) {
           echo $row["ID_LIBRO"]."<br>";
           
       }
        $conn->Close();
        ?>
    </body>
</html>
