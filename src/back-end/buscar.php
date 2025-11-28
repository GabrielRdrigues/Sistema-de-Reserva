<?php
// buscar.php
require 'db.php';
header('Content-Type: application/json; charset=utf-8');

$termo = $_GET['termo'] ?? '';

if ($termo) {
    try {
        // Limpa para deixar só números
        $termoLimpo = preg_replace('/[^0-9]/', '', $termo);

        // Monta a busca (agora SEM buscar por ID)
        $sql = "SELECT * FROM reservas WHERE 1=0";
        $params = [];

        if (!empty($termoLimpo)) {
            // Busca apenas por CPF ou Telefone
            $sql .= " OR cpf = :cpf_b"; 
            $sql .= " OR telefone LIKE :tel_b";
            
            $params[':cpf_b'] = $termoLimpo;
            $params[':tel_b'] = "%$termoLimpo%";
        }

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($resultado) {
            echo json_encode(['status' => 'encontrado', 'dados' => $resultado]);
        } else {
            echo json_encode(['status' => 'nao_encontrado']);
        }
    } catch (Exception $e) {
        http_response_code(500); 
        echo json_encode(['status' => 'erro', 'msg' => $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'erro', 'msg' => 'Termo de busca vazio']);
}
?>