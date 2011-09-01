<?php

/**
 * Helper class to send Admin Messages to the WebSocketServer
 * 
 * Makes the server execute onAdminXXXX() events
 * 
 * @author Chris
 *
 */
class WebSocketAdminMessage extends stdClass{
	public $task = null;
	
	private function __construct(){
	
	}
	
	/**
	 * Create a message that will be send to the instance of the WebSocketServer
	 *
	 * @param string $task
	 * @return WebSocketAdminMessage
	 */
	public static function createGlobalMessage($task){
		$o = new self();
		$o->task = $task;
		
		return $o;
	}
	
	/**
	 * Create a message that will be send to a specific IWebSocketResourceHandler of the WebSocketServer
	 *
	 * @param string $resource
	 * @param string $task
	 * @return WebSocketAdminMessage
	 */
	public static function createMessage($resource, $task){
		$o = new self();
		$o->resource = $resource;
		$o->task = $task;
		
		return $o;
	}
	
	/**
	 * Send the message to a specific server
	 * 
	 * @todo only 127.0.0.1 supported, since no admin authentication yet!
	 * @param string $host
	 * @param int $port
	 */
	public function send($host = '127.0.0.1', $port = 12345){
		$socket=socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
		socket_connect($socket, $host, $port);
		
		$msg = json_encode($this);
		
		socket_write($socket, $msg, strlen($msg));
		socket_close($socket);
	}
}