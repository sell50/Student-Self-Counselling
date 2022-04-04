<?php

class Program extends Model
{
    public static function all(): array
    {
        return self::get('programs');
    }

    public static function getRequiredCourses(int $program): array
    {
        return App::getDatabase()->selectAll(
            'select c.id as id, c.name as name from course_program cp join courses c on c.id = cp.course_id where cp.program_id = ' . $program
        );
    }
}
