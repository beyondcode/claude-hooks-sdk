<?php

namespace BeyondCode\ClaudeHooks\Hooks;

abstract class Hook
{
    protected array $data;

    protected string $sessionId;

    protected string $transcriptPath;

    protected array $response = [];

    protected ?Response $responseInstance = null;

    public function __construct(array $data)
    {
        $this->data = $data;
        $this->sessionId = $data['session_id'] ?? '';
        $this->transcriptPath = $data['transcript_path'] ?? '';
    }

    public function sessionId(): string
    {
        return $this->sessionId;
    }

    public function transcriptPath(): string
    {
        return $this->transcriptPath;
    }

    public function rawData(): array
    {
        return $this->data;
    }

    public function transcript(): array
    {
        if (! file_exists($this->transcriptPath)) {
            return [];
        }

        $content = file_get_contents($this->transcriptPath);
        $decoded = json_decode($content, true);

        return $decoded ?: [];
    }

    public function toJson(): string
    {
        return json_encode($this->data, JSON_PRETTY_PRINT);
    }

    abstract public function eventName(): string;

    public function response(): Response
    {
        if ($this->responseInstance === null) {
            $this->responseInstance = new Response;
        }

        return $this->responseInstance;
    }

    public function error(string $message): void
    {
        fwrite(STDERR, $message);
        exit(2);
    }

    public function success(string $message = ''): void
    {
        if ($message) {
            echo $message;
        }
        exit(0);
    }
}
