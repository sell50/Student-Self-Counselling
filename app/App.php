<?php

final class App
{
    private static Database $database;
    public Environment $environment;
    public Router $router;

    public function __construct()
    {
        $this->environment = new Environment(__DIR__ . '/../.env');
        $this->router = new Router();
    }

    public static function getDatabase(): Database
    {
        return self::$database ?? new Database();
    }
}