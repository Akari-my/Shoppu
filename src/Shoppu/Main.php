<?php

declare(strict_types=1);

namespace Shoppu;

use pocketmine\plugin\PluginBase;
use Shoppu\command\ShopCommand;
use Shoppu\data\DataProvider;
use Shoppu\data\JSONProvider;
use Shoppu\data\YAMLProvider;
use Shoppu\economy\BedrockEconomyProvider;
use Shoppu\economy\EconomyProvider;
use Shoppu\manager\LangManager;
use Shoppu\manager\ShopManager;

class Main extends PluginBase{

    private ShopManager $shopManager;
    private DataProvider $dataProvider;
    private EconomyProvider $economy;

    protected function onEnable(): void{
        $this->saveDefaultConfig();
        $this->saveResource("shop.yml");
        foreach(["eng", "ita", "fra", "deu", "esp", "por", "nld", "swe", "pol", "tur", "rus", "ukr", "jpn", "kor", "zho", "vie"] as $lang){
            $this->saveResource("lang/$lang.yml");
        }
        LangManager::init($this);

        if($this->getServer()->getPluginManager()->getPlugin("BedrockEconomy") === null){
            $this->getLogger()->critical("BedrockEconomy not found. Disabling plugin.");
            $this->getServer()->getPluginManager()->disablePlugin($this);
            return;
        }

        $this->economy = $this->createEconomyProvider();

        $storage = strtolower($this->getConfig()->get("storage", "yaml"));
        $this->dataProvider = match($storage){
            "json" => new JSONProvider($this->getDataFolder()),
            default => new YAMLProvider($this->getDataFolder()),
        };

        $this->shopManager = new ShopManager($this->dataProvider, $this->economy);

        $this->getServer()->getCommandMap()->register("shoppu", new ShopCommand($this, $this->shopManager));
    }

    protected function onDisable(): void{
        $this->shopManager->save();
    }

    private function createEconomyProvider(): EconomyProvider{
        $economyType = strtolower($this->getConfig()->getNested("economy.type", "bedrockeconomy"));

        return match($economyType){
            "bedrockeconomy" => new BedrockEconomyProvider(),
            default => throw new \InvalidArgumentException("Unsupported economy type: " . $economyType),
        };
    }
}
