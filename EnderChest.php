<?php

/*
__PocketMine Plugin__
name=EnderChest
description=Every player has personal, universal chest.
version=Dev 0.1
author=Darunia18 & I_Is_Payton_
class=EnderChest
apiversion=10,11,12
*/

class EnderChest implements Plugin {
		public $name;
		public $normal;
		public $id;
		public $x;
		public $y;
		public $z;
		public $data;
		public $class;
		public $attach;
		public $metadata;
		public $closed;
		private $server;
        
        public function __construct(ServerAPI $api, $server = false) {
                $this->api = $api;
        }

                public function init(){
	function __construct(Level $level, $id, $class, $x, $y, $z, $data = array()){
		$this->server = ServerAPI::request();
		$this->level = $level;
		$this->normal = true;
		$this->class = $class;
		$this->data = $data;
		$this->closed = false;
		if($class === false){
			$this->closed = true;
		}
		$this->name = "";
		$this->lastUpdate = microtime(true);
		$this->scheduledUpdate = false;
		$this->id = (int) $id;
		$this->x = (int) $x;
		$this->y = (int) $y;
		$this->z = (int) $z;
		$this->server->query("INSERT OR REPLACE INTO tiles (ID, level, class, x, y, z) VALUES (".$this->id.", '".$this->level->getName()."', '".$this->class."', ".$this->x.", ".$this->y.", ".$this->z.");");
		switch($this->class){
			case TILE_CHEST:
				$this->server->query("UPDATE tiles SET spawnable = 1 WHERE ID = ".$this->id.";");
				break;
		}
	}
}
	
	public function isPaired(){
		if($this->class !== TILE_CHEST){
			return false;
		}
		if(!isset($this->data["pairx"]) or !isset($this->data["pairz"])){
			return false;
		}
		return true;
	}
	
	public function getPair(){
		if($this->isPaired()){
			return $this->server->api->tile->get(new Position((int) $this->data["pairx"], $this->y, (int) $this->data["pairz"], $this->level));
		}
		return false;
	}
	
	public function pairWith(Tile $tile){
		if($this->isPaired()or $tile->isPaired()){
			return false;
		}
		
		$this->data["pairx"] = $tile->x;
		$this->data["pairz"] = $tile->z;
		
		$tile->data["pairx"] = $this->x;
		$tile->data["pairz"] = $this->z;
		
		$this->server->api->tile->spawnToAll($this);
		$this->server->api->tile->spawnToAll($tile);
		$this->server->handle("tile.update", $this);
		$this->server->handle("tile.update", $tile);
	}
	
	public function unpair(){
		if(!$this->isPaired()){
			return false;
		}
		
		$tile = $this->getPair();
		unset($this->data["pairx"], $this->data["pairz"], $tile->data["pairx"], $tile->data["pairz"]);
		
		$this->server->api->tile->spawnToAll($this);
		$this->server->handle("tile.update", $this);
		if($tile instanceof Tile){
			$this->server->api->tile->spawnToAll($tile);
			$this->server->handle("tile.update", $tile);
		}
	}
	
	public function openInventory(Player $player){
		if($this->class === TILE_CHEST){
			$player->windowCnt++;
			$player->windowCnt = $id = max(2, $player->windowCnt % 99);
			if(($pair = $this->getPair()) !== false){				
				if(($pair->x + ($pair->z << 13)) > ($this->x + ($this->z << 13))){ //Order them correctly
					$player->windows[$id] = array(
						$pair,
						$this
					);
				}else{
					$player->windows[$id] = array(
						$this,
						$pair
					);
				}
			}else{
				$player->windows[$id] = $this;
			}
			$player->dataPacket(MC_CONTAINER_OPEN, array(
				"windowid" => $id,
				"type" => WINDOW_CHEST,
				"slots" => is_array($player->windows[$id]) ? CHEST_SLOTS << 1:CHEST_SLOTS,
				"x" => $this->x,
				"y" => $this->y,
				"z" => $this->z,
			));
			$slots = array();
			
			if(is_array($player->windows[$id])){
				$all = $this->server->api->player->getAll($this->level);
				foreach($player->windows[$id] as $ob){
					$this->server->api->player->broadcastPacket($all, MC_TILE_EVENT, array(
						"x" => $ob->x,
						"y" => $ob->y,
						"z" => $ob->z,
						"case1" => 1,
						"case2" => 2,
					));
					for($s = 0; $s < CHEST_SLOTS; ++$s){
						$slot = $ob->getSlot($s);
						if($slot->getID() > AIR and $slot->count > 0){
							$slots[] = $slot;
						}else{
							$slots[] = BlockAPI::getItem(AIR, 0, 0);
						}
					}
				}
			}else{
				$this->server->api->player->broadcastPacket($this->server->api->player->getAll($this->level), MC_TILE_EVENT, array(
					"x" => $this->x,
					"y" => $this->y,
					"z" => $this->z,
					"case1" => 1,
					"case2" => 2,
				));
				for($s = 0; $s < CHEST_SLOTS; ++$s){
					$slot = $this->getSlot($s);
					if($slot->getID() > AIR and $slot->count > 0){
						$slots[] = $slot;
					}else{
						$slots[] = BlockAPI::getItem(AIR, 0, 0);
					}
				}
			}
	}
}

