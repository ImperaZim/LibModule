<?php

declare(strict_types=1);

namespace easylibraryexamples\economy\modules\economy\api;

use pocketmine\player\Player;

interface EconomyApi {

    public function getBalance(Player|string $player): float;

    public function addMoney(Player|string $player, float $amount): void;

    public function takeMoney(Player|string $player, float $amount): bool;

    public function format(float $amount): string;
}
