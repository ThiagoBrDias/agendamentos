<?php

// Definir o fuso horário para Brasília
date_default_timezone_set('America/Sao_Paulo');

// Habilitar log de erros
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Incluir o arquivo com a conexão com banco de dados
include_once './conexao.php';

// Receber os dados enviado pelo JavaScript
$dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);

// Log dos dados recebidos
error_log("Dados recebidos no editar_evento.php: " . print_r($dados, true));

// Validar os dados recebidos
if (empty($dados['edit_id']) || empty($dados['edit_title']) || empty($dados['edit_start']) || empty($dados['edit_end'])) {
    $retorna = ['status' => false, 'msg' => 'Erro: Dados obrigatórios não informados!'];
    echo json_encode($retorna);
    exit;
}

// Recuperar os dados do usuário no banco de dados
$query_user = "SELECT id, sala FROM users WHERE id =:id LIMIT 1";
$result_user = $conn->prepare($query_user);
$result_user->bindParam(':id', $dados['edit_user_id']);
$result_user->execute();
$row_user = $result_user->fetch(PDO::FETCH_ASSOC);

if (!$row_user) {
    $retorna = ['status' => false, 'msg' => 'Erro: Sala não encontrada!'];
    echo json_encode($retorna);
    exit;
}

// Verificar se o evento existe e recuperar seus dados atuais
$query_event = "SELECT start, end FROM events WHERE id = :id LIMIT 1";
$result_event = $conn->prepare($query_event);
$result_event->bindParam(':id', $dados['edit_id']);
$result_event->execute();
$evento_atual = $result_event->fetch(PDO::FETCH_ASSOC);

if (!$evento_atual) {
    $retorna = ['status' => false, 'msg' => 'Erro: Evento não encontrado!'];
    echo json_encode($retorna);
    exit;
}

// Se não foi enviada uma nova cor, manter a cor atual
if (empty($dados['edit_color'])) {
    $query_color = "SELECT color FROM events WHERE id = :id LIMIT 1";
    $result_color = $conn->prepare($query_color);
    $result_color->bindParam(':id', $dados['edit_id']);
    $result_color->execute();
    $cor_atual = $result_color->fetch(PDO::FETCH_ASSOC);
    $dados['edit_color'] = $cor_atual['color'];
}

// Converter as datas para o formato do MySQL
$start_date = new DateTime($dados['edit_start']);
$end_date = new DateTime($dados['edit_end']);

// Formatar as datas para o MySQL
$start_mysql = $start_date->format('Y-m-d H:i:s');
$end_mysql = $end_date->format('Y-m-d H:i:s');

// Log das datas convertidas
error_log("Datas convertidas - Start: " . $start_mysql . ", End: " . $end_mysql);

// Criar a QUERY editar evento no banco de dados
$query_edit_event = "UPDATE events SET 
    title = :title, 
    color = :color, 
    start = :start, 
    end = :end, 
    resp = :resp, 
    user_id = :user_id 
    WHERE id = :id";

// Prepara a QUERY
$edit_event = $conn->prepare($query_edit_event);

// Substituir o link pelo valor
$edit_event->bindParam(':title', $dados['edit_title']);
$edit_event->bindParam(':color', $dados['edit_color']);
$edit_event->bindParam(':start', $start_mysql);
$edit_event->bindParam(':end', $end_mysql);
$edit_event->bindParam(':resp', $dados['edit_resp']);
$edit_event->bindParam(':user_id', $dados['edit_user_id']);
$edit_event->bindParam(':id', $dados['edit_id']);

// Log da query preparada
error_log("Query preparada: " . $query_edit_event);
error_log("Parâmetros: " . print_r([
    'title' => $dados['edit_title'],
    'color' => $dados['edit_color'],
    'start' => $start_mysql,
    'end' => $end_mysql,
    'resp' => $dados['edit_resp'],
    'user_id' => $dados['edit_user_id'],
    'id' => $dados['edit_id']
], true));

// Verificar se consegui editar corretamente
if ($edit_event->execute()) {
    // Formatar as datas para retornar no formato que o FullCalendar espera
    $start_iso = $start_date->format('Y-m-d\TH:i:s');
    $end_iso = $end_date->format('Y-m-d\TH:i:s');

    $retorna = [
        'status' => true, 
        'msg' => 'Evento editado com sucesso!', 
        'id' => $dados['edit_id'], 
        'title' => $dados['edit_title'], 
        'color' => $dados['edit_color'], 
        'start' => $start_iso, 
        'end' => $end_iso, 
        'resp' => $dados['edit_resp'],
        'user_id' => $row_user['id'],
        'sala' => $row_user['sala']
    ];

    // Log do retorno
    error_log("Evento editado com sucesso: " . print_r($retorna, true));
} else {
    $erro = $edit_event->errorInfo();
    error_log("Erro ao editar evento: " . print_r($erro, true));
    $retorna = ['status' => false, 'msg' => 'Erro: Evento não editado! - ' . $erro[2]];
}

// Adicionar header para garantir que a resposta seja JSON
header('Content-Type: application/json');
echo json_encode($retorna);