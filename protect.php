<?php 

if(!isset ($_SESSION)){
    session_start();
}

if(!isset ($_SESSION['id'])){
    die("Sem acesso a essa pagina, não esta logado. <p><a href=\"login.php\">Entrar</a/p>");
}

?>