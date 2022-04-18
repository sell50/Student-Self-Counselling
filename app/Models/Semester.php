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

    public static function isCourseAvailable(string $semester, string $course): bool
    {
        $course = Course::find($course)['id'];
        $semester = Semester::find($semester)['id'];
        $data = App::getDatabase()->select(
            'select * from course_semester where course_id = ' . $course . ' and semester_id = ' . $semester
        );
        return !empty($data);
    }
}
