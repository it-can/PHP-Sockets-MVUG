<?php
class WebSocketMessageNotFinalised extends Exception{
	public function __construct(IWebSocketMessage $msg){
		parent::__construct("WebSocketMessage is not finalised!");
	}
}

class WebSocketFrameSizeMismatch extends Exception{
	public function __construct(IWebSocketFrame $msg){
		parent::__construct("Frame size mismatches with the expected frame size. Maybe a buggy client.");
	}
}

class WebSocketInvalidChallengeResponse extends Exception{
	public function __construct(){
		parent::__construct("Server send an incorrect response to the clients challenge!");
	}
}

class WebSocketInvalidUrlScheme extends Exception{
	public function __construct(){
		parent::__construct("Only 'ws://' urls are supported!");
	}
}