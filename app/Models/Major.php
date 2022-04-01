<?php

class Major extends Model
{
    public static function all(): array
    {
        return self::get('majors');
    }
}
