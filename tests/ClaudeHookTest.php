<?php

use BeyondCode\ClaudeHooks\ClaudeHook;
use BeyondCode\ClaudeHooks\Hooks\Notification;
use BeyondCode\ClaudeHooks\Hooks\PostToolUse;
use BeyondCode\ClaudeHooks\Hooks\PreToolUse;
use BeyondCode\ClaudeHooks\Hooks\Stop;
use BeyondCode\ClaudeHooks\Hooks\SubagentStop;
use BeyondCode\ClaudeHooks\Hooks\UserPromptSubmit;

it('creates PreToolUse hook from stdin', function () {
    $stdin = json_encode([
        'session_id' => 'test-session',
        'transcript_path' => '/path/to/transcript.jsonl',
        'hook_event_name' => 'PreToolUse',
        'tool_name' => 'Read',
        'tool_input' => ['file_path' => '/test/file.php'],
    ]);

    $hook = ClaudeHook::fromStdin($stdin);

    expect($hook)->toBeInstanceOf(PreToolUse::class);
    expect($hook->eventName())->toBe('PreToolUse');
    expect($hook->toolName())->toBe('Read');
    expect($hook->toolInput())->toBe(['file_path' => '/test/file.php']);
    expect($hook->toolInput('file_path'))->toBe('/test/file.php');
    expect($hook->sessionId())->toBe('test-session');
    expect($hook->transcriptPath())->toBe('/path/to/transcript.jsonl');
});

it('creates PostToolUse hook from stdin', function () {
    $stdin = json_encode([
        'session_id' => 'test-session',
        'transcript_path' => '/path/to/transcript.jsonl',
        'hook_event_name' => 'PostToolUse',
        'tool_name' => 'Write',
        'tool_input' => ['file_path' => '/test/file.php', 'content' => 'test'],
        'tool_response' => ['filePath' => '/test/file.php', 'success' => true],
    ]);

    $hook = ClaudeHook::fromStdin($stdin);

    expect($hook)->toBeInstanceOf(PostToolUse::class);
    expect($hook->eventName())->toBe('PostToolUse');
    expect($hook->toolName())->toBe('Write');
    expect($hook->toolInput())->toBe(['file_path' => '/test/file.php', 'content' => 'test']);
    expect($hook->toolResponse())->toBe(['filePath' => '/test/file.php', 'success' => true]);
    expect($hook->toolResponse('success'))->toBe(true);
});

it('creates Notification hook from stdin', function () {
    $stdin = json_encode([
        'session_id' => 'test-session',
        'transcript_path' => '/path/to/transcript.jsonl',
        'hook_event_name' => 'Notification',
        'message' => 'Task completed successfully',
        'title' => 'Claude Code',
    ]);

    $hook = ClaudeHook::fromStdin($stdin);

    expect($hook)->toBeInstanceOf(Notification::class);
    expect($hook->eventName())->toBe('Notification');
    expect($hook->message())->toBe('Task completed successfully');
    expect($hook->title())->toBe('Claude Code');
});

it('creates Stop hook from stdin', function () {
    $stdin = json_encode([
        'session_id' => 'test-session',
        'transcript_path' => '/path/to/transcript.jsonl',
        'hook_event_name' => 'Stop',
        'stop_hook_active' => true,
    ]);

    $hook = ClaudeHook::fromStdin($stdin);

    expect($hook)->toBeInstanceOf(Stop::class);
    expect($hook->eventName())->toBe('Stop');
    expect($hook->stopHookActive())->toBe(true);
});

it('creates SubagentStop hook from stdin', function () {
    $stdin = json_encode([
        'session_id' => 'test-session',
        'transcript_path' => '/path/to/transcript.jsonl',
        'hook_event_name' => 'SubagentStop',
        'stop_hook_active' => false,
    ]);

    $hook = ClaudeHook::fromStdin($stdin);

    expect($hook)->toBeInstanceOf(SubagentStop::class);
    expect($hook->eventName())->toBe('SubagentStop');
    expect($hook->stopHookActive())->toBe(false);
});

it('creates UserPromptSubmit hook from stdin', function () {
    $stdin = json_encode([
        'session_id' => 'test-session',
        'transcript_path' => '/path/to/transcript.jsonl',
        'cwd' => '/path/to/project',
        'hook_event_name' => 'UserPromptSubmit',
        'prompt' => 'Write a function to calculate factorial',
    ]);

    $hook = ClaudeHook::fromStdin($stdin);

    expect($hook)->toBeInstanceOf(UserPromptSubmit::class);
    expect($hook->eventName())->toBe('UserPromptSubmit');
    expect($hook->prompt())->toBe('Write a function to calculate factorial');
    expect($hook->sessionId())->toBe('test-session');
    expect($hook->transcriptPath())->toBe('/path/to/transcript.jsonl');
});

it('throws exception for invalid JSON', function () {
    $stdin = 'invalid json';

    expect(fn () => ClaudeHook::fromStdin($stdin))
        ->toThrow(InvalidArgumentException::class, 'Invalid JSON data provided');
});

it('throws exception for missing hook_event_name', function () {
    $stdin = json_encode([
        'session_id' => 'test-session',
        'transcript_path' => '/path/to/transcript.jsonl',
    ]);

    expect(fn () => ClaudeHook::fromStdin($stdin))
        ->toThrow(InvalidArgumentException::class, 'Missing hook_event_name in input data');
});

it('throws exception for unknown hook event type', function () {
    $stdin = json_encode([
        'session_id' => 'test-session',
        'transcript_path' => '/path/to/transcript.jsonl',
        'hook_event_name' => 'UnknownHook',
    ]);

    expect(fn () => ClaudeHook::fromStdin($stdin))
        ->toThrow(InvalidArgumentException::class, 'Unknown hook event type: UnknownHook');
});
