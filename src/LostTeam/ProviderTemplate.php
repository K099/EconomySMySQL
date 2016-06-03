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
     * @param \pocketmine\Player|string $player
     * @return bool
     */
    public function accountExists(Player $player);
    /**
     * @param \pocketmine\Player|string $player
     * @param float $defaultMoney
     * @return bool
     */
    public function createAccount(Player $player, $defaultMoney = 1000);
    /**
     * @param \pocketmine\Player|string $player
     * @return bool
     */
    public function removeAccount(Player $player);
    /**
     * @param string $player
     * @return float|bool
     */
    public function getMoney(Player $player);
    /**
     * @param \pocketmine\Player|string $player
     * @param float $amount
     * @return bool
     */
    public function setMoney(Player $player, $amount);
    /**
     * @param \pocketmine\Player|string $player
     * @param float $amount
     * @return bool
     */
    public function addMoney(Player $player, $amount);
    /**
     * @param \pocketmine\Player|string $player
     * @param float $amount
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
    public function save();
    public function close();
}