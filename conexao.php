<?php 
//Início da conexão com o BD utilizando PDO

$host = "localhost";
$user = "root";
$pass = "";
$dbname = "ceisc";
$port = 3306;

try {
    // Conexão com a porta
    //$conn = new PDO("mysql:host=$host;port=$port;dbname=" . $dbname, $user, $pass);

    //Conexão sem a porta
    $conn = new PDO("mysql:host=$host;dbname=" . $dbname, $user, $pass);
    
    // Configurar o fuso horário do MySQL para Brasília
    $conn->exec("SET time_zone = '-03:00'");
    
    // Configurar o charset para utf8mb4
    $conn->exec("SET NAMES utf8mb4");
    
    // Configurar o modo de SQL para ser mais restritivo
    $conn->exec("SET SESSION sql_mode = 'STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION'");
    
    //echo "Conexão com banco de dados realizado com sucesso.";
} catch (PDOException $err) {
    die("Erro: Conexão com banco de dados não realizado com sucesso. Erro gerado " . $err->getMessage());
}
    // Fim da conexão com o banco de dados utilizando PDO