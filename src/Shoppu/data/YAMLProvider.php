<?php

declare(strict_types=1);

namespace Shoppu\data;

use pocketmine\utils\Config;

class YAMLProvider implements DataProvider{

    private Config $config;

    public function __construct(string $dataFolder){
        $this->config = new Config($dataFolder . "shop.yml", Config::YAML);
    }

    public function loadAll(): array{
        return $this->config->getAll();
    }

    public function saveAll(array $data): void{
        $this->config->setAll($data);
        $this->config->save();
    }
}
