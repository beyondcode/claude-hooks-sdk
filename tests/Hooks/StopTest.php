<?php

use BeyondCode\ClaudeHooks\Hooks\Stop;

beforeEach(function () {
    $this->data = [
        'session_id' => 'test-session',
        'transcript_path' => '/path/to/transcript.jsonl',
        'stop_hook_active' => true,
    ];
});

it('accesses stop hook active status', function () {
    $hook = new Stop($this->data);

    expect($hook->stopHookActive())->toBe(true);
});

it('defaults to false when stop_hook_active is missing', function () {
    unset($this->data['stop_hook_active']);
    $hook = new Stop($this->data);

    expect($hook->stopHookActive())->toBe(false);
});
