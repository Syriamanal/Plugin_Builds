<?php

/*
__PocketMine Plugin__
name=FoodGive
description=Gives food. :P
version=0.0.1
author=I_Is_Payton_
class=FoodGive
apiversion=10,11,12,13,14,15,16
*/

/*
TODO List:
1.) Add rand() option to give random items listed in a json file.
2.) Optional SQL secure DB integration.
3.) Find an alternative way to close cmd "food".
*/

class FoodGive implements Plugin {
private $api;
public function __construct(ServerAPI $api, $server = false) {
$this->api = $api;
}

public function init() {
$this->api->console->register("food","Gives food to the issuer.",array($this, "food"));
$this->api->console->register("foodon","Turns food cmd on.",array($this, "foodon"));
$this->api->console->register("foodoff","Turns food cmd off.",array($this, "foodoff"));

$this->items = new Config($this->api->plugin->configPath($this) . "ranks.yml",CONFIG_YAML,array("0" => "o","1" => "a","2" => "t","3" => "g","4" => "6"));
}

public function food($args , $user , $issuer) {

$user = $issuer->username;
$EOT = array($this->api->console->run("give $user steak 64")); //edit the number to give whatever food you want.
$EOI = $this->api->chat->sendTo(false, "Enjoy!", $user);

if (!$issuer Instanceof Player){
    $output = "Please run this command in game.";
     return $output;
}
}

public function foodon($args , $user , $issuer) {

	$user = $issuer->username;
    $IGA = array($this->items->get("0"),$this->items->get("1"),$this->items->get("2"),$this->items->get("3"),$this->items->get("4"));
    $ItemGive = $IGA;
    if($ItemGive == "0") {
    	$this->api->console->run("foodon");
    }else{
    	$this->api->console->run("ppplayer food $ItemGive");
    	$this->api->chat->broadcast("$user turned on the command /food.");
    }
}

public function foodoff($args , $user , $issuer) {

	$user = $issuer->username;
	$this->api->console->run("ppplayer food");
	$this->api->chat->broadcast("$user turned off the command /food.");
  }

public function __destruct(){}

}
?>
