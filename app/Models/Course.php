<?php

class Course extends Model
{
    public static function find(string $code): array
    {
        return App::getDatabase()->select('select * from courses where code = \'' . $code . '\'');
    }

    public static function getPrerequisites(string $code, bool $flatten = false): array
    {
        $course = self::find($code);
        $data = App::getDatabase()->selectAll(
            'select c.id as id, c.code as code, c.name as name from course_prerequisite cp join courses c on c.id = cp.prerequisite_id where cp.course_id = ' . $course['id'] . ' order by c.code'
        );
        return $flatten ? Helper::flatten($data) : $data;
    }

    // TODO
    public static function hasCompletedPrerequisites(int $course, array $completed): bool
    {
        foreach (Course::getPrerequisites($course) as $course) {
            if (!in_array($course['code'], $completed)) {
                return false;
            }
        }
        return true;
    }

    // TODO
    public static function getSemesters(int $course): array
    {
        return App::getDatabase()->selectAll(
            'select s.name as name from course_semester cm join semesters s on s.id = cm.semester_id where cm.course_id = ' . $course
        );
    }
}
