## Bienvenido

Ejemplo de Uso.

```
        <?php
   
        ///Se incluye la Clase
        include_once './msql.drive.inc.php';

             
        ///Se crea el objeto de la clase SQLConnect;
         $conn = new SQLConnect("localhost","1433","user","12345678","example");
         
         $sql="Select ISNULL(MAX(ID_LIBRO),0)+1 from LIBRO";
        //Funcion OneDato
         printf("<br>El ID ES: %s",$conn->OneDato($sql));
       
         $sql="Select * from LIBRO";
       //Funcion que Muestra los Datos obtenidos
         $conn->SqlShow($sql);
       
       //Funcion que ejecuta una sentencia que devuelve datos
         $resultset= $conn->ExecQuery($sql);

       //Funcion que regresa las filas obtenidas(Select)
            while ($row = $conn->SqlFetch($resultset)) {
                echo $row["ID_LIBRO"]."<br>";
                
            }

            //Cierra la conexion, para evitar bloquear otras coexiónes.
            $conn->Close();

        ?>
        ```
