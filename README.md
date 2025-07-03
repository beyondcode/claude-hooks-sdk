# Claude Hooks SDK

[![Latest Version on Packagist](https://img.shields.io/packagist/v/beyondcode/claude-hooks-sdk.svg?style=flat-square)](https://packagist.org/packages/beyondcode/claude-hooks-sdk)
[![Tests](https://img.shields.io/github/actions/workflow/status/beyondcode/claude-hooks-sdk/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/beyondcode/claude-hooks-sdk/actions/workflows/run-tests.yml)
[![Total Downloads](https://img.shields.io/packagist/dt/beyondcode/claude-hooks-sdk.svg?style=flat-square)](https://packagist.org/packages/beyondcode/claude-hooks-sdk)

A Laravel-inspired PHP SDK for building Claude Code hook responses with a clean, fluent API. This SDK makes it easy to create structured JSON responses for Claude Code hooks using an expressive, chainable interface.

## Installation

You can install the package via composer:

```bash
composer require beyondcode/claude-hooks-sdk
```

## Usage

### Basic Examples

#### PreToolUse Hooks

Block a tool call:
```php
use ClaudeHooks\Hook;

Hook::preToolUse()
    ->block('Command uses deprecated grep instead of ripgrep')
    ->send();
```

Approve a tool call:
```php
Hook::preToolUse()
    ->approve('Command validated successfully')
    ->send();
```

#### PostToolUse Hooks

Provide feedback after tool execution:
```php
Hook::postToolUse()
    ->block('Code formatting violations detected')
    ->send();
```

#### Stop/SubagentStop Hooks

Prevent Claude from stopping:
```php
Hook::stop()
    ->block('Tests are still running, please wait')
    ->send();
```

### Advanced Examples

#### Suppress Output

Hide stdout from transcript mode:
```php
Hook::preToolUse()
    ->approve('Silently approved')
    ->suppressOutput()
    ->send();
```

#### Stop Processing

Stop Claude from continuing with a reason:
```php
Hook::make()
    ->stopProcessing('System maintenance in progress')
    ->send();
```

#### Custom Fields

Add custom fields to the response:
```php
Hook::postToolUse()
    ->with('customField', 'value')
    ->merge(['foo' => 'bar', 'baz' => 123])
    ->send();
```

#### Error Responses

Send a blocking error (exit code 2):
```php
Hook::blockWithError('Invalid file path detected');
```

Send a non-blocking error:
```php
Hook::make()->fail('Warning: deprecated function used', 1);
```

### Example Hook Scripts

#### Code Formatter Hook

```php
#!/usr/bin/env php
<?php
require __DIR__ . '/vendor/autoload.php';

use ClaudeHooks\Hook;

$input = json_decode(file_get_contents('php://stdin'), true);
$toolInput = $input['tool_input'] ?? [];
$filePath = $toolInput['file_path'] ?? '';

if (!$filePath || !file_exists($filePath)) {
    exit(0);
}

$extension = pathinfo($filePath, PATHINFO_EXTENSION);

// Run appropriate formatter
$formatters = [
    'php' => 'php-cs-fixer fix %s',
    'js' => 'prettier --write %s',
    'ts' => 'prettier --write %s',
    'py' => 'black %s',
];

if (isset($formatters[$extension])) {
    $cmd = sprintf($formatters[$extension], escapeshellarg($filePath));
    exec($cmd, $output, $exitCode);
    
    if ($exitCode !== 0) {
        Hook::postToolUse()
            ->block("Formatting failed: " . implode("\n", $output))
            ->send();
    }
}

Hook::success();
```

#### Command Validator Hook

```php
#!/usr/bin/env php
<?php
require __DIR__ . '/vendor/autoload.php';

use ClaudeHooks\Hook;

$input = json_decode(file_get_contents('php://stdin'), true);

if ($input['tool_name'] !== 'Bash') {
    exit(0);
}

$command = $input['tool_input']['command'] ?? '';

// Validate dangerous commands
$dangerous = ['rm -rf /', 'dd if=', ':(){:|:&};:'];
foreach ($dangerous as $pattern) {
    if (strpos($command, $pattern) !== false) {
        Hook::preToolUse()
            ->block("Dangerous command detected: $pattern")
            ->send();
    }
}

// Check for deprecated commands
if (preg_match('/\bgrep\b(?!.*\|)/', $command)) {
    Hook::preToolUse()
        ->block("Use 'rg' (ripgrep) instead of 'grep' for better performance")
        ->send();
}

// Approve if all checks pass
Hook::preToolUse()
    ->approve()
    ->send();
```

#### Stop Hook with Tests

```php
#!/usr/bin/env php
<?php
require __DIR__ . '/vendor/autoload.php';

use ClaudeHooks\Hook;

// Check if tests are running
exec('pgrep -f "phpunit|pest"', $output, $exitCode);

if ($exitCode === 0) {
    Hook::stop()
        ->block('Tests are still running. Please wait for completion.')
        ->send();
}

// Check for uncommitted changes
exec('git diff --quiet', $output, $exitCode);

if ($exitCode !== 0) {
    Hook::stop()
        ->block('You have uncommitted changes. Please commit or stash them first.')
        ->send();
}

// Allow stopping
Hook::success();
```

### API Reference

#### Static Factory Methods

- `Hook::preToolUse()` - Create a PreToolUse hook response
- `Hook::postToolUse()` - Create a PostToolUse hook response  
- `Hook::stop()` - Create a Stop hook response
- `Hook::subagentStop()` - Create a SubagentStop hook response
- `Hook::make()` - Create a generic hook response

#### Decision Methods

- `approve(string $reason = '')` - Approve tool execution (PreToolUse only)
- `block(string $reason)` - Block tool execution or prevent stopping

#### Control Flow Methods

- `continueProcessing()` - Allow Claude to continue (default)
- `stopProcessing(string $stopReason)` - Stop Claude with a reason
- `suppressOutput(bool $suppress = true)` - Hide output from transcript

#### Data Methods

- `with(string $key, $value)` - Add a custom field
- `merge(array $fields)` - Merge multiple fields
- `toArray()` - Get output as array
- `toJson(int $options)` - Get output as JSON string

#### Output Methods

- `send(int $exitCode = 0)` - Send JSON response and exit
- `error(string $message)` - Send blocking error (exit 2)
- `fail(string $message, int $exitCode)` - Send non-blocking error

#### Static Helpers

- `Hook::blockWithError(string $message)` - Quick blocking error
- `Hook::success(string $message = '')` - Quick success response

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
