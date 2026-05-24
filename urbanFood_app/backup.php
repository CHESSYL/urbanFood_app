<?php
session_start();

if($_SESSION['rol'] != 'admin'){
    die("Acceso denegado");
}

$host = "localhost";
$usuario = "root";
$password = "FantasyStrikeblue715X";
$bd = "urbanfood_app_db"; 

$fecha = date("Y-m-d_H-i-s");
$archivo = "backup_$fecha.sql";

$comando = "mysqldump --user=$usuario --password=$password --host=$host $bd > $archivo";

system($comando);

header('Content-Type: application/octet-stream');
header("Content-Disposition: attachment; filename=$archivo");
readfile($archivo);

unlink($archivo);
?>