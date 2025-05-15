<?php
// Definir o fuso horário para Brasília
date_default_timezone_set('America/Sao_Paulo');

// Habilitar log de erros
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Definir o tipo de conteúdo como JSON
header('Content-Type: application/json');

try {
    // Incluir o arquivo com a conexão com banco de dados
    include_once './conexao.php';

    // Receber os dados enviado pelo JavaScript
    $dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);

    // Log dos dados recebidos
    error_log("Dados recebidos no cadastro: " . print_r($dados, true));

    // Verificar se todos os campos necessários foram enviados
    $campos_obrigatorios = ['cad_title', 'cad_resp', 'cad_start', 'cad_end', 'cad_user_id'];
    foreach ($campos_obrigatorios as $campo) {
        if (empty($dados[$campo])) {
            throw new Exception("O campo " . str_replace('cad_', '', $campo) . " é obrigatório.");
        }
    }

    // Se a cor não foi enviada, definir uma cor padrão baseada na sala
    if (empty($dados['cad_color'])) {
        // Mapear cores padrão para cada sala
        $cores_salas = [
            '1' => '#FF4500', // Cabine 3ª andar
            '2' => '#8B4513', // Cabine Térreo
            '3' => '#0071c5', // Sala de Reuniões 01
            '4' => '#228B22', // Sala de Reuniões 02
            '5' => '#FFD700'  // Sala de Reuniões 03
        ];
        
        $dados['cad_color'] = $cores_salas[$dados['cad_user_id']] ?? '#CCCCCC';
        error_log("Cor definida automaticamente: " . $dados['cad_color']);
    }

    // Validar formato da cor apenas se ela foi enviada
    if (!empty($dados['cad_color']) && !preg_match('/^#[a-fA-F0-9]{6}$/', $dados['cad_color'])) {
        throw new Exception("Formato de cor inválido. A cor deve estar no formato hexadecimal (ex: #FF0000).");
    }

    // Recuperar os dados da sala no BD
    $query_user = "SELECT id, sala FROM users WHERE id = :id LIMIT 1";
    $result_user = $conn->prepare($query_user);
    $result_user->bindParam(':id', $dados['cad_user_id']);
    $result_user->execute();
    $row_user = $result_user->fetch(PDO::FETCH_ASSOC);

    if (!$row_user) {
        throw new Exception("Sala não encontrada.");
    }

    // Log dos dados da sala
    error_log("Dados da sala: " . print_r($row_user, true));

    // Converter as datas para o formato do MySQL
    $start_date = new DateTime($dados['cad_start']);
    $end_date = new DateTime($dados['cad_end']);

    // Formatar as datas para o MySQL
    $start_mysql = $start_date->format('Y-m-d H:i:s');
    $end_mysql = $end_date->format('Y-m-d H:i:s');

    // Log das datas convertidas
    error_log("Datas convertidas - Start: " . $start_mysql . ", End: " . $end_mysql);

    // Criar a QUERY cadastrar evento no banco de dados
    $query_cad_event = "INSERT INTO events (title, color, start, end, resp, user_id) 
                       VALUES (:title, :color, STR_TO_DATE(:start, '%Y-%m-%d %H:%i:%s'), 
                       STR_TO_DATE(:end, '%Y-%m-%d %H:%i:%s'), :resp, :user_id)";

    // Prepara a QUERY
    $cad_event = $conn->prepare($query_cad_event);

    // Substituir o link pelo valor
    $cad_event->bindParam(':title', $dados['cad_title']);
    $cad_event->bindParam(':color', $dados['cad_color']);
    $cad_event->bindParam(':start', $start_mysql);
    $cad_event->bindParam(':end', $end_mysql);
    $cad_event->bindParam(':resp', $dados['cad_resp']);
    $cad_event->bindParam(':user_id', $dados['cad_user_id']);

    // Log da query preparada
    error_log("Query preparada: " . $query_cad_event);
    error_log("Parâmetros: " . print_r([
        'title' => $dados['cad_title'],
        'color' => $dados['cad_color'],
        'start' => $start_mysql,
        'end' => $end_mysql,
        'resp' => $dados['cad_resp'],
        'user_id' => $dados['cad_user_id']
    ], true));

    // Verificar se consegui cadastrar corretamente
    if ($cad_event->execute()) {
        // Formatar as datas para retornar no formato que o FullCalendar espera
        $start_iso = $start_date->format('Y-m-d\TH:i:s');
        $end_iso = $end_date->format('Y-m-d\TH:i:s');

        $retorna = [
            'status' => true,
            'msg' => 'Evento cadastrado com sucesso!',
            'id' => $conn->lastInsertId(), 
            'title' => $dados['cad_title'], 
            'color' => $dados['cad_color'], 
            'start' => $start_iso, 
            'end' => $end_iso, 
            'resp' => $dados['cad_resp'],
            'user_id' => $row_user['id'],
            'sala' => $row_user['sala']
        ];
        error_log("Evento cadastrado com sucesso: " . print_r($retorna, true));
    } else {
        $erro = $cad_event->errorInfo();
        error_log("Erro ao cadastrar evento: " . print_r($erro, true));
        throw new Exception("Erro ao cadastrar evento: " . $erro[2]);
    }

} catch (Exception $e) {
    error_log("Erro no cadastro: " . $e->getMessage());
    $retorna = [
        'status' => false,
        'msg' => $e->getMessage()
    ];
} catch (PDOException $e) {
    error_log("Erro de banco de dados: " . $e->getMessage());
    $retorna = [
        'status' => false,
        'msg' => "Erro de banco de dados. Por favor, tente novamente."
    ];
} catch (Throwable $e) {
    error_log("Erro inesperado: " . $e->getMessage());
    $retorna = [
        'status' => false,
        'msg' => "Erro inesperado. Por favor, tente novamente."
    ];
}

echo json_encode($retorna);