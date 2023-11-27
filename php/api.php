<?php
session_start();

$host = 'localhost';
$port = 9000;

$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
socket_connect($socket, $host, $port);

// Receber dados do POST
$numeroRecebido = $_POST['numero'];

// Lógica para verificar se o número está correto
$numeroAleatorio = $_SESSION['numAleatorio'];
$resposta = ($numeroRecebido == $numeroAleatorio) ? "Número correto!" : "Número incorreto!";

// Enviar a resposta de volta ao cliente
echo $resposta;

// Fechar o socket
socket_close($socket);
?>
