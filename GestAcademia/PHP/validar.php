<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
   
    <style>
        #capa1{
         float:left;
         height:150px;
         width:100%;
         background: rgb(218, 164, 126);
         font-size: 25px;
         text-align: center;
         text-decoration: underline;
         text-decoration-style: double;
         text-decoration-color: aquamarine;
         color:rgb(130, 30, 150);


     }
     #capa2{
         float:left;
         height:150px;
         width:100%;
         background: rgb(218, 164, 126);
         font-size: 25px;
         text-align: center;
         text-decoration: underline;
         text-decoration-style: double;
         text-decoration-color: aquamarine;
         color:rgb(148, 27, 100);


     }
     span{
         font-weight:bold;
     }
    </style>
</head>
<body>
    <?php
        $nombre=$_POST["name"];
        $direccion=$_POST["adress"];
        $poblacion=$_POST["village"];
        $provincia=$_POST["Provincia"];
        $codigopos=$_POST["cpostal"];
        $telefono=$_POST["Phone"];
        $mail=$_POST["mail"];
        $nacimiento=$_POST['fechanac'];
        $datoslista=$_POST['browser'];
        $nacimiento = date("d/m/Y", strtotime($nacimiento));
        
        //jAlert('This is a custom alert box', 'Alert Dialog');
        //if(isset($_POST['cpostal'])){ $Cp = $_POST['cpostal'];};
        //if(isset($_POST['fechanac'])) $nacimiento = $_POST['fechanac']; 
        
        if (empty($nombre)){
                echo "<script> alert('Debe de introducir un nombre');
                        location.href='formulario de contacto.html'
                </script>";
            }
        echo "<span>Nombre:</span> $nombre <br>";
        echo "<span>Dirección</span>: $direccion <br>";
        echo "<span>Poblacion:</span> $poblacion<br>
            <span>Provincia</span> $provincia <br>";
        echo "<span>Codigo postal:</span> $codigopos <br>";   
        echo "<span>Fecha de nacimiento:</span> $nacimiento <br>";    
        echo "<span>Has elegido: </span>".$datoslista;

         
        
    ?>
</body>
</html>