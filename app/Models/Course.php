<?php

class Course extends Model
{
    public static function getSemesters(int $course): array
    {
        return App::getDatabase()->selectAll(
            'select s.name as name from course_semester cm join semesters s on s.id = cm.semester_id where cm.course_id = ' . $course
        );
    }
}
