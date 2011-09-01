<?php
require_once("websocket.functions.php");
require_once("websocket.exceptions.php");
require_once("websocket.users.php");
require_once("websocket.framing.php");
require_once("websocket.message.php");
require_once("websocket.resources.php");

class WebSocket{
	protected $socket;
	protected $handshakeChallenge;
	protected $host;
	protected $port;
	protected $origin;
	protected $requestUri;
	protected $url;
	
	public function __construct($url){
		$parts = parse_url($url);
		
		$this->url = $url;
		
		if(in_array($parts['scheme'], array('ws')) === false)
			throw new WebSocketInvalidUrlScheme();
		
		$this->host = $parts['host'];
		$this->port = $parts['port'];
		
		$this->origin = 'http://'.$this->host;
		
		if(isset($parts['path']))
			$this->requestUri = $parts['path'];
		else $this->requestUri = "/";
		
		if(isset($parts['query']))
			$this->requestUri .= "?".$parts['query']; 
		
		$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
		socket_set_option($socket,SOL_SOCKET, SO_RCVTIMEO, array("sec" => 1, "usec" => 0));
		socket_connect($socket, $this->host, $this->port);

		$this->socket = $socket;

		$this->doHandshake();
	}
	
	/**
	 * TODO: Proper header generation!
	 * TODO: Check server response!
	 */
	protected function doHandshake(){
		$this->handshakeChallenge = WebSocketFunctions::randHybiKey();
		
		$buffer = 
		"GET {$this->url} HTTP/1.1
Connection: Upgrade
Host: {$this->host}:{$this->port}
Sec-WebSocket-Key: {$this->handshakeChallenge}
Sec-WebSocket-Origin: {$this->origin}
Sec-WebSocket-Version: 8
Upgrade: websocket";
		
		socket_write($this->socket, $buffer, strlen($buffer));
		
		// wait for response
		$buffer = socket_read($this->socket, 2048,PHP_BINARY_READ);
		$headers = WebSocketFunctions::parseHeaders($buffer);
		
		if($headers['Sec-Websocket-Accept'] != WebSocketFunctions::calcHybiResponse($this->handshakeChallenge)){
			throw new WebSocketInvalidChallengeResponse();
		}
	}
	
	public function send($string){
		$msg = WebSocketMessage::create($string);
		
		$this->sendMessage($msg);
	}
	
	public function sendMessage(IWebSocketMessage $msg){
		// Sent all fragments
		foreach($msg->getFrames() as $frame){
			$this->sendFrame($frame);
		}
	}

	public function sendFrame(IWebSocketFrame $frame){
		$msg = $frame->encode();
		socket_write($this->socket, $msg,strlen($msg));	
	}
	
	public function readFrame(){
		$data = socket_read($this->socket,2048,PHP_BINARY_READ);
		
		return WebSocketFrame::decode($data);
	}
	
	public function readMessage(){
		$msg = WebSocketMessage::fromFrame($this->readFrame());
		
		while($msg->isFinalised() == false)
			$msg->takeFrame($this->readMessage());
		
		return $msg;
	}
	
	public function close(){
		socket_close($this->socket);
	}

}