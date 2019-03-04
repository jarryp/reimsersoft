<?php
session_start();
 $_SESSION['cod_usu']="";
 session_destroy();
 header('Location:../index.php');
?>