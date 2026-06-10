<?php
require_once 'models/post_model.php';

function processar_criar_post() {
    if (!isset($_SESSION['usuario_id'])) {
        echo 'erro';
        exit;
    }
    $texto = trim($_POST['texto'] ?? '');
    if (!empty($texto)) {
        post_criar($_SESSION['usuario_id'], $texto);
    }
    header('Location: index.php?rota=feed');
    exit;
}

function exibir_feed() {
    if (!isset($_SESSION['usuario_id'])) {
        header('Location: index.php?rota=login');
        exit;
    }
    $posts = post_listar_feed($_SESSION['usuario_id']);
    require 'views/feed.php';
}
?>