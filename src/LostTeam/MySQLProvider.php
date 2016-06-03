<?php
namespace LostTeam;

use LostTeam\task\EconomySMySQLTask;
use onebone\economyapi\EconomyAPI;

use pocketmine\Player;

class MySQLProvider implements ProviderTemplate {
    /*
     * @var \mysqli $db
     * @var EconomyAPI $plugin
     * @var EconomySMySQL $SQLplugin
     */
    private $db, $plugin, $SQLplugin;
    private $groupsData = [];
    public function __construct(EconomyAPI $plugin) {
        $this->plugin = $plugin;
        $this->SQLplugin = $this->plugin->getServer()->getPluginManager()->getPlugin("EconomySMySQL");
        $mySQLSettings = $this->SQLplugin->getConfig()->getNested("mysql-settings");
        if(!isset($mySQLSettings["host"]) || !isset($mySQLSettings["port"]) || !isset($mySQLSettings["user"]) || !isset($mySQLSettings["password"]) || !isset($mySQLSettings["db"]))
            throw new \RuntimeException("Failed to connect to the MySQLi database: Invalid MySQL settings");
        $this->db = new \mysqli($mySQLSettings["host"], $mySQLSettings["user"], $mySQLSettings["password"], $mySQLSettings["db"], $mySQLSettings["port"]);
        if($this->db->connect_error)
            throw new \RuntimeException("Failed to connect to the MySQLi database: " . $this->db->connect_error);
        $resource = $this->plugin->getResource("mysql_deploy_01.sql");
        $this->db->multi_query(stream_get_contents($resource));
        while($this->db->more_results()) {
            $this->db->next_result();
        }
        fclose($resource);
        $this->loadMoneyData();
        $this->plugin->getServer()->getScheduler()->scheduleRepeatingTask(new EconomySMySQLTask($this->plugin, $this->db), 1200);
    }

    public function loadMoneyData() {
        $result01 = $this->db->query("SELECT * FROM money");

        if($result01 instanceof \mysqli_result)
        {
            if($result01->num_rows <= 0)
            {
                $this->plugin->getLogger()->notice("No groups found in table 'money', loading groups defined in default SQL script");

                $resource = $this->SQLplugin->getResource("mysql_deploy_02.sql");

                $this->db->multi_query(stream_get_contents($resource));

                while($this->db->more_results())
                {
                    $this->db->next_result();
                }

                fclose($resource);

                $result01 = $this->db->query("SELECT * FROM money");
            }

            while($currentRow = $result01->fetch_assoc())
            {
                $userName = $currentRow["userName"];

                $this->groupsData[$userName]["cash"] = $currentRow["cash"];
            }

            $result01->free();
        }
    }

    /**
     * @param Player $player
     * @return bool
     */
    public function accountExists(Player $player) {
        $playerName = $player->getName();
        $result = $this->db->query("SELECT * FROM money WHERE userName='$playerName'");
        return $result->num_rows > 0 ? true:false;
    }

    /**
     * @param Player $player
     * @param integer $defaultMoney
     * @return void
     */
    public function createAccount(Player $player, $defaultMoney = 1000) {
        $playerName = $player->getName();
        if(!$this->accountExists($player)) {
            $this->db->query("INSERT INTO money (userName, cash) VALUES ('$playerName', $defaultMoney)");
        }
        return;
    }

    /**
     * @param Player $player
     * @return void
     */
    public function removeAccount(Player $player) {
        $playerName = $player->getName();
        if($this->accountExists($player)) {
            $this->db->query("DELETE * FROM money where userName='$playerName'");
        }
        return;
    }

    /**
     * @param Player $player
     * @return void
     */
    public function getMoney(Player $player) {
        $playerName = $player->getName();
        if($this->accountExists($player)) {
            $this->db->query("SELECT cash FROM money WHERE userName='$playerName'");
        }
        return;
    }

    /**
     * @param Player $player
     * @param integer $amount
     * @return void
     */
    public function addMoney(Player $player, $amount) {
        $playerName = $player->getName();
        if($this->accountExists($player)) {
            $cash = $this->db->query("SELECT cash FROM money WHERE userName='$playerName'")->fetch_array(MYSQLI_ASSOC);
            $cash = $cash["cash"]+$amount;
            $this->db->query("UPDATE money SET cash= $cash WHERE userName='$playerName'");
        }
        return;
    }

    /**
     * @param Player $player
     * @param integer $amount
     * @return void
     */
    public function reduceMoney(Player $player, $amount) {
        $playerName = $player->getName();
        if($this->accountExists($player)) {
            $cash = $this->db->query("SELECT cash FROM money WHERE userName='$playerName'")->fetch_array(MYSQLI_ASSOC);
            $cash = $cash["cash"]-$amount;
            $this->db->query("UPDATE money SET cash= $cash WHERE userName='$playerName'");
        }
        return;
    }

    /**
     * @param Player $player
     * @param integer $amount
     * @return void
     */
    public function setMoney(Player $player, $amount) {
        $playerName = $player->getName();
        if($this->accountExists($player)) {
            $this->db->query("UPDATE money SET cash={$amount} WHERE userName='$playerName'");
        }
        return;
    }

    /**
     * @return array
     */
    public function getAll() {
        return $this->db->query("SELECT * FROM money")->fetch_array(MYSQLI_ASSOC);
    }

    /**
     * @return string
     */
    public function getName() {
        return "MySQL";
    }

    /**
     *@return void
     */
    public function save() {
        return;
    }

    /**
     *@return void
     */
    public function close() {
        $this->db->close();
        return;
    }
}
