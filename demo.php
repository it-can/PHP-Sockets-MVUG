#!/php -q
<?php

// Run from command prompt > php demo.php
require_once("websocket.server.php");

/**
 * This demo resource handler will respond to all messages sent to /echo/ on the socketserver below
 *
 * All this handler does is echoing the responds to the user
 * @author Chris
 *
 */
class DemoEchoHandler extends WebSocketResourceHandler{
	public function onMessage(IWebSocketUser $user, IWebSocketMessage $msg){
		$this->say("[ECHO] {$msg->getData()}");

		foreach ($this->users AS $user)
		{
			// Echo
			$this->send($user, $msg->getData());
		}
	}

	public function onAdminMessage(IWebSocketUser $user, stdClass $obj){
		$this->say("[DEMO] Admin TEST received!");

		$frame = WebSocketFrame::create(WebSocketOpcode::PongFrame);
		$this->server->sendFrame($user, $frame);
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
	public function getAdminKey(){
		return "superdupersecretkey";
	}

	public function __construct($address, $port){
		parent::__construct($address, $port);

		$this->addResourceHandler("echo", new DemoEchoHandler());
	}
	protected function onConnect(IWebSocketUser $user){
		$this->say("[DEMO] {$user->getId()} connected");
	}

	public function onMessage($user, IWebSocketMessage $msg){
		$this->say("[DEMO] {$user->getId()} says '{$msg->getData()}'");
	}

	protected function onDisconnect(IWebSocketUser $user){
		$this->say("[DEMO] {$user->getId()} disconnected");
	}

	protected function onAdminTest(IWebSocketUser $user){
		$this->say("[DEMO] Admin TEST received!");

		$frame = WebSocketFrame::create(WebSocketOpcode::PongFrame);
		$this->sendFrame($user, $frame);
	}
}

// Start server
$server = new DemoSocketServer(0,12345);
$server->run();
