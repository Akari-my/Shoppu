<?php

declare(strict_types=1);

namespace Shoppu\form;

use pocketmine\player\Player;
use Shoppu\libs\FormX\CustomForm;
use Shoppu\manager\LangManager;
use Shoppu\manager\ShopManager;

class QuantityForm extends CustomForm{

    public function __construct(ShopManager $manager, string $categoryId, string $itemId, ?\Closure $onComplete = null){
        $itemData = $manager->getItem($categoryId, $itemId);
        if($itemData === null){
            parent::__construct(function(Player $player, array $data): void{});
            $this->setTitle(LangManager::raw("ui-error-title"));
            return;
        }

        $buyPrice = $itemData["buy"] ?? null;
        $sellPrice = $itemData["sell"] ?? null;
        $itemName = $itemData["item"]->getName();

        $offset = 0;
        if($buyPrice !== null){
            $this->addLabel(LangManager::raw("ui-quantity-buy-label", ["price" => $buyPrice]));
            $offset++;
        }
        if($sellPrice !== null){
            $this->addLabel(LangManager::raw("ui-quantity-sell-label", ["price" => $sellPrice]));
            $offset++;
        }

        $this->addDropdown(LangManager::raw("ui-quantity-action"), array_values(array_filter([$buyPrice !== null ? LangManager::raw("ui-quantity-action-buy") : null, $sellPrice !== null ? LangManager::raw("ui-quantity-action-sell") : null])));
        $this->addSlider(LangManager::raw("ui-quantity-slider"), 1, 64, 1, 1);

        parent::__construct(function(Player $player, array $data) use ($manager, $categoryId, $itemId, $buyPrice, $sellPrice, $offset, $onComplete): void{
            $actionIndex = (int) $data[$offset];
            $quantity = max(1, (int) $data[$offset + 1]);

            $options = [];
            if($buyPrice !== null){
                $options[] = "Buy";
            }
            if($sellPrice !== null){
                $options[] = "Sell";
            }

            if(!isset($options[$actionIndex])){
                return;
            }

            $action = $options[$actionIndex];

            $callback = function(bool $success, string $message) use ($player, $onComplete): void{
                $player->sendMessage($message);
                if($onComplete !== null){
                    $onComplete();
                }
            };

            if($action === "Buy"){
                $manager->buyItem($player, $categoryId, $itemId, $quantity, $callback);
            }else{
                $manager->sellItem($player, $categoryId, $itemId, $quantity, $callback);
            }
        });

        $this->setTitle(LangManager::raw("ui-quantity-title", ["name" => $itemName]));
    }
}
