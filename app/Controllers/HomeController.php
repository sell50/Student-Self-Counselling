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
        foreach (Program::getRequiredCourses($_GET['program']) as $course) {

            $semesters = Course::getSemesters($course['id']);
            $semesters = array_column($semesters, 'name');
            $semesters = join(', ', $semesters);

            $courses[] = [
                'id' => $course['id'],
                'name' => $course['name'],
                'semesters' => $semesters,
            ];
        }

        return $this->render('second', [
            'courses' => $courses
        ]);
    }

    public function third(): Response
    {
        if (!isset($_GET['art'], $_GET['social'], $_GET['electives'])) {
            return $this->bad();
        }

        return $this->render('third', [
        ]);
    }
}
