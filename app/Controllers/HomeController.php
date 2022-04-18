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
            foreach (Course::getPrerequisites($course['code']) as $requirement) {
                $requirements[] = [
                    'id' => $requirement['id'],
                    'code' => $requirement['code'],
                    'name' => $requirement['name'],
                    'semesters' => Helper::getSemestersArray($requirement['id']),
                ];
            }

            $courses[] = [
                'id' => $course['id'],
                'code' => $course['code'],
                'name' => $course['name'],
                'semesters' => Helper::getSemestersArray($course['id']),
                'requirements' => $requirements,
            ];
        }

        return $this->render('second', [
            'courses' => $courses,
        ]);
    }

    public function third(): Response
    {
        if (!isset($_GET['program'], $_GET['year'], $_GET['semester'])) {
            return $this->bad();
        }

        $program = $_GET['program'];
        $courses = [];
        foreach (Program::getRequiredCourses($program) as $course) {
            $courses[] = [
                'id' => $course['id'],
                'code' => $course['code'],
                'name' => $course['name'],
                'semesters' => Helper::getSemestersArray($course['id']),
                'requirements' => Course::getPrerequisites($course['code']),
            ];
        }

        return $this->render('third', [
            'program' => $program,
            'courses' => $courses,
        ]);
    }

    public function fourth(): Response
    {
        /*$completedCourses = $_POST['courses'];
        $completedArtCoursesCount = count($completedCourses) + $completedArtCourses + $completedSocialCourses + $completedElectiveCourses;
        $program = Program::find($_POST['program']);
        $program['courses'] = Program::getRequiredCourses($program['id']);

        $remainingCourses = self::removeCompletedCourses($program['courses'], $completedCourses);
        $remaining = [
            'additional' => self::getRemainingAdditionalCourses($program['courses'], $completedCourses, $program['additional_courses']),
            'art' => $program['art_courses'] - $_POST['art'],
            'social' => $program['social_courses'] - $_POST['social'],
            'art_social' => $program['art_social_courses'] - ($program['art_courses'] + $program['social_courses']),
            'electives' => $program['electives'] - $_POST['electives'],
        ];

        $time = [
            'semester' => 'Fall',
            'year' => 'First Year',
        ];
        $tables = [];
        for ($i = 0; $i < ceil($program['total_courses'] - count($completedCourses)) / 5; $i++) {

            $courses = self::getMajorCourses($time['semester'], $remainingCourses, $completedCourses);
            self::updateCourses($courses, $remaining);

            var_dump($courses);

            $tables[] = [
                'name' => 'test',
                'courses' => $courses,
            ];

            // add the new courses to the taken courses array
            foreach ($courses as $course) {
                $completedCourses[] = $course;
            }

            // move on to the next semester
            self::incrementTime($time['semester'], $time['year']);
        }*/

        if (!isset(
            $_POST['program'],
            $_POST['year'],
            $_POST['semester'],
            $_POST['art'],
            $_POST['social'],
            $_POST['electives'],
            $_POST['program'],
        )) {
            return $this->bad();
        }

        return $this->render('fourth');
    }

    /*
       private static function isCourseInArray(array $courses, string $course): bool
       {
           foreach ($courses as $c) {
               if ($c['code'] === $course) {
                   return true;
               }
           }
           return false;
       }

       private static function removeCompletedCourses(array $courses, array $completed): array
       {
           foreach ($courses as $index => $course) {
               if (in_array($course['code'], $completed)) {
                   unset($courses[$index]);
               }
           }
           return $courses;
       }

       private static function getRemainingAdditionalCourses(array $courses, array $completed, int $required): int
       {
           $coursesCompleted = 0;
           foreach ($completed as $course) {
               $courseType = explode('-', $course)[0];
               if ($courseType === 'COMP' && !self::isCourseInArray($courses, $course)) {
                   $coursesCompleted++;
               }
           }
           return $required - $coursesCompleted;
       }

       private static function getMajorCourses(string $semester, array &$coursesToTake, array $coursesCompleted): array
       {
           $coursesAdded = [];
           foreach ($coursesToTake as $index => $course) {
               if (count($coursesAdded) === 5) {
                   break;
               }

               if (Course::isAvailable($course['id'], $semester)) {
                   if (
                       empty(Course::getPrerequisites($course['id'])) ||
                       Course::hasCompletedPrerequisites($course['id'], $coursesCompleted)
                   ) {
                       $coursesAdded[] = $course;
                       unset($coursesToTake[$index]);
                   }
               }
           }
           return $coursesAdded;
       }

       private static function updateCourses(array &$courses, array &$remaining): void
       {
           for ($j = 0; $j < (5 - count($courses)); $j++) {
               if ($remaining['additional'] > 0) {

                   $courses[] = "CS course";
                   $remaining['additional']--;

               } else if ($remaining['art'] > 0) {

                   $courses[] = "Arts course";
                   $remaining['art']--;

               } else if ($remaining['social'] > 0) {

                   $courses[] = "Soc course";
                   $remaining['social']--;

               } else if ($remaining['art_social'] > 0) {

                   $courses[] = "Arts/Soc course";
                   $remaining['art_social']--;

               } else if ($remaining['electives'] > 0) {

                   $courses[] = "Elective course";
                   $remaining['electives']--;

               } else {
                   $courses[] = "No course";
               }
           }
       }

       private static function incrementTime(&$semester, &$year)
       {
           if ($semester === "Fall") {
               $semester = "Winter";
           } else if ($semester === "Winter") {
               $semester = "Fall";
               if ($year === "First Year") {
                   $year = "Second Year";
               } else if ($year === "Second Year") {
                   $year = "Third Year";
               } else if ($year === "Third Year") {
                   $year = "Fourth Year";
               }
           }
       }*/
}
