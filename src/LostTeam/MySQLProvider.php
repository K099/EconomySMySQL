<?php
namespace LostTeam;

use LostTeam\task\EconomySMySQLTask;
use onebone\economyapi\EconomyAPI;

class MySQLProvider {
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

                $result01 = $this->db->query("SELECT * FROM money;");
            }

            while($currentRow = $result01->fetch_assoc())
            {
                $userName = $currentRow["userName"];

                $this->groupsData[$userName]["cash"] = $currentRow["cash"];
            }

            $result01->free();
        }

        $result02 = $this->db->query("SELECT * FROM money_mw;");

        if($result02 instanceof \mysqli_result)
        {
            while($currentRow = $result02->fetch_assoc())
            {
                $userName = $currentRow["userName"];
                $worldName = $currentRow["worldName"];
                $usercash = explode(",", $currentRow["cash"]);

                $this->groupsData[$userName]["worlds"][$worldName]["cash"] = $usercash;
            }

            $result02->free();
        }
    }
    public function close() {
        $this->db->close();
    }
}
