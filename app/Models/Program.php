<?php

class Program extends Model
{
    public static function all(): array
    {
        return self::get('programs');
    }

    public static function getRequiredCourses(int $program, int $year): array
    {

        return App::getDatabase()->selectAll(
            'select c.id as id, c.code as code, c.name as name from course_program cp join courses c on c.id = cp.course_id where cp.program_id = ' . $program . ' and substring(c.code, 6,  1) = ' . $year
        );
    }
}
