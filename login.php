<?php
require_once 'config/conexao.php';

function seguidor_seguir($seguidor_id, $seguindo_id) {
    global $conexao;
    $stmt = mysqli_prepare($conexao, "INSERT INTO seguidores (seguidor_id, seguindo_id) VALUES (?, ?)");
    mysqli_stmt_bind_param($stmt, "ii", $seguidor_id, $seguindo_id);
    return mysqli_stmt_execute($stmt);
}

function seguidor_deixar_seguir($seguidor_id, $seguindo_id) {
    global $conexao;
    $stmt = mysqli_prepare($conexao, "DELETE FROM seguidores WHERE seguidor_id = ? AND seguindo_id = ?");
    mysqli_stmt_bind_param($stmt, "ii", $seguidor_id, $seguindo_id);
    return mysqli_stmt_execute($stmt);
}

function seguidor_verificar($seguidor_id, $seguindo_id) {
    global $conexao;
    $stmt = mysqli_prepare($conexao, "SELECT id FROM seguidores WHERE seguidor_id = ? AND seguindo_id = ?");
    mysqli_stmt_bind_param($stmt, "ii", $seguidor_id, $seguindo_id);
    mysqli_stmt_execute($stmt);
    return mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
}
?>