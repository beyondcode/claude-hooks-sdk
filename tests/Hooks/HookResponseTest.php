<?php

use BeyondCode\ClaudeHooks\Hooks\Hook;
use BeyondCode\ClaudeHooks\Hooks\Response;

class TestableHook extends Hook
{
    public function eventName(): string
    {
        return 'test-event';
    }
}

beforeEach(function () {
    $this->data = [
        'session_id' => 'test-session',
        'transcript_path' => '/path/to/transcript.jsonl',
    ];
    $this->hook = new TestableHook($this->data);
});

it('returns a Response instance', function () {
    $response = $this->hook->response();
    
    expect($response)->toBeInstanceOf(Response::class);
});

it('returns the same Response instance on multiple calls', function () {
    $response1 = $this->hook->response();
    $response2 = $this->hook->response();
    
    expect($response1)->toBe($response2);
});

it('creates a new Response instance on first call', function () {
    $reflection = new ReflectionClass($this->hook);
    $property = $reflection->getProperty('responseInstance');
    $property->setAccessible(true);
    
    expect($property->getValue($this->hook))->toBeNull();
    
    $response = $this->hook->response();
    
    expect($property->getValue($this->hook))->toBe($response);
});