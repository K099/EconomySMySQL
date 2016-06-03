<?php
namespace LostTeam\task;

use onebone\economyapi\EconomyAPI;

use pocketmine\scheduler\PluginTask;

class EconomySMySQLTask extends PluginTask {
    
    /*
     * @var \mysqli $db
     */
    private $db, $plugin;

    /**
     * @param EconomyAPI $plugin
     * @param \mysqli $db
     */
    public function __construct(EconomyAPI $plugin, \mysqli $db) {
        parent::__construct($plugin);

        $this->plugin = $plugin;
        $this->db = $db;
    }
    public function onRun($currentTick) {
        if($this->db->ping())
        {
            $this->plugin->getLogger()->debug("Connected to MySQL Server");
        }
        else
        {
            $this->plugin->getLogger()->debug("[MySQL] Warning: " . $this->db->error);
        }
    }
}