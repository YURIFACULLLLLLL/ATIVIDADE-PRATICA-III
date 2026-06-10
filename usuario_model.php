<?php
session_start();
$rota = $_GET['rota'] ?? 'login';

switch ($rota) {
    case 'login':
        require 'controllers/autenticacao_controller.php';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            processar_login();
        } else {
            exibir_login();
        }
        break;
    case 'cadastro':
        require 'controllers/autenticacao_controller.php';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            processar_cadastro();
        } else {
            exibir_cadastro();
        }
        break;
    case 'feed':
        require 'controllers/post_controller.php';
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['postar'])) {
            processar_criar_post();
        } else {
            exibir_feed();
        }
        break;
    case 'meu_perfil':
        require 'controllers/usuario_controller.php';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['editar_perfil'])) {
                processar_atualizacao_perfil();
            } elseif (isset($_FILES['foto'])) {
                processar_trocar_foto();
            } elseif (isset($_POST['alterar_senha'])) {
                processar_trocar_senha();
            }
        } else {
            exibir_meu_perfil();
        }
        break;
    case 'buscar_usuarios':
        require 'controllers/usuario_controller.php';
        exibir_buscar_usuarios();
        break;
    case 'perfil_usuario':
        require 'controllers/usuario_controller.php';
        exibir_perfil_usuario();
        break;
    case 'curtir':
        require 'controllers/curtida_controller.php';
        processar_curtir_ajax();
        break;
    case 'seguir':
        require 'controllers/seguidor_controller.php';
        processar_seguir_ajax();
        break;
    case 'logout':
        require 'controllers/autenticacao_controller.php';
        logout();
        break;
    default:
        header('Location: index.php?rota=login');
        exit;
}
?>