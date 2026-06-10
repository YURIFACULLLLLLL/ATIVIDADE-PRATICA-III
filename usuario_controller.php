<?php
require_once 'models/usuario_model.php';

function exibir_login() {
    $erro = $_SESSION['erro_login'] ?? '';
    unset($_SESSION['erro_login']);
    require 'views/login.php';
}

function processar_login() {
    $email = $_POST['email'] ?? '';
    $senha = $_POST['senha'] ?? '';
    $usuario = usuario_buscar_por_email($email);
    
    if ($usuario && password_verify($senha, $usuario['senha'])) {
        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['usuario_nome'] = $usuario['nome'];
        $_SESSION['usuario_username'] = $usuario['username'];
        $_SESSION['usuario_foto'] = $usuario['foto'];
        header('Location: index.php?rota=feed');
        exit;
    } else {
        $_SESSION['erro_login'] = 'E-mail ou senha inválidos.';
        header('Location: index.php?rota=login');
        exit;
    }
}

function exibir_cadastro() {
    $erro = $_SESSION['erro_cadastro'] ?? '';
    $dados = $_SESSION['dados_cadastro'] ?? [];
    unset($_SESSION['erro_cadastro'], $_SESSION['dados_cadastro']);
    require 'views/cadastro.php';
}

function processar_cadastro() {
    $nome = trim($_POST['nome'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';
    $confirmar = $_POST['confirmar_senha'] ?? '';
    $data_nasc = $_POST['data_nascimento'] ?? '';
    $genero = $_POST['genero'] ?? '';
    
    $erros = [];
    if (empty($nome) || empty($username) || empty($email) || empty($senha) || empty($data_nasc) || empty($genero)) {
        $erros[] = 'Todos os campos são obrigatórios.';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erros[] = 'E-mail inválido.';
    }
    if ($senha !== $confirmar) {
        $erros[] = 'Senhas não coincidem.';
    }
    if (strlen($senha) < 6 || !preg_match('/[A-Z]/', $senha) || !preg_match('/[0-9]/', $senha)) {
        $erros[] = 'A senha deve ter no mínimo 6 caracteres, uma letra maiúscula e um número.';
    }
    if (!strtotime($data_nasc)) {
        $erros[] = 'Data de nascimento inválida.';
    }
    if (!in_array($genero, ['Feminino', 'Masculino', 'Outro'])) {
        $erros[] = 'Gênero inválido.';
    }
    if (usuario_buscar_por_email($email)) {
        $erros[] = 'E-mail já cadastrado.';
    }
    if (usuario_buscar_por_username($username)) {
        $erros[] = 'Nome de usuário já existe.';
    }
    
    if (empty($erros)) {
        if (usuario_cadastrar($nome, $username, $email, $senha, $data_nasc, $genero)) {
            header('Location: index.php?rota=login');
            exit;
        } else {
            $erros[] = 'Erro ao cadastrar. Tente novamente.';
        }
    }
    
    $_SESSION['erro_cadastro'] = implode('<br>', $erros);
    $_SESSION['dados_cadastro'] = $_POST;
    header('Location: index.php?rota=cadastro');
    exit;
}

function logout() {
    session_destroy();
    header('Location: index.php?rota=login');
    exit;
}
?>