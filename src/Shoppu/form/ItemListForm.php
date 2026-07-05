<?php

declare(strict_types=1);

namespace Shoppu\form;

use pocketmine\player\Player;
use Shoppu\libs\FormX\SimpleForm;
use Shoppu\manager\LangManager;
use Shoppu\manager\ShopManager;

class ItemListForm extends SimpleForm{

    public function __construct(ShopManager $manager, string $categoryId){
        $items = $manager->getCategoryItems($categoryId);
        if($items === null){
            $items = [];
        }
        $ids = array_keys($items);
        $itemCount = count($ids);

        parent::__construct(function(Player $player, int $data) use ($manager, $categoryId, $ids, $itemCount): void{
            if($data === $itemCount){
                (new CategoryListForm($manager))->sendTo($player);
                return;
            }
            if(!isset($ids[$data])){
                return;
            }
            (new QuantityForm($manager, $categoryId, $ids[$data], fn() => (new ItemListForm($manager, $categoryId))->sendTo($player)))->sendTo($player);
        });

        $this->setTitle(LangManager::raw("ui-item-title", ["category" => $manager->getCategoryName($categoryId)]));
        $this->setContent(LangManager::raw("ui-item-content"));

        foreach($items as $id => $itemData){
            $item = $manager->getItem($categoryId, $id);
            if($item === null){
                continue;
            }
            $itemName = $item["item"]->getName();
            $text = LangManager::raw("ui-item-name", ["name" => $itemName]);
            if($item["buy"] !== null){
                $text .= "\n" . LangManager::raw("ui-item-buy", ["price" => $item["buy"]]);
            }
            if($item["sell"] !== null){
                $text .= LangManager::raw("ui-item-sell", ["price" => $item["sell"]]);
            }
            $this->addButton($text);
        }

        $this->addButton(LangManager::raw("ui-back"));
    }
}
