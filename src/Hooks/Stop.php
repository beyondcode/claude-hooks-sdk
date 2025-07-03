<?php

namespace BeyondCode\ClaudeHooks\Hooks;

class Stop extends Hook
{
    protected bool $stopHookActive;

    public function __construct(array $data)
    {
        parent::__construct($data);
        $this->stopHookActive = $data['stop_hook_active'] ?? false;
    }

    public function eventName(): string
    {
        return 'Stop';
    }

    public function stopHookActive(): bool
    {
        return $this->stopHookActive;
    }
}
