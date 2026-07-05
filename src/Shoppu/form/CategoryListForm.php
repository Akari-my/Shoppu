<?php

declare(strict_types=1);

namespace Shoppu\form;

use pocketmine\block\StringToBlockParser;
use pocketmine\item\ItemBlock;
use pocketmine\item\StringToItemParser;
use pocketmine\player\Player;
use Shoppu\libs\FormX\SimpleForm;
use Shoppu\manager\LangManager;
use Shoppu\manager\ShopManager;

class CategoryListForm extends SimpleForm{

    public function __construct(ShopManager $manager){
        $categories = $manager->getCategories();
        $ids = array_keys($categories);
        parent::__construct(function(Player $player, int $data) use ($manager, $ids): void{
            if(!isset($ids[$data])){
                return;
            }
            (new ItemListForm($manager, $ids[$data]))->sendTo($player);
        });
        $this->setTitle(LangManager::raw("ui-category-title"));
        $this->setContent(LangManager::raw("ui-category-content"));
        foreach($categories as $category){
            $icon = $category["icon"] ?? null;
            if($icon !== null){
                $this->addButton(LangManager::raw("ui-category-button", ["name" => $category["name"]]), "path", self::getTexturePath($icon));
            }else{
                $this->addButton(LangManager::raw("ui-category-button", ["name" => $category["name"]]));
            }
        }
    }

    private static function getTexturePath(string $icon): string{
        if(str_contains($icon, "/")){
            return $icon;
        }

        $itemParser = StringToItemParser::getInstance();
        $item = $itemParser->parse($icon) ?? $itemParser->parse("minecraft:" . $icon);
        if($item !== null){
            return "textures/" . ($item instanceof ItemBlock ? "blocks" : "items") . "/" . $icon;
        }

        $blockParser = StringToBlockParser::getInstance();
        if($blockParser->parse($icon) !== null || $blockParser->parse("minecraft:" . $icon) !== null){
            return "textures/blocks/" . $icon;
        }

        return "textures/items/" . $icon;
    }
}
