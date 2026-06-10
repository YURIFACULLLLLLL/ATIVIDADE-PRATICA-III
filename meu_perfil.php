<?php
require_once 'config/conexao.php';

function usuario_cadastrar($nome, $username, $email, $senha, $data_nasc, $genero) {
    global $conexao;
    $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
    $stmt = mysqli_prepare($conexao, "INSERT INTO usuarios (nome, username, email, senha, data_nascimento, genero) VALUES (?, ?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "ssssss", $nome, $username, $email, $senha_hash, $data_nasc, $genero);
    return mysqli_stmt_execute($stmt);
}

function usuario_buscar_por_email($email) {
    global $conexao;
    $stmt = mysqli_prepare($conexao, "SELECT * FROM usuarios WHERE email = ?");
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    return mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
}

function usuario_buscar_por_id($id) {
    global $conexao;
    $stmt = mysqli_prepare($conexao, "SELECT * FROM usuarios WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    return mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
}

function usuario_buscar_por_username($username) {
    global $conexao;
    $stmt = mysqli_prepare($conexao, "SELECT * FROM usuarios WHERE username = ?");
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    return mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
}

function usuario_atualizar_foto($usuario_id, $foto) {
    global $conexao;
    $stmt = mysqli_prepare($conexao, "UPDATE usuarios SET foto = ? WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "si", $foto, $usuario_id);
    return mysqli_stmt_execute($stmt);
}

function usuario_atualizar_senha($usuario_id, $nova_senha) {
    global $conexao;
    $senha_hash = password_hash($nova_senha, PASSWORD_DEFAULT);
    $stmt = mysqli_prepare($conexao, "UPDATE usuarios SET senha = ? WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "si", $senha_hash, $usuario_id);
    return mysqli_stmt_execute($stmt);
}

function usuario_atualizar_perfil($usuario_id, $nome, $username, $email, $data_nasc, $genero) {
    global $conexao;
    $stmt = mysqli_prepare($conexao, "UPDATE usuarios SET nome = ?, username = ?, email = ?, data_nascimento = ?, genero = ? WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "sssssi", $nome, $username, $email, $data_nasc, $genero, $usuario_id);
    return mysqli_stmt_execute($stmt);
}

function usuario_contar_seguidores($usuario_id) {
    global $conexao;
    $stmt = mysqli_prepare($conexao, "SELECT COUNT(*) as total FROM seguidores WHERE seguindo_id = ?");
    mysqli_stmt_bind_param($stmt, "i", $usuario_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    return $result['total'];
}

function usuario_contar_seguindo($usuario_id) {
    global $conexao;
    $stmt = mysqli_prepare($conexao, "SELECT COUNT(*) as total FROM seguidores WHERE seguidor_id = ?");
    mysqli_stmt_bind_param($stmt, "i", $usuario_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    return $result['total'];
}

function usuario_contar_posts($usuario_id) {
    global $conexao;
    $stmt = mysqli_prepare($conexao, "SELECT COUNT(*) as total FROM posts WHERE usuario_id = ?");
    mysqli_stmt_bind_param($stmt, "i", $usuario_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    return $result['total'];
}

function usuario_buscar_todos($termo) {
    global $conexao;
    $termo = "%$termo%";
    $stmt = mysqli_prepare($conexao, "SELECT * FROM usuarios WHERE nome LIKE ? OR username LIKE ?");
    mysqli_stmt_bind_param($stmt, "ss", $termo, $termo);
    mysqli_stmt_execute($stmt);
    return mysqli_stmt_get_result($stmt);
}
?>