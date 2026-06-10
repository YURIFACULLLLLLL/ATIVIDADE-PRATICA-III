<?php
require_once 'models/seguidor_model.php';

function processar_seguir_ajax() {
    if (!isset($_SESSION['usuario_id'])) {
        echo json_encode(['erro' => 'não autenticado']);
        exit;
    }
    $seguindo_id = $_POST['usuario_id'] ?? 0;
    $seguidor_id = $_SESSION['usuario_id'];
    
    if ($seguidor_id == $seguindo_id) {
        echo json_encode(['erro' => 'não pode seguir a si mesmo']);
        exit;
    }
    
    if (seguidor_verificar($seguidor_id, $seguindo_id)) {
        seguidor_deixar_seguir($seguidor_id, $seguindo_id);
        $seguindo = false;
    } else {
        seguidor_seguir($seguidor_id, $seguindo_id);
        $seguindo = true;
    }
    
    echo json_encode(['seguindo' => $seguindo]);
    exit;
}
?>