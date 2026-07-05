<?php

declare(strict_types=1);

namespace Shoppu\economy;

use pocketmine\player\Player;

interface EconomyProvider{

    public function getMoney(Player $player, callable $callback): void;

    public function addMoney(Player $player, float $amount, callable $callback): void;

    public function subtractMoney(Player $player, float $amount, callable $callback): void;

    public function getCurrencySymbol(): string;
}
