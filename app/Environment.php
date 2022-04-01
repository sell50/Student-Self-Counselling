<?php

final class Environment
{
    public function __construct(private string $path)
    {
        // checking if the file exists
        if (!file_exists($path)) {
            throw new InvalidArgumentException("Environment file not found: $path");
        }

        // checking if the file is readable
        if (!is_readable($path)) {
            throw new InvalidArgumentException("Environment file not readable: $path");
        }
    }

    public function load(): void
    {
        $lines = file($this->path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {

            // get key & value
            [$key, $value] = explode('=', $line, 2);

            // remove empty spaces
            $key = trim($key);
            $value = trim($value);

            // set the environment variable
            $_ENV[$key] = $value;
        }
    }
}
