<?php
namespace LostTeam;

use LosTeam\DataParser;
use onebone\economyapi\EconomyAPI;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat as TF;

class EconomySMySQL extends PluginBase {
  public function onEnable() {
    if(strtolower($this->getConfig()->get("Host"))) {
      $time = $this->getConfig()->get("check-time");
    }
    $this->getServer()->getPluginManager()->registerEvents(new DataParser($this,new MySQLProvider(EconomyAPI::getInstance())), $this);
    $this->getLogger()->notice(TF::GREEN."Enabled!");
  }
  public function onDisable() {
    $this->getLogger()->notice(TF::GREEN."Disabled!");
  }
  
}
