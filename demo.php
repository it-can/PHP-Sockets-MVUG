#!/php -q
<?php

// Run from command prompt > php demo.php
require_once('websocket.server.php');

/**
 * This demo resource handler will respond to all messages sent to /echo/ on the socketserver below
 *
 * All this handler does is echoing the responds to the user
 * @author Chris
 *
 */
class DemoEchoHandler extends WebSocketResourceHandler{
	public function onMessage(WebSocketUser $user, IWebSocketMessage $msg){
		$text = $msg->getData();

		$this->say("[MVUG] {".$text."}");

		// Show message to all connected users
		foreach ($this->users AS $user)
		{
			// Echo
			$this->send($user, $text);
		}
	}
}

/**
 * Demo socket server. Implements the basic eventlisteners and attaches a resource handler for /echo/ urls.
 *
 *
 * @author Chris
 *
 */
class DemoSocketServer extends WebSocketServer{
	public function __construct($address, $port){
		parent::__construct($address, $port);

		$this->addResourceHandler('echo', new DemoEchoHandler());
	}
	protected function onConnect(WebSocketUser $user){
		$this->say("[MVUG] {$user->id} connected");
	}

	public function onMessage($user, IWebSocketMessage $msg){
		$this->say("[MVUG] {$user->id} says '{$msg->getData()}'");
	}

	protected function onDisconnect(WebSocketUser $user){
		$this->say("[MVUG] {$user->id} disconnected");
	}
}

// Start server
$server = new DemoSocketServer('127.0.0.1', 12345);
$server->run();
