<?php
$numGerado = $_POST['numGerado'];
$numJogado = isset($_POST['numero']) ? $_POST['numero'] : null;

$resposta = $numGerado == $numJogado ? true : false;
echo json_encode(['resposta' => $resposta]);

return;

$host = 'localhost';
$port = 9000;

$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
socket_bind($socket, $host, $port);
socket_listen($socket);