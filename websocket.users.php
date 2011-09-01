<?php

/**
 * TODO: Getters and setters
 *
 * @author Chris
 *
 */
class WebSocketUser{
	var $id;
	
	var $socket;
	
	var $resource;
	
	var $handshake = false;
	var $protocol = 0;
	
	/**
	 * 
	 * Enter description here ...
	 * @var WebSocketMessage
	 */
	var $message;
	
	public function createMessage($data){
		if($this->protocol == 0)
			return WebSocketMessage76::create($data);
		else return WebSocketMessage::create($data);
	}
}

class WebSocketAdminUser extends WebSocketUser{
	public function __construct(){
		//$this->handshake = true;
	}
}