<?php

/*
__PocketMine Plugin__
name=PocketCalc
description=Does math. (Just basic like + - / and *)
version=0.0.1
author=I_Is_Payton_
class=Math
apiversion=10,11,12,13,14,15,16,17,18,19,20
*/


class Math implements Plugin{
        private $api, $path, $config;
        public function __construct(ServerAPI $api, $server = false){
                $this->api = $api;
        }
        
        public function init(){
                $this->api->console->register("calc", "does your math homework! xD", array($this, "Calculator"));
        }
        
        public function __destruct(){}
        
        public function Calculator($cmd, $arg, $issuer) {
                
                switch($cmd) {
                        
                        case "calc":
                        
                         $firstValue = $arg[0];
                         $operator = $arg[1];
                         $secondValue = $arg[2];
                        
                         if (empty($arg[0]) || empty($arg[1]) || empty($arg[2])) {
                                 if (($issuer instanceof Player)) {
                                 $output = "[Calc] Usage: /calc <number one> <operation> <number two>";
                                            return $output;
                                 }
                                 else{
                                  $this->api->chat->sendTo(false, "[Calc] Usage: /calc <number one> <operation> <number two>", $issuer);
                                    break;
                                 }
                         }
                                 
                         elseif(!is_numeric($firstValue) || !is_numeric($secondValue)) {
                         if (($issuer instanceof Player)) {
                                  $this->api->chat->sendTo(false, "[Calculator] Usage: /calc <number one> <operation> <number two>", $issuer->username);
                                    break;
                         }
                         else{
                                  console("[Calc] Usage: /calc <number one> <operation> <number two>");
                                    break;
                         }        
                                 
                         }
                         else {
                                 
                                 switch($operator) {
                                         
                                         case "+":
                                          $result = $firstValue + $secondValue;
                                          if (($issuer instanceof Player)) {
                                                  $issuer->sendChat("The result is: $result");
                                break;
                                          }
                                          else {
                                                  console("The result is: $result");
                                                  break;
                                          } //done with +
                                          break;
                                         
                                         case "-":
                            $result = $firstValue - $secondValue;
                                          if (($issuer instanceof Player)) {
                                                  $issuer->sendChat("The result is: $result");
                                                        break;
                                          }
                                          else {
                                                  console("The result is: $result");
                                                  break;
                                          } //done with -
                                         
                                         case "*":
                            $result = $firstValue * $secondValue;
                                          if (($issuer instanceof Player)) {
                                                  $issuer->sendChat("The result is: $result");
                                                break;
                                          }
                                          else {
                                                  console("The result is: $result");
                                                  break;
                                          } //done with *
                                         
                                         case "/":
                                          $result = $firstValue / $secondValue;
                                          if (($issuer instanceof Player)) {
                                                  $issuer->sendChat("The result is: $result");
                                                break;
                                          }
                                          else {
                                                  console("The result is: $result");
                                                  break;
                                          } //done with /
                                         
                                         default:
                                          if (($issuer instanceof Player)) {
                                  $this->api->chat->sendTo(false, "[Calc] Usage: /calc <number one> <operation> <number two>", $issuer->username);
                                                break;
                         }
                         else{
                                  console("[Calc] Usage: /calc <number one> <operation> <number two>");
                                                break;
                         }        
                                 }
                                 
                         }        
                        
                }
                
        }

}

?>
