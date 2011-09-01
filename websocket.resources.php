<?php
interface IWebSocketResourceHandler{
	public function addUser(WebSocketUser $user);
	public function removeUser(WebSocketUser $user);
	public function onMessage(WebSocketUser $user, IWebSocketMessage $msg);
	public function onAdminMessage(WebSocketAdminUser $user, stdClass $msg);
	
	public function setServer(WebSocketServer $server);
}


abstract class WebSocketResourceHandler implements IWebSocketResourceHandler{
	
	/**
	 * 
	 * Enter description here ...
	 * @var SplObjectStorage
	 */
	protected $users;
	
	/**
	 * 
	 * Enter description here ...
	 * @var WebSocketServer
	 */
	protected $server;
	
	public function __construct(){
		$this->users = new SplObjectStorage();
	}
	
	public function addUser(WebSocketUser $user){
		$this->users->attach($user);
	}
	
	public function removeUser(WebSocketUser $user){
		$this->users->detach($user);
	}
	
	public function setServer(WebSocketServer $server){
		$this->server = $server;
	}
	
	public function say($msg =''){
		return $this->server->say($msg);
	}
	
	public function send(WebSocketUser $client, $str){
		return $this->server->send($client, $str);
	}
	
	public function onMessage(WebSocketUser $user, IWebSocketMessage $msg){}
	public function onAdminMessage(WebSocketAdminUser $user, stdClass $msg){}
	
	//abstract public function onMessage(WebSocketUser $user, IWebSocketMessage $msg);
} 