<?php

use BeyondCode\ClaudeHooks\Hooks\PreToolUse;

beforeEach(function () {
    $this->data = [
        'session_id' => 'test-session',
        'transcript_path' => '/path/to/transcript.jsonl',
        'tool_name' => 'Read',
        'tool_input' => ['file_path' => '/test/file.php'],
    ];
});

it('handles tool input with default values', function () {
    $hook = new PreToolUse($this->data);

    expect($hook->toolInput('missing_key'))->toBeNull();
    expect($hook->toolInput('missing_key', 'default'))->toBe('default');
});

it('converts to JSON', function () {
    $hook = new PreToolUse($this->data);
    $json = $hook->toJson();

    expect($json)->toBeJson();
    expect(json_decode($json, true))->toBe($this->data);
});

it('provides access to raw data', function () {
    $this->data['extra_field'] = 'extra_value';
    $hook = new PreToolUse($this->data);

    expect($hook->rawData())->toBe($this->data);
});
