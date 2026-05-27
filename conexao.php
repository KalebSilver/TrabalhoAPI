<?php

$host = "localhost";
$port = "5432";
$dbname = "trabalho";
$user = "postgres";
$password = "1234";

$conn = pg_connect("
    host=$host
    port=$port
    dbname=$dbname
    user=$user
    password=$password
");

if (!$conn) {
    die("Erro na conexão com PostgreSQL");
}

?>