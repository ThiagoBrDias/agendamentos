<?php

// Incluir o arquivo com a conexão com banco de dados
include_once './conexao.php';

// QUERY para recuperar as salas
$query_users = "SELECT id, sala FROM users ORDER BY sala ASC";
                

// Prepara a QUERY
$result_users = $conn->prepare($query_users);

// Executar a QUERY
$result_users->execute();

// Criar o array que recebe os eventos
$eventos = [];

// Acessar o IF quando encontrar usuário no BD
if(($result_users) and ($result_users->rowCount() != 0)){

    //Ler os registros recuperdoS do BD
    $dados = $result_users->fetchAll(PDO::FETCH_ASSOC);

    //Criar o array com o status e os dados
    $retorna = ['status' => true, 'dados' => $dados];
    
} else {
    
    //Criar o array em objeto e retornar para o JS
    $retorna = ['status' => false, 'msg' => 'Nenhuma sala encontrada!'];
}

echo json_encode($retorna);