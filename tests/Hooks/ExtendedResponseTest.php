<?php

use BeyondCode\ClaudeHooks\Hooks\Response;

it('can block and send immediately', function () {
    $response = new class extends Response {
        public function testBlockAndSend(): void {
            ob_start();
            try {
                $this->blockAndSend('Tool execution blocked');
            } catch (SystemExit $e) {
                // Expected
            }
            $output = ob_get_clean();
            
            $data = json_decode($output, true);
            expect($data)->toBeArray();
            expect($data['decision'])->toBe('block');
            expect($data['reason'])->toBe('Tool execution blocked');
        }
    };
    
    $response->testBlockAndSend();
});

it('can approve and send immediately', function () {
    $response = new class extends Response {
        public function testApproveAndSend(): void {
            ob_start();
            try {
                $this->approveAndSend('Tool execution approved');
            } catch (SystemExit $e) {
                // Expected
            }
            $output = ob_get_clean();
            
            $data = json_decode($output, true);
            expect($data)->toBeArray();
            expect($data['decision'])->toBe('approve');
            expect($data['reason'])->toBe('Tool execution approved');
        }
    };
    
    $response->testApproveAndSend();
});

it('blockAndSend terminates execution', function () {
    $response = new Response();
    
    expect(function () use ($response) {
        $response->blockAndSend('Blocked');
    })->toThrow(SystemExit::class);
});

it('approveAndSend terminates execution', function () {
    $response = new Response();
    
    expect(function () use ($response) {
        $response->approveAndSend('Approved');
    })->toThrow(SystemExit::class);
});