<?php

namespace Shoppu\libs\FormX;

use Closure;
use pocketmine\form\Form;
use pocketmine\player\Player;

class ModalForm implements Form {

    private Closure $callback;
    private string $title = "";
    private string $content = "";
    private string $yesButton = "";
    private string $noButton = "";

    public function __construct(Closure $callback) {
        $this->callback = $callback;
    }

    public function setTitle(string $title): void {
        $this->title = $title;
    }

    public function setContent(string $content): void {
        $this->content = $content;
    }

    public function setYesButton(string $text): void {
        $this->yesButton = $text;
    }

    public function setNoButton(string $text): void {
        $this->noButton = $text;
    }

    public function sendTo(Player $player): void {
        $player->sendForm($this);
    }

    public function handleResponse(Player $player, $data): void {
        if (!is_bool($data)) return;
        ($this->callback)($player, $data);
    }

    public function jsonSerialize(): mixed {
        return [
            'type' => 'modal',
            'title' => $this->title,
            'content' => $this->content,
            'button1' => $this->yesButton,
            'button2' => $this->noButton
        ];
    }
}
