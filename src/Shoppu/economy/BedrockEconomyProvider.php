<?php

declare(strict_types=1);

namespace Shoppu\economy;

use cooldogedev\BedrockEconomy\api\BedrockEconomyAPI;
use cooldogedev\BedrockEconomy\api\type\ClosureAPI;
use pocketmine\player\Player;

class BedrockEconomyProvider implements EconomyProvider{

    private ?ClosureAPI $api = null;

    public function __construct(){
        $this->api = BedrockEconomyAPI::CLOSURE();
    }

    public function getMoney(Player $player, callable $callback): void{
        $this->api->get(
            xuid: $player->getXuid(),
            username: $player->getName(),
            onSuccess: static function(array $result) use ($callback): void{
                $balance = (float) $result["amount"] + (float) $result["decimals"] / 100;
                $callback($balance);
            },
            onError: static function() use ($callback): void{
                $callback(0.0);
            }
        );
    }

    public function addMoney(Player $player, float $amount, callable $callback): void{
        [$integer, $decimal] = self::floatToBedrock($amount);
        $this->api->add(
            xuid: $player->getXuid(),
            username: $player->getName(),
            amount: $integer,
            decimals: $decimal,
            onSuccess: static function() use ($callback): void{
                $callback(true);
            },
            onError: static function() use ($callback): void{
                $callback(false);
            }
        );
    }

    public function subtractMoney(Player $player, float $amount, callable $callback): void{
        [$integer, $decimal] = self::floatToBedrock($amount);
        $this->api->subtract(
            xuid: $player->getXuid(),
            username: $player->getName(),
            amount: $integer,
            decimals: $decimal,
            onSuccess: static function() use ($callback): void{
                $callback(true);
            },
            onError: static function() use ($callback): void{
                $callback(false);
            }
        );
    }

    public function getCurrencySymbol(): string{
        return "$";
    }

    private static function floatToBedrock(float $value): array{
        $amount = (int) floor($value);
        $decimals = (int) round(($value - $amount) * 100);
        return [$amount, $decimals];
    }
}
