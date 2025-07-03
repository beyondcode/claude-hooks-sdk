<?php

use BeyondCode\ClaudeHooks\Hooks\SubagentStop;

beforeEach(function () {
    $this->data = [
        'session_id' => 'test-session',
        'transcript_path' => '/path/to/transcript.jsonl',
        'stop_hook_active' => false,
    ];
});

it('accesses stop hook active status', function () {
    $hook = new SubagentStop($this->data);

    expect($hook->stopHookActive())->toBe(false);
});

it('defaults to false when stop_hook_active is missing', function () {
    unset($this->data['stop_hook_active']);
    $hook = new SubagentStop($this->data);

    expect($hook->stopHookActive())->toBe(false);
});
