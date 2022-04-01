<?php

abstract class Model
{
    private array $data;

    public function __get($name)
    {
        if (array_key_exists($name, $this->data)) {
            return $this->data[$name];
        } else {
            throw new Exception('Variable not found.');
        }
    }

    public function __set($name, $value): void
    {
        $this->data[$name] = $value;
    }

    protected static function get(string $table, $primaryKey = null, $value = null): array
    {
        if ($primaryKey !== null && $value !== null) {
            $response = App::getDatabase()->select(
                "SELECT * FROM $table where $primaryKey = $value"
            );
        } else {
            $response = App::getDatabase()->selectAll(
                "SELECT * FROM $table"
            );
        }

        if ($response === false) {
            throw new InvalidArgumentException('Model not found.');
        }

        return $response;
    }

    protected function execute(string $query): void
    {
        $response = App::getDatabase()->execute($query, $this->data);

        if ($response === false) {
            throw new InvalidArgumentException('Unable to save model.');
        }
    }
}
