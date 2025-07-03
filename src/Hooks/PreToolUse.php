<?php

namespace BeyondCode\ClaudeHooks\Hooks;

use BeyondCode\ClaudeHooks\Concerns\HasToolInput;

class PreToolUse extends Hook
{
    use HasToolInput;

    protected string $toolName;

    public function __construct(array $data)
    {
        parent::__construct($data);
        $this->toolName = $data['tool_name'] ?? '';
        $this->toolInput = $data['tool_input'] ?? [];
    }

    public function eventName(): string
    {
        return 'PreToolUse';
    }

    public function toolName(): string
    {
        return $this->toolName;
    }
}
