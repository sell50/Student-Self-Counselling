<?php

class Course extends Model
{
    public static function getSemesters(int $course): array
    {
        return App::getDatabase()->selectAll(
            'select s.name as name from course_semester cm join semesters s on s.id = cm.semester_id where cm.course_id = ' . $course
        );
    }

    public static function getPrerequisites(int $course): array
    {
        return App::getDatabase()->selectAll(
            'select c.id as id, c.code as code, c.name as name from course_prerequisite cp join courses c on c.id = cp.prerequisite_id where cp.course_id = ' . $course
        );
    }
}
