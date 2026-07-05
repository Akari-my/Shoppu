<?php

declare(strict_types=1);

namespace Shoppu\data;

interface DataProvider{

    public function loadAll(): array;

    public function saveAll(array $data): void;
}
