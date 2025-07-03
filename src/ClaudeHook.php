<?php

namespace BeyondCode\ClaudeHooks;

use BeyondCode\ClaudeHooks\Hooks\Hook;
use BeyondCode\ClaudeHooks\Hooks\Notification;
use BeyondCode\ClaudeHooks\Hooks\PostToolUse;
use BeyondCode\ClaudeHooks\Hooks\PreToolUse;
use BeyondCode\ClaudeHooks\Hooks\Stop;
use BeyondCode\ClaudeHooks\Hooks\SubagentStop;

class ClaudeHook
{
    protected static array $eventMap = [
        'PreToolUse' => PreToolUse::class,
        'PostToolUse' => PostToolUse::class,
        'Notification' => Notification::class,
        'Stop' => Stop::class,
        'SubagentStop' => SubagentStop::class,
    ];

    public static function fromStdin(string $stdin): Hook
    {
        $data = json_decode($stdin, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \InvalidArgumentException('Invalid JSON data provided: '.json_last_error_msg());
        }

        if (! isset($data['hook_event_name'])) {
            throw new \InvalidArgumentException('Missing hook_event_name in input data');
        }

        $eventType = $data['hook_event_name'];

        if (! isset(static::$eventMap[$eventType])) {
            throw new \InvalidArgumentException("Unknown hook event type: {$eventType}");
        }

        $hookClass = static::$eventMap[$eventType];

        return new $hookClass($data);
    }

    /**
     * Create a hook from actual stdin input
     */
    public static function create(): Hook
    {
        $stdin = file_get_contents('php://stdin');

        return self::fromStdin($stdin);
    }
}
