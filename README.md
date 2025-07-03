# Claude Hooks SDK

[![Latest Version on Packagist](https://img.shields.io/packagist/v/beyondcode/claude-hooks-sdk.svg?style=flat-square)](https://packagist.org/packages/beyondcode/claude-hooks-sdk)
[![Tests](https://img.shields.io/github/actions/workflow/status/beyondcode/claude-hooks-sdk/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/beyondcode/claude-hooks-sdk/actions/workflows/run-tests.yml)
[![Total Downloads](https://img.shields.io/packagist/dt/beyondcode/claude-hooks-sdk.svg?style=flat-square)](https://packagist.org/packages/beyondcode/claude-hooks-sdk)

A Laravel-inspired PHP SDK for building Claude Code hook responses with a clean, fluent API. This SDK makes it easy to create structured JSON responses for Claude Code hooks using an expressive, chainable interface.

Claude Code hooks are user-defined shell commands that execute at specific points in Claude Code's lifecycle, providing deterministic control over its behavior. For more details, see the [Claude Code Hooks documentation](https://docs.anthropic.com/en/docs/claude-code/hooks).

## Installation

You can install the package via composer:

```bash
composer require beyondcode/claude-hooks-sdk
```

## Usage

### Creating a Claude Hook

Here's how to create a PHP script that Claude Code can use as a hook:

#### Step 1: Create your PHP hook script

Create a new PHP file (e.g., `validate-code.php`) using the SDK:

```php
<?php

require 'vendor/autoload.php';

use BeyondCode\ClaudeHooks\ClaudeHook;

// Read the hook data from stdin. 
// This will automatically return the correct Hook instance (for example PreToolUse)
$hook = ClaudeHook::create();

// Example: Validate bash commands
if ($hook->toolName() === 'Bash') {
    $command = $hook->toolInput('command', '');
    
    // Check for potentially dangerous commands
    if (str_contains($command, 'rm -rf')) {
        // Block the tool call with feedback
        $hook->response()->block('Dangerous command detected. Use caution with rm -rf commands.');
    }
}

// Allow other tool calls to proceed
$hook->success();
```

#### Step 2: Register your hook in Claude Code

1. Run the `/hooks` command in Claude Code
2. Select the `PreToolUse` hook event (runs before tool execution)
3. Add a matcher (e.g., `Bash` to match shell commands)
4. Add your hook command: `php /path/to/your/validate-code.php`
5. Save to user or project settings

Your hook is now active and will validate commands before Claude Code executes them!

### Hook Types and Methods

The SDK automatically creates the appropriate hook type based on the input:

```php
use BeyondCode\ClaudeHooks\ClaudeHook;
use BeyondCode\ClaudeHooks\Hooks\{PreToolUse, PostToolUse, Notification, Stop, SubagentStop};

$hook = ClaudeHook::create();

if ($hook instanceof PreToolUse) {
    $toolName = $hook->toolName();           // e.g., "Bash", "Write", "Edit"
    $toolInput = $hook->toolInput();         // Full input array
    $filePath = $hook->toolInput('file_path'); // Specific input value
}

if ($hook instanceof PostToolUse) {
    $toolResponse = $hook->toolResponse();   // Full response array
    $success = $hook->toolResponse('success', true); // With default value
}

if ($hook instanceof Notification) {
    $message = $hook->message();
    $title = $hook->title();
}

if ($hook instanceof Stop || $hook instanceof SubagentStop) {
    $isActive = $hook->stopHookActive();
}
```

### Response Methods

All hooks provide a fluent response API:

```php
// Continue processing (default behavior)
$hook->response()->continue();

// Stop Claude from continuing with a reason
$hook->response()->stop('Reason for stopping');

// For PreToolUse: approve or block tool calls
$hook->response()->approve('Optional approval message')->continue();
$hook->response()->block('Required reason for blocking')->continue();

// Suppress output from transcript mode
$hook->response()->suppressOutput()->continue();
```

### Example Hooks

#### Code Formatter Hook

Automatically format PHP files after edits:

```php
<?php

require 'vendor/autoload.php';

use BeyondCode\ClaudeHooks\ClaudeHook;
use BeyondCode\ClaudeHooks\Hooks\PostToolUse;

$hook = ClaudeHook::create();

$filePath = $hook->toolInput('file_path', '');

if (str_ends_with($filePath, '.php')) {
    exec("php-cs-fixer fix $filePath", $output, $exitCode);
    
    if ($exitCode !== 0) {
        $hook->response()
            ->suppressOutput()
            ->merge(['error' => 'Formatting failed'])
            ->continue();
    }
}
```

#### Security Validator Hook

Prevent modifications to sensitive files:

```php
#!/usr/bin/env php
<?php

require 'vendor/autoload.php';

use BeyondCode\ClaudeHooks\ClaudeHook;
use BeyondCode\ClaudeHooks\Hooks\PreToolUse;

$hook = ClaudeHook::fromStdin(file_get_contents('php://stdin'));

if ($hook instanceof PreToolUse) {
    // Check file-modifying tools
    if (in_array($hook->toolName(), ['Write', 'Edit', 'MultiEdit'])) {
        $filePath = $hook->toolInput('file_path', '');
        
        $sensitivePatterns = [
            '.env',
            'config/database.php',
            'storage/oauth-private.key',
        ];
        
        foreach ($sensitivePatterns as $pattern) {
            if (str_contains($filePath, $pattern)) {
                $hook->response()->block("Cannot modify sensitive file: $filePath");
            }
        }
    }
}

// Allow all other operations
$hook->response()->continue();
```

#### Notification Handler Hook

Custom notification handling:

```php
<?php

require 'vendor/autoload.php';

use BeyondCode\ClaudeHooks\ClaudeHook;
use BeyondCode\ClaudeHooks\Hooks\Notification;

$hook = ClaudeHook::create();

// Send to custom notification system
$notificationData = [
    'title' => $hook->title(),
    'message' => $hook->message(),
    'session' => $hook->sessionId(),
    'timestamp' => time()
];
    
// Send notification to Slack, Discord, etc.

$hook->success();
```


## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Marcel Pociot](https://github.com/mpociot)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
