<?php

class HomeController extends Controller
{
    public function first(): Response
    {
        return $this->render('first', [
            'programs' => Program::all(),
            'semesters' => Semester::all(),
        ]);
    }

    public function second(): Response
    {
        if (!isset($_GET['program'], $_GET['year'], $_GET['semester'])) {
            return $this->bad();
        }

        $courses = [];
        foreach (Program::getRequiredCoursesForYear($_GET['program'], $_GET['year']) as $course) {

            $requirements = [];
            foreach (Course::getPrerequisites($course['id']) as $requirement) {
                $requirements[] = [
                    'id' => $requirement['id'],
                    'code' => $requirement['code'],
                    'name' => $requirement['name'],
                    'semesters' => self::getSemesters($requirement['id']),
                ];
            }

            $courses[] = [
                'id' => $course['id'],
                'code' => $course['code'],
                'name' => $course['name'],
                'semesters' => self::getSemesters($course['id']),
                'requirements' => $requirements,
            ];
        }

        return $this->render('second', [
            'program' => $_GET['program'],
            'courses' => $courses,
        ]);
    }

    public function third(): Response
    {
        if (!isset($_GET['program'])) {
            return $this->bad();
        }

        $courses = [];
        foreach (Program::getRequiredCourses($_GET['program']) as $course) {
            $courses[] = [
                'id' => $course['id'],
                'code' => $course['code'],
                'name' => $course['name'],
                'semesters' => self::getSemesters($course['id']),
                'requirements' => Course::getPrerequisites($course['id']),
            ];
        }

        return $this->render('third', [
            'courses' => $courses,
        ]);
    }

    public function fourth(): Response
    {
        var_dump($_POST['courses']);
        exit();

        return $this->render('fourth', [
        ]);
    }

    private static function getSemesters(int $course): string
    {
        $semesters = Course::getSemesters($course);
        $semesters = array_column($semesters, 'name');
        return join(', ', $semesters);
    }
}
