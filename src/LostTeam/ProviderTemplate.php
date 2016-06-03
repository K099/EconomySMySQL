<?php
/*
 * EconomyS, the massive economy plugin with many features for PocketMine-MP
 * Copyright (C) 2013-2016  onebone <jyc00410@gmail.com>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace LostTeam;

use onebone\economyapi\EconomyAPI;
use pocketmine\Player;

interface ProviderTemplate {
    public function __construct(EconomyAPI $plugin);
    /**
     * @param Player $player
     * @return bool
     */
    public function accountExists(Player $player);
    /**
     * @param Player $player
     * @param integer $defaultMoney
     * @return bool
     */
    public function createAccount(Player $player, $defaultMoney = 1000);
    /**
     * @param Player $player
     * @return bool
     */
    public function removeAccount(Player $player);
    /**
     * @param Player $player
     * @return integer
     */
    public function getMoney(Player $player);
    /**
     * @param Player $player
     * @param integer $amount
     * @return bool
     */
    public function setMoney(Player $player, $amount);
    /**
     * @param Player $player
     * @param integer $amount
     * @return bool
     */
    public function addMoney(Player $player, $amount);
    /**
     * @param Player $player
     * @param integer $amount
     * @return bool
     */
    public function reduceMoney(Player $player, $amount);
    /**
     * @return array
     */
    public function getAll();
    /**
     * @return string
     */
    public function getName();
    /**
     * @return bool
     */
    public function save();
    /**
     * @return bool
     */
    public function close();
}