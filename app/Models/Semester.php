<?php

class Semester extends Model
{
    public static function all(): array
    {
        return self::get('semesters');
    }
}
