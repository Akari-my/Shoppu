<?php

declare(strict_types=1);

namespace Shoppu\data;

class JSONProvider implements DataProvider{

    private array $data = [];
    private string $file;

    public function __construct(string $dataFolder){
        $this->file = $dataFolder . "shop.json";
        $this->loadAll();
    }

    public function loadAll(): array{
        if(file_exists($this->file)){
            $content = file_get_contents($this->file);
            if($content !== false){
                $this->data = json_decode($content, true) ?? [];
            }
        }
        return $this->data;
    }

    public function saveAll(array $data): void{
        $this->data = $data;
        $json = json_encode($this->data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        if($json === false){
            return;
        }
        $tmp = $this->file . ".tmp";
        file_put_contents($tmp, $json);
        rename($tmp, $this->file);
    }
}
