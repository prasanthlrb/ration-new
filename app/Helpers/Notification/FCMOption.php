<?php

namespace App\Helpers\Notification;


class FCMOption
{

    public string $title;
    public ?string $body = null;
    public ?string $token = null;
    public ?array $tokens = null;
    public ?array $data = null;


    public function getRegistrationIds(): array|string|null
    {
        if ($this->token != null) {
            return [$this->token];
        } else if ($this->tokens != null) {
            return $this->tokens;
        } else {
            return null;
        }
    }


    public function is_valid(): bool
    {

        return $this->token != null || ($this->tokens != null && sizeof($this->tokens) != 0);
    }

}