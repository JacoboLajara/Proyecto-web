<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <link rel="stylesheet" href="./CSS/estilos.css">
    <link rel="stylesheet" href="./CSS/estilohojacontacto.css">
</head>
<body>
<?php
        session_start();
       echo
       "<div>
            <form action='procesardatos.php' method='POST'>
                <fieldset>
                <legend> <span class='titulo'> - Introduzca sus datos personales - </span></legend>
                    <p>nombre: <input type='text' name='nombre'></p>
                    <p>password:<input type='password' name='password'></p>
                <fieldset>
                <input type='submit' name='enviar'>
            </form>
        </div>";
        
    ?>
    
</body>
</html>