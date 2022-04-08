<?php

class Course extends Model
{
    public static function find(string $code): array
    {
        return App::getDatabase()->select('select * from courses where code = ' . $code);
    }

    public static function getSemesters(int $course): array
    {
        return App::getDatabase()->selectAll(
            'select s.name as name from course_semester cm join semesters s on s.id = cm.semester_id where cm.course_id = ' . $course
        );
    }

    public static function getPrerequisites(int $course): array
    {
        return App::getDatabase()->selectAll(
            'select c.id as id, c.code as code, c.name as name from course_prerequisite cp join courses c on c.id = cp.prerequisite_id where cp.course_id = ' . $course . ' order by c.code'
        );
    }

    public static function hasCompletedPrerequisites(int $course, array $completed): bool
    {
        foreach (Course::getPrerequisites($course) as $course) {
            if (!in_array($course['code'], $completed)) {
                return false;
            }
        }
        return true;
    }

    public static function isAvailable(int $course, string $semester): bool
    {
        //$course = Course::find($course)['id'];
        $semester = Semester::find($semester)['id'];
        $data = App::getDatabase()->select(
            'select * from course_semester where course_id = ' . $course . ' and semester_id = ' . $semester
        );
        return empty($data);
    }
}
