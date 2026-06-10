<?php
require_once 'config/conexao.php';

function curtida_contar_por_post($post_id) {
    global $conexao;
    $stmt = mysqli_prepare($conexao, "SELECT COUNT(*) as total FROM curtidas WHERE post_id = ?");
    mysqli_stmt_bind_param($stmt, "i", $post_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    return $result['total'];
}
?>