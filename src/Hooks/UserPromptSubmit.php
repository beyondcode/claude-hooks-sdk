<?php

namespace BeyondCode\ClaudeHooks\Hooks;

class UserPromptSubmit extends Hook
{
    protected string $prompt;

    public function __construct(array $data)
    {
        parent::__construct($data);
        $this->prompt = $data['prompt'] ?? '';
    }

    public function eventName(): string
    {
        return 'UserPromptSubmit';
    }

    public function prompt(): string
    {
        return $this->prompt;
    }
}
