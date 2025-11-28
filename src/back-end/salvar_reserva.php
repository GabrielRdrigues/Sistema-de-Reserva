<?php
// salvar_reserva.php
require 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = $_POST['nome'];
    $cpf = preg_replace('/[^0-9]/', '', $_POST['cpf']);
    $telefone = preg_replace('/[^0-9]/', '', $_POST['telefone']);
    $data = $_POST['data'];
    $hora = $_POST['hora'];
    $pessoas = (int)$_POST['pessoas'];
    $obs = $_POST['obs'];
    $limite_maximo = 40;

    try {
        // 1. Verifica capacidade
        $sql_check = "SELECT SUM(num_pessoas) as total FROM reservas WHERE data_reserva = :data AND horario = :hora";
        $stmt_check = $pdo->prepare($sql_check);
        $stmt_check->execute([':data' => $data, ':hora' => $hora]);
        $resultado = $stmt_check->fetch(PDO::FETCH_ASSOC);
        $total_ja_reservado = $resultado['total'] ? (int)$resultado['total'] : 0;

        if (($total_ja_reservado + $pessoas) > $limite_maximo) {
            $lugares_restantes = $limite_maximo - $total_ja_reservado;
            header("Location: ../front-end/pages/reserva.html?erro=lotado&restam=" . $lugares_restantes);
            exit;
        }

        // 2. Salva (sem ID)
        $sql = "INSERT INTO reservas (nome, cpf, telefone, data_reserva, horario, num_pessoas, observacoes) 
                VALUES (:nome, :cpf, :telefone, :data, :hora, :pessoas, :obs)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':nome' => $nome,
            ':cpf' => $cpf,
            ':telefone' => $telefone,
            ':data' => $data,
            ':hora' => $hora,
            ':pessoas' => $pessoas,
            ':obs' => $obs
        ]);
        
        // Redireciona passando o CPF na URL para facilitar (opcional)
        header("Location: ../front-end/pages/consulta.html?status=sucesso&cpf=" . $cpf);

    } catch (Exception $e) {
        echo "Erro no sistema: " . $e->getMessage();
    }
}
?>