<?php
namespace LostTeam;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat as TF;

class EconomySMySQL extends PluginBase {
  public function onEnable() {
    $this->getLogger()->notice(TF::GREEN."Enabled!");
  }
  public function onDisable() {
    $this->getLogger()->notice(TF::GREEN."Disabled!");
  }
}
