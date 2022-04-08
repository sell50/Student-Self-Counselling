<?php

class Semester extends Model
{
    public static function all(): array
    {
        return self::get('semesters');
    }

    public static function find(string $name): array
    {
        return App::getDatabase()->select("select * from semesters where name = '" . ucfirst($name) . "'");
    }
}
