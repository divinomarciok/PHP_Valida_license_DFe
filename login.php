<?php

include('conexao.php');

if(isset($_POST['userlogin']) || isset($_POST['senha'])){

    if(strlen($_POST['userlogin']) == 0){
        echo "Preencha seu usuario";
    }else if (strlen($_POST['senha']) == 0){
        echo "Preencha sua senha";
    }else {
        
        $usuariologin = $mysqli->real_escape_string($_POST['userlogin']);
        $senha = $mysqli->real_escape_string($_POST['senha']);

        $sql_code = "SELECT * FROM usuario WHERE userlogin ='$usuariologin' AND senha = '$senha'";
        $sql_query = $mysqli -> query($sql_code) or die("Falha na execução do código SQL: ". $mysqli->error);


        $quantidade = $sql_query->num_rows;

        if($quantidade == 1 ){

                $usuario = $sql_query->fetch_assoc();

                if(!isset($_SESSION)){
                    session_start();
                }
                
                $_SESSION['id'] = $usuario['id'];
                $_SESSION['nome'] = $usuario['nome'];

                header("Location: index_hom.php");
 
        }{
            echo "Falha ao logar! Email ou senha incorretos";
        }

    }

}

?>

<DOCTYPE html>
<html>
    <head>
    <title>Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="utf-8">       
    <link href="\dashboard\stylesheets\style.css"  type="text/css" rel="stylesheet"/>
    </head>
    <body>
        <h2>Acesse sua conta</h2>
        <form action="" method="POST">
        <div id="textbox"> 
              <p>
              <input type="text" name="userlogin" placeholder="LOGIN">
              </p>        
             <p>
              <input type="password" id="senha" name="senha" placeholder="SENHA">
             </p>
            <p>
            <button type = "submit">Entrar</button>
            </p>
        <div>
    </body>
    


</html>