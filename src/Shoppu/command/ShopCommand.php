<?php

declare(strict_types=1);

namespace Shoppu\command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\plugin\PluginOwned;
use pocketmine\plugin\Plugin;
use Shoppu\form\CategoryListForm;
use Shoppu\Main;
use Shoppu\manager\LangManager;
use Shoppu\manager\ShopManager;

class ShopCommand extends Command implements PluginOwned{

    private Main $plugin;
    private ShopManager $manager;

    public function __construct(Main $plugin, ShopManager $manager){
        parent::__construct("shop", "Open the shop", "/shop");
        $this->setPermission("shoppu.command");
        $this->plugin = $plugin;
        $this->manager = $manager;
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void{
        if(!$sender instanceof Player){
            $sender->sendMessage(LangManager::get("error-in-game"));
            return;
        }

        if(!$this->testPermission($sender)){
            return;
        }

        (new CategoryListForm($this->manager))->sendTo($sender);
    }

    public function getOwningPlugin(): Plugin{
        return $this->plugin;
    }
}
