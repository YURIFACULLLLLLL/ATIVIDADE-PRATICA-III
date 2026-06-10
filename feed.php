<?php
require_once 'config/conexao.php';

function post_criar($usuario_id, $conteudo) {
    global $conexao;
    $stmt = mysqli_prepare($conexao, "INSERT INTO posts (usuario_id, conteudo) VALUES (?, ?)");
    mysqli_stmt_bind_param($stmt, "is", $usuario_id, $conteudo);
    return mysqli_stmt_execute($stmt);
}

function post_listar_feed($usuario_id) {
    global $conexao;
    $sql = "SELECT p.*, u.nome, u.username, u.foto,
            (SELECT COUNT(*) FROM curtidas WHERE post_id = p.id) as total_curtidas,
            (SELECT COUNT(*) FROM curtidas WHERE post_id = p.id AND usuario_id = $usuario_id) > 0 as ja_curtiu
            FROM posts p
            JOIN usuarios u ON p.usuario_id = u.id
            WHERE p.usuario_id = $usuario_id OR p.usuario_id IN (SELECT seguindo_id FROM seguidores WHERE seguidor_id = $usuario_id)
            ORDER BY p.created_at DESC";
    return mysqli_query($conexao, $sql);
}

function post_listar_por_usuario($usuario_id, $visitante_id) {
    global $conexao;
    $sql = "SELECT p.*, u.nome, u.username, u.foto,
            (SELECT COUNT(*) FROM curtidas WHERE post_id = p.id) as total_curtidas,
            (SELECT COUNT(*) FROM curtidas WHERE post_id = p.id AND usuario_id = $visitante_id) > 0 as ja_curtiu
            FROM posts p
            JOIN usuarios u ON p.usuario_id = u.id
            WHERE p.usuario_id = $usuario_id
            ORDER BY p.created_at DESC";
    return mysqli_query($conexao, $sql);
}

function post_verificar_curtida($post_id, $usuario_id) {
    global $conexao;
    $stmt = mysqli_prepare($conexao, "SELECT id FROM curtidas WHERE post_id = ? AND usuario_id = ?");
    mysqli_stmt_bind_param($stmt, "ii", $post_id, $usuario_id);
    mysqli_stmt_execute($stmt);
    return mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
}

function post_adicionar_curtida($post_id, $usuario_id) {
    global $conexao;
    $stmt = mysqli_prepare($conexao, "INSERT INTO curtidas (post_id, usuario_id) VALUES (?, ?)");
    mysqli_stmt_bind_param($stmt, "ii", $post_id, $usuario_id);
    return mysqli_stmt_execute($stmt);
}

function post_remover_curtida($post_id, $usuario_id) {
    global $conexao;
    $stmt = mysqli_prepare($conexao, "DELETE FROM curtidas WHERE post_id = ? AND usuario_id = ?");
    mysqli_stmt_bind_param($stmt, "ii", $post_id, $usuario_id);
    return mysqli_stmt_execute($stmt);
}
?>