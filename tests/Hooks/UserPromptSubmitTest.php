<?php

use BeyondCode\ClaudeHooks\Hooks\UserPromptSubmit;

beforeEach(function () {
    $this->data = [
        'session_id' => 'test-session',
        'transcript_path' => '/path/to/transcript.jsonl',
        'prompt' => 'Write a function to calculate factorial',
    ];
});

it('accesses prompt', function () {
    $hook = new UserPromptSubmit($this->data);
    expect($hook->prompt())->toBe('Write a function to calculate factorial');
});

it('handles missing prompt gracefully', function () {
    unset($this->data['prompt']);
    $hook = new UserPromptSubmit($this->data);
    expect($hook->prompt())->toBe('');
});
