<?php

use BeyondCode\ClaudeHooks\Hooks\Notification;

beforeEach(function () {
    $this->data = [
        'session_id' => 'test-session',
        'transcript_path' => '/path/to/transcript.jsonl',
        'message' => 'Task completed successfully',
        'title' => 'Claude Code',
    ];
});

it('accesses message and title', function () {
    $hook = new Notification($this->data);

    expect($hook->message())->toBe('Task completed successfully');
    expect($hook->title())->toBe('Claude Code');
});

it('handles missing message and title gracefully', function () {
    $data = [
        'session_id' => 'test-session',
        'transcript_path' => '/path/to/transcript.jsonl',
    ];

    $hook = new Notification($data);

    expect($hook->message())->toBe('');
    expect($hook->title())->toBe('');
});
