<?php

use BeyondCode\ClaudeHooks\Hooks\PostToolUse;

beforeEach(function () {
    $this->data = [
        'session_id' => 'test-session',
        'transcript_path' => '/path/to/transcript.jsonl',
        'tool_name' => 'Write',
        'tool_input' => ['file_path' => '/test/file.php', 'content' => 'test'],
        'tool_response' => ['filePath' => '/test/file.php', 'success' => true],
    ];
});

it('handles tool response with default values', function () {
    $hook = new PostToolUse($this->data);

    expect($hook->toolResponse('missing_key'))->toBeNull();
    expect($hook->toolResponse('missing_key', 'default'))->toBe('default');
});

it('accesses nested tool response values', function () {
    $hook = new PostToolUse($this->data);

    expect($hook->toolResponse('success'))->toBe(true);
    expect($hook->toolResponse('filePath'))->toBe('/test/file.php');
});

it('returns full tool response when no key provided', function () {
    $hook = new PostToolUse($this->data);

    expect($hook->toolResponse())->toBe($this->data['tool_response']);
});
