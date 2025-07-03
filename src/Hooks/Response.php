<?php

namespace BeyondCode\ClaudeHooks\Hooks;

class Response
{
    protected array $data = [];

    protected int $exitCode = 0;

    /**
     * Continue processing (exit with code 0)
     */
    public function continue(): never
    {
        $this->data['continue'] = true;
        $this->send();
    }

    /**
     * Stop processing with a reason (exit with code 1)
     */
    public function stop(string $reason): never
    {
        $this->data['continue'] = false;
        $this->data['stopReason'] = $reason;
        $this->exitCode = 1;
        $this->send();
    }

    /**
     * Block the tool call
     */
    public function block(string $reason): self
    {
        $this->data['decision'] = 'block';
        $this->data['reason'] = $reason;

        return $this;
    }

    /**
     * Approve the tool call (PreToolUse only)
     */
    public function approve(string $reason = ''): self
    {
        $this->data['decision'] = 'approve';
        if ($reason) {
            $this->data['reason'] = $reason;
        }

        return $this;
    }

    /**
     * Suppress output from transcript mode
     */
    public function suppressOutput(): self
    {
        $this->data['suppressOutput'] = true;

        return $this;
    }

    /**
     * Merge multiple fields into the response
     */
    public function merge(array $fields): self
    {
        $this->data = array_merge($this->data, $fields);

        return $this;
    }

    /**
     * Send the response and exit
     */
    protected function send(): never
    {
        echo json_encode($this->data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        exit($this->exitCode);
    }
}