	//public function update(){
		//if($this->closed === true){
			//return false;
		//}	
				//$this->server->schedule(2, array($this, "update"));
				//$this->scheduledUpdate = true;
		//}
	
	public function getSlotIndex($s){
		if($this->class !== TILE_CHEST){
			return false;
		}
		foreach($this->data["Items"] as $i => $slot){
			if($slot["Slot"] === $s){
				return $i;
			}
		}
		return -1;
	}
	
	public function getSlot($s){
		$i = $this->getSlotIndex($s);
		if($i === false or $i < 0){
			return BlockAPI::getItem(AIR, 0, 0);
		}else{
			return BlockAPI::getItem($this->data["Items"][$i]["id"], $this->data["Items"][$i]["Damage"], $this->data["Items"][$i]["Count"]);
		}
	}
	
	public function setSlot($s, Item $item, $update = true, $offset = 0){
		$i = $this->getSlotIndex($s);
		$d = array(
			"Count" => $item->count,
			"Slot" => $s,
			"id" => $item->getID(),
			"Damage" => $item->getMetadata(),
		);
		if($i === false){
			return false;
		}elseif($item->getID() === AIR or $item->count <= 0){
			if($i >= 0){
				unset($this->data["Items"][$i]);
			}
		}elseif($i < 0){
			$this->data["Items"][] = $d;
		}else{
			$this->data["Items"][$i] = $d;
		}
		$this->server->api->dhandle("tile.container.slot", array(
			"tile" => $this,
			"slot" => $s,
			"offset" => $offset,
			"slotdata" => $item,
		));

		if($update === true and $this->scheduledUpdate === false){
			$this->update();
		}
		return true;
	}

	public function spawn($player){
		if($this->closed){
			return false;
		}
		if(!($player instanceof Player)){
			$player = $this->server->api->player->get($player);
		}
		switch($this->class){
			case TILE_CHEST:
				$nbt = new NBT();
				$nbt->write(chr(NBT::TAG_COMPOUND)."\x00\x00");
				
				$nbt->write(chr(NBT::TAG_STRING));
				$nbt->writeTAG_String("id");
				$nbt->writeTAG_String($this->class);
				
				$nbt->write(chr(NBT::TAG_INT));
				$nbt->writeTAG_String("x");
				$nbt->writeTAG_Int((int) $this->x);
				
				$nbt->write(chr(NBT::TAG_INT));
				$nbt->writeTAG_String("y");
				$nbt->writeTAG_Int((int) $this->y);
				
				$nbt->write(chr(NBT::TAG_INT));
				$nbt->writeTAG_String("z");
				$nbt->writeTAG_Int((int) $this->z);
				
				if($this->isPaired()){
					$nbt->write(chr(NBT::TAG_INT));
					$nbt->writeTAG_String("pairx");
					$nbt->writeTAG_Int((int) $this->data["pairx"]);
					
					$nbt->write(chr(NBT::TAG_INT));
					$nbt->writeTAG_String("pairz");
					$nbt->writeTAG_Int((int) $this->data["pairz"]);
				}
				
				$nbt->write(chr(NBT::TAG_END));				
				
				$player->dataPacket(MC_ENTITY_DATA, array(
					"x" => $this->x,
					"y" => $this->y,
					"z" => $this->z,
					"namedtag" => $nbt->binary,
				));
				break;
		}
	}
	
	public function close(){
		if($this->closed === false){
			$this->closed = true;
			$this->server->api->tile->remove($this->id);
		}
	}

	public function __destruct(){
		$this->close();
	}

	public function getName(){
		return $this->name;
	}


	public function setPosition(Vector3 $pos){
		if($pos instanceof Position){
			$this->level = $pos->level;
			$this->server->query("UPDATE tiles SET level = '".$this->level->getName()."' WHERE ID = ".$this->id.";");
		}
		$this->x = (int) $pos->x;
		$this->y = (int) $pos->y;
		$this->z = (int) $pos->z;
		$this->server->query("UPDATE tiles SET x = ".$this->x.", y = ".$this->y.", z = ".$this->z." WHERE ID = ".$this->id.";");
	}
}
?>
