<?php

declare(strict_types=1);

namespace Shoppu\manager;

use pocketmine\item\Item;
use pocketmine\item\StringToItemParser;
use pocketmine\player\Player;
use Shoppu\data\DataProvider;
use Shoppu\economy\EconomyProvider;
use Shoppu\manager\LangManager;

class ShopManager{

    private DataProvider $dataProvider;
    private EconomyProvider $economy;
    private array $categories = [];
    private array $resolvedItems = [];

    public function __construct(DataProvider $dataProvider, EconomyProvider $economy){
        $this->dataProvider = $dataProvider;
        $this->economy = $economy;
        $this->load();
    }

    public function load(): void{
        $data = $this->dataProvider->loadAll();
        $this->categories = $data["categories"] ?? [];
        $this->resolveItems();
    }

    public function save(): void{
        $this->dataProvider->saveAll(["categories" => $this->categories]);
    }

    public function getEconomy(): EconomyProvider{
        return $this->economy;
    }

    public function getCategories(): array{
        return $this->categories;
    }

    public function getCategoryItems(string $categoryId): ?array{
        return $this->categories[$categoryId]["items"] ?? null;
    }

    public function getItem(string $categoryId, string $itemId): ?array{
        $items = $this->getCategoryItems($categoryId);
        if($items === null){
            return null;
        }
        $itemData = $items[$itemId] ?? null;
        if($itemData === null){
            return null;
        }
        $resolved = $this->resolvedItems[$categoryId][$itemId] ?? null;
        if($resolved === null){
            return null;
        }
        return [
            "item" => $resolved,
            "buy" => $itemData["buy"] ?? null,
            "sell" => $itemData["sell"] ?? null,
        ];
    }

    public function getCategoryName(string $categoryId): string{
        return $this->categories[$categoryId]["name"] ?? $categoryId;
    }

    public function buyItem(Player $player, string $categoryId, string $itemId, int $quantity, callable $callback): void{
        $itemData = $this->getItem($categoryId, $itemId);
        if($itemData === null || $itemData["buy"] === null){
            $callback(false, LangManager::get("buy-unavailable"));
            return;
        }

        $price = (float) $itemData["buy"];
        $total = $price * $quantity;

        $this->economy->subtractMoney($player, $total, function(bool $success) use ($player, $itemData, $quantity, $total, $callback): void{
            if(!$success){
                $callback(false, LangManager::get("buy-insufficient-funds"));
                return;
            }
            $item = clone $itemData["item"];
            $item->setCount($quantity);
            $remaining = $player->getInventory()->addItem($item);
            if(count($remaining) > 0){
                $this->economy->addMoney($player, $total, function() use ($player, $remaining, $callback): void{
                    foreach($remaining as $leftover){
                        $player->getWorld()->dropItem($player->getPosition(), $leftover);
                    }
                    $callback(false, LangManager::get("buy-inventory-full"));
                });
                return;
            }
            $callback(true, LangManager::get("buy-success", ["item" => $itemData["item"]->getName(), "quantity" => $quantity, "total" => $total]));
        });
    }

    public function sellItem(Player $player, string $categoryId, string $itemId, int $quantity, callable $callback): void{
        $itemData = $this->getItem($categoryId, $itemId);
        if($itemData === null || $itemData["sell"] === null){
            $callback(false, LangManager::get("sell-unavailable"));
            return;
        }

        $price = (float) $itemData["sell"];
        $total = $price * $quantity;

        $item = clone $itemData["item"];
        $item->setCount($quantity);

        if(!$player->getInventory()->contains($item)){
            $callback(false, LangManager::get("sell-no-items"));
            return;
        }

        $player->getInventory()->removeItem($item);

        $this->economy->addMoney($player, $total, function(bool $success) use ($callback, $total, $itemData, $quantity): void{
            if(!$success){
                $callback(false, LangManager::get("sell-failed"));
                return;
            }
            $callback(true, LangManager::get("sell-success", ["item" => $itemData["item"]->getName(), "quantity" => $quantity, "total" => $total]));
        });
    }

    private function resolveItems(): void{
        $this->resolvedItems = [];
        $parser = StringToItemParser::getInstance();

        foreach($this->categories as $categoryId => &$category){
            $items = &$category["items"];
            foreach($items as $itemId => &$itemData){
                $resolved = $parser->parse($itemId);
                if($resolved === null){
                    $resolved = $parser->parse("minecraft:" . $itemId);
                }
                if($resolved !== null){
                    $this->resolvedItems[$categoryId][$itemId] = $resolved;
                }else{
                    unset($items[$itemId]);
                }
            }
        }
    }
}
