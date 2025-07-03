<?php

namespace BeyondCode\ClaudeHooks\Hooks;

class Notification extends Hook
{
    protected string $message;

    protected string $title;

    public function __construct(array $data)
    {
        parent::__construct($data);
        $this->message = $data['message'] ?? '';
        $this->title = $data['title'] ?? '';
    }

    public function eventName(): string
    {
        return 'Notification';
    }

    public function message(): string
    {
        return $this->message;
    }

    public function title(): string
    {
        return $this->title;
    }
}
