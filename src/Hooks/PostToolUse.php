<?php

namespace BeyondCode\ClaudeHooks\Hooks;

use BeyondCode\ClaudeHooks\Concerns\HasToolInput;

class PostToolUse extends Hook
{
    use HasToolInput;

    protected string $toolName;

    protected array $toolResponse;

    public function __construct(array $data)
    {
        parent::__construct($data);
        $this->toolName = $data['tool_name'] ?? '';
        $this->toolInput = $data['tool_input'] ?? [];
        $this->toolResponse = $data['tool_response'] ?? [];
    }

    public function eventName(): string
    {
        return 'PostToolUse';
    }

    public function toolName(): string
    {
        return $this->toolName;
    }

    public function toolResponse(?string $key = null, mixed $default = null): mixed
    {
        if ($key === null) {
            return $this->toolResponse;
        }

        return $this->toolResponse[$key] ?? $default;
    }
}
