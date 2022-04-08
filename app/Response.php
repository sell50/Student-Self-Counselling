<?php

final class Response
{
    public function __construct(private int $status, private string $content)
    {
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function getContent(): string
    {
        return $this->content;
    }
}
