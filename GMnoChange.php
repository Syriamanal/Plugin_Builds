<?php

/*
__PocketMine Plugin__
name=GMnoKick
description=Gamemode change, No kick!
version=0.0.1
author=I_Is_Payton_
class=Gamemode
apiversion=10,11,12,13,14,15,16
*/

/*
Please keep in mind that i am finding a new way to change gamemode so this doesn't work At the moment.
*/

class Gamemode implements Plugin {
private $api;
public function __construct(ServerAPI $api, $server = false) {
$this->api = $api;
}

public function init() {
$this->api->console->register("s","Changes you to survival.",array($this, "s"));
$this->api->console->register("c","Changes you to creative.",array($this, "c"));
}

public function s($args , $user , $issuer) {
	$user = $issuer->username;
	$gms = "survival";
	if(!$issuer Instanceof Player){
		$output = "Please run this command in game.";
		return $output;
	}else{
		$issuer->setGamemode("$gms", $user);
		$this->api->chat->sendTo(false, "Gamemode changed to survival.", $issuer);
	}
}

	public function c($args , $user , $issuer) {
		$user = $issuer->username;
		$gmc = "creative";
		if(!$issuer Instanceof Player){
			// safety measures
			$input = "Please run this command in game.";
			return $input;
		}else{
			$issuer->setGamemode("$gmc", $issuer);
			$this->api->chat->sendTo(false, "Gamemode changed to creative.", $issuer);
		}
	}
	 public function __destruct(){}
}
?>
