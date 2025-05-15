<?php
// Definir o fuso horário para Brasília
date_default_timezone_set('America/Sao_Paulo');

// Habilitar log de erros
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Incluir o arquivo com a conexão com banco de dados
include_once './conexao.php';

// Receber o id do usuário
$user_id = filter_input(INPUT_GET, 'user_id', FILTER_SANITIZE_NUMBER_INT);

// Log do user_id recebido
error_log("User ID recebido: " . $user_id);

// Verificar se o parâmetro user_id foi enviado
if (!empty($user_id)) {
    // QUERY para recuperar os eventos
    $query_events = "SELECT e.id, e.title, e.color, 
                    DATE_FORMAT(e.start, '%Y-%m-%d %H:%i:%s') as start,
                    DATE_FORMAT(e.end, '%Y-%m-%d %H:%i:%s') as end,
                    e.resp, e.user_id, u.sala
                    FROM events AS e
                    INNER JOIN users AS u ON u.id = e.user_id
                    WHERE u.id = :user_id";

    // Prepara a QUERY
    $result_events = $conn->prepare($query_events);

    // Atribuir o valor do parâmetro
    $result_events->bindParam(':user_id', $user_id, PDO::PARAM_INT);
} else {
    // QUERY para recuperar os eventos
    $query_events = "SELECT e.id, e.title, e.color,
                    DATE_FORMAT(e.start, '%Y-%m-%d %H:%i:%s') as start,
                    DATE_FORMAT(e.end, '%Y-%m-%d %H:%i:%s') as end,
                    e.resp, e.user_id, u.sala
                    FROM events AS e
                    INNER JOIN users AS u ON u.id = e.user_id";

    // Prepara a QUERY
    $result_events = $conn->prepare($query_events);
}

// Log da query
error_log("Query de eventos: " . $query_events);

// Executar a QUERY
$result_events->execute();

// Criar o array que recebe os eventos
$eventos = [];

// Percorrer a lista de registros retornado do banco de dados
while($row_events = $result_events->fetch(PDO::FETCH_ASSOC)) {
    // Log dos dados brutos do evento
    error_log("Dados brutos do evento: " . print_r($row_events, true));

    // Converter as datas para o formato ISO 8601
    $start_date = new DateTime($row_events['start']);
    $end_date = new DateTime($row_events['end']);

    // Formatar as datas para o formato que o FullCalendar espera
    $start_iso = $start_date->format('Y-m-d\TH:i:s');
    $end_iso = $end_date->format('Y-m-d\TH:i:s');

    // Log das datas convertidas
    error_log("Datas convertidas para o evento {$row_events['id']} - Start: {$start_iso}, End: {$end_iso}");

    $eventos[] = [
        'id' => $row_events['id'],
        'title' => $row_events['title'],
        'color' => $row_events['color'],
        'start' => $start_iso,
        'end' => $end_iso,
        'resp' => $row_events['resp'],
        'user_id' => $row_events['user_id'],
        'sala' => $row_events['sala']
    ];
}

// Log dos eventos formatados
error_log("Eventos formatados: " . print_r($eventos, true));

// Adicionar header para garantir que a resposta seja JSON
header('Content-Type: application/json');
echo json_encode($eventos);