<?php
require_once 'models/usuario_model.php';
require_once 'models/seguidor_model.php';
require_once 'models/post_model.php';

function verificar_autenticacao() {
    if (!isset($_SESSION['usuario_id'])) {
        header('Location: index.php?rota=login');
        exit;
    }
}

function exibir_meu_perfil() {
    verificar_autenticacao();
    $usuario = usuario_buscar_por_id($_SESSION['usuario_id']);
    $seguidores = usuario_contar_seguidores($usuario['id']);
    $seguindo = usuario_contar_seguindo($usuario['id']);
    $total_posts = usuario_contar_posts($usuario['id']);
    $erro = $_SESSION['erro_perfil'] ?? '';
    $sucesso = $_SESSION['sucesso_perfil'] ?? '';
    unset($_SESSION['erro_perfil'], $_SESSION['sucesso_perfil']);
    require 'views/meu_perfil.php';
}

function processar_atualizacao_perfil() {
    verificar_autenticacao();
    $usuario_id = $_SESSION['usuario_id'];
    $nome = trim($_POST['nome'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $data_nasc = $_POST['data_nascimento'] ?? '';
    $genero = $_POST['genero'] ?? '';

    $erro = '';

    $usuario_username = usuario_buscar_por_username($username);
    if ($usuario_username && $usuario_username['id'] != $usuario_id) {
        $erro = 'Nome de usuário já está em uso.';
    }

    $usuario_email = usuario_buscar_por_email($email);
    if ($usuario_email && $usuario_email['id'] != $usuario_id) {
        $erro = 'E-mail já está em uso.';
    }

    if (empty($erro)) {
        if (usuario_atualizar_perfil($usuario_id, $nome, $username, $email, $data_nasc, $genero)) {
            $_SESSION['usuario_nome'] = $nome;
            $_SESSION['usuario_username'] = $username;
            $_SESSION['sucesso_perfil'] = 'Perfil atualizado com sucesso.';
        } else {
            $erro = 'Erro ao atualizar perfil. Tente novamente.';
        }
    }

    if (!empty($erro)) {
        $_SESSION['erro_perfil'] = $erro;
    }
    header('Location: index.php?rota=meu_perfil');
    exit;
}

function processar_trocar_foto() {
    verificar_autenticacao();
    $usuario_id = $_SESSION['usuario_id'];
    $arquivo = $_FILES['foto'] ?? null;
    $erro = '';
    
    if ($arquivo && $arquivo['error'] === UPLOAD_ERR_OK) {
        $extensoes = ['jpg', 'jpeg', 'png'];
        $ext = strtolower(pathinfo($arquivo['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $extensoes)) {
            $erro = 'Formato inválido. Use JPG, JPEG ou PNG.';
        } elseif ($arquivo['size'] > 2 * 1024 * 1024) {
            $erro = 'Arquivo muito grande (máx 2MB).';
        } else {
            $nome_arquivo = 'usuario_' . $usuario_id . '_' . time() . '.' . $ext;
            $destino = 'uploads/' . $nome_arquivo;
            if (move_uploaded_file($arquivo['tmp_name'], $destino)) {
                if (usuario_atualizar_foto($usuario_id, $nome_arquivo)) {
                    $_SESSION['usuario_foto'] = $nome_arquivo;
                    $_SESSION['sucesso_perfil'] = 'Foto atualizada.';
                } else {
                    $erro = 'Erro ao salvar no banco.';
                }
            } else {
                $erro = 'Erro no upload.';
            }
        }
    } else {
        $erro = 'Selecione uma foto.';
    }
    
    if ($erro) {
        $_SESSION['erro_perfil'] = $erro;
    }
    header('Location: index.php?rota=meu_perfil');
    exit;
}

function processar_trocar_senha() {
    verificar_autenticacao();
    $usuario_id = $_SESSION['usuario_id'];
    $atual = $_POST['senha_atual'] ?? '';
    $nova = $_POST['nova_senha'] ?? '';
    $confirmar = $_POST['confirmar_senha'] ?? '';
    
    $usuario = usuario_buscar_por_id($usuario_id);
    $erro = '';
    
    if (!password_verify($atual, $usuario['senha'])) {
        $erro = 'Senha atual incorreta.';
    } elseif ($nova !== $confirmar) {
        $erro = 'Nova senha e confirmação não coincidem.';
    } elseif (strlen($nova) < 6 || !preg_match('/[A-Z]/', $nova) || !preg_match('/[0-9]/', $nova)) {
        $erro = 'A nova senha deve ter mínimo 6 caracteres, uma maiúscula e um número.';
    } else {
        if (usuario_atualizar_senha($usuario_id, $nova)) {
            $_SESSION['sucesso_perfil'] = 'Senha alterada com sucesso.';
        } else {
            $erro = 'Erro ao alterar senha.';
        }
    }
    
    if ($erro) {
        $_SESSION['erro_perfil'] = $erro;
    }
    header('Location: index.php?rota=meu_perfil');
    exit;
}

function exibir_buscar_usuarios() {
    verificar_autenticacao();
    $termo = $_GET['q'] ?? '';
    $resultados = [];
    if (!empty($termo)) {
        $termo_sanitizado = htmlspecialchars($termo);
        $resultados = usuario_buscar_todos($termo_sanitizado);
    }
    require 'views/buscar_usuarios.php';
}

function exibir_perfil_usuario() {
    verificar_autenticacao();
    $id = $_GET['id'] ?? 0;
    $usuario = usuario_buscar_por_id($id);
    if (!$usuario) {
        header('Location: index.php?rota=feed');
        exit;
    }
    $seguidores = usuario_contar_seguidores($id);
    $seguindo = usuario_contar_seguindo($id);
    $total_posts = usuario_contar_posts($id);
    $posts = post_listar_por_usuario($id, $_SESSION['usuario_id']);
    $esta_seguindo = seguidor_verificar($_SESSION['usuario_id'], $id) ? true : false;
    require 'views/perfil_usuario.php';
}
?>