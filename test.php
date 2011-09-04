<?php
require_once("websocket.client.php");
require_once("websocket.admin.php");

$input = "Hello World!";
$msg = WebSocketMessage::create($input);

$client = new WebSocket("ws://127.0.0.1:12345/echo");
$client->open();
$client->sendMessage($msg);

$client->close();
