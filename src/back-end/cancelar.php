<?php
// src/back-end/cancelar.php
require 'db.php';
header('Content-Type: application/json; charset=utf-8');

// Recebe os dados enviados pelo JavaScript (JSON)
$input = json_decode(file_get_contents('php://input'), true);

if (isset($input['cpf']) && isset($input['data']) && isset($input['hora'])) {
    try {
        // Limpa o CPF (deixa só números)
        $cpf = preg_replace('/[^0-9]/', '', $input['cpf']);
        $data = $input['data'];
        $hora = $input['hora'];

        // Deleta baseando-se na combinação única de CPF + Data + Hora
        // (Já que não temos ID, essa é a forma segura de não apagar a reserva errada)
        $sql = "DELETE FROM reservas WHERE cpf = :cpf AND data_reserva = :data AND horario = :hora";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':cpf' => $cpf,
            ':data' => $data,
            ':hora' => $hora
        ]);

        if ($stmt->rowCount() > 0) {
            echo json_encode(['status' => 'sucesso', 'msg' => 'Reserva cancelada com sucesso!']);
        } else {
            echo json_encode(['status' => 'erro', 'msg' => 'Não foi possível encontrar essa reserva para cancelar.']);
        }

    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['status' => 'erro', 'msg' => 'Erro no banco: ' . $e->getMessage()]);
    }
} else {
    http_response_code(400);
    echo json_encode(['status' => 'erro', 'msg' => 'Dados incompletos.']);
}
?>