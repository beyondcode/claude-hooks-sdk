<?php

namespace BeyondCode\ClaudeHooks\Concerns;

trait HasToolInput
{
    protected array $toolInput;

    public function toolInput(?string $key = null, mixed $default = null): mixed
    {
        if ($key === null) {
            return $this->toolInput;
        }

        return $this->toolInput[$key] ?? $default;
    }
}
