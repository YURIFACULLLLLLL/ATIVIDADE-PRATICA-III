<?php
$host = 'localhost';
$usuario = 'admin';
$senha = 'senha123';
$banco = 'rede_social';

$conexao = mysqli_connect($host, $usuario, $senha, $banco);

if (!$conexao) {
    die("Erro de conexão: " . mysqli_connect_error());
}
?>