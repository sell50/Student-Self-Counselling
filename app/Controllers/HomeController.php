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

        /* $mysqli = new mysqli($_ENV['DB_HOST'], $_ENV['DB_USER'], $_ENV['DB_PASSWORD'], $_ENV['DB_NAME']);
         if ($mysqli->connect_error) {
             die("Connection failed: " . $mysqli->connect_error);
         }*/

        $program = $_POST['program'];
        $year = match ($_POST['year']) {
            '1' => 'First Year',
            '2' => 'Second Year',
            '3' => 'Third Year',
            '4' => 'Fourth Year',
        };
        $semester = $_POST['semester'];
        $num_arts = $_POST['art'];
        $num_soc = $_POST['social'];
        $num_elec = $_POST['electives'];

        //$program = Program::find($_POST['program']);

        $completedCourses = $_POST['courses'];
        $completedCoursesClean = $_POST['courses'];
        $num_completed_courses = count($completedCoursesClean);
        $num_completed_courses += ($num_arts + $num_soc + $num_elec);

        if ($program == 1) {

            $class = new Program1($_POST['program']);
            //$class->get_major_courses($mysqli, $program);
            $remaining_major_courses = $class->requirement_major($completedCourses, $class->major_courses);
            $remaining_cs_courses = $class->requirement_cs($completedCourses);
            $remaining_arts_courses = $class->requirement_arts($num_arts);
            $remaining_soc_courses = $class->requirement_soc($num_soc);
            $remaining_artssoc_courses = $class->requirement_ArtsOrSoc($class->get_min_Arts_courses(), $class->get_min_Arts_courses());
            $remaining_electives = $class->requirement_electives($completedCourses, $num_elec);

            for ($i = 0; $i < ceil((($class->get_num_courses()) - $num_completed_courses) / 5); $i++) { //Create enough tables of 5 to cover all terms the user needs to graduate
                $courses_this_term = [];
                $courses_added = $class->addMajorCourses($semester, $remaining_major_courses, $courses_this_term, $completedCoursesClean);

                $current_num_courses_added = $courses_added; //creating this variable to store the number of courses we have added up to this point. Do this to avoid using "$j < (5-$courses_added)" because we want to increment $courses_added
                echo "" . $semester . " " . $year;
                $class->buildCourseTable($current_num_courses_added, $courses_this_term, $remaining_cs_courses, $remaining_arts_courses, $remaining_soc_courses, $remaining_artssoc_courses, $remaining_electives);

                /*              var_dump(11);
                              exit(1);*/

                Helper::increment_time($semester, $year);
                foreach ($courses_this_term as $course) { //add group of 5 courses to list of all completed courses
                    $completedCoursesClean[] = $course;
                }
            }

        } else if ($program == "Bachelor of Computer Science (Honours)") {
            $class = new Major2();
            $class->get_major_courses($mysqli, $program['id']);
            $remaining_major_courses = $class->requirement_major($completedCourses, $class->major_courses);
            $remaining_cs_2000 = $class->requirement_cs_2000($completedCourses);
            $remaining_cs_3000 = $class->requirement_cs_3000($completedCourses);
            $remaining_arts_courses = $class->requirement_arts($num_arts);
            $remaining_soc_courses = $class->requirement_soc($num_soc);
            $remaining_artssoc_courses = $class->requirement_ArtsOrSoc($class->get_min_Arts_courses(), $class->get_min_Arts_courses());
            $remaining_electives = $class->requirement_electives($completedCourses, $num_elec);

            for ($i = 0; $i < ceil((($class->get_num_courses()) - $num_completed_courses) / 5); $i++) { //Create enough tables of 5 to cover all terms the user needs to graduate
                $courses_this_term = array();
                $courses_added = $class->addMajorCourses($mysqli, $term, $year, $remaining_major_courses, $courses_this_term, $completedCoursesClean);
                $current_num_courses_added = $courses_added; //creating this variable to store the number of courses we have added up to this point. Do this to avoid using "$j < (5-$courses_added)" because we want to increment $courses_added
                echo "" . $term . " " . $year;
                $class->buildCourseTable($mysqli, $year, $current_num_courses_added, $courses_this_term, $remaining_cs_2000, $remaining_cs_3000, $remaining_arts_courses, $remaining_soc_courses, $remaining_artssoc_courses, $remaining_electives);
                increment_time($term, $year);
                foreach ($courses_this_term as $course) { //add group of 5 courses to list of all completed courses
                    $completedCoursesClean[] = $course;
                }
            }
        } else if ($program == "Bachelor of Computer Science (Honours Applied Computing)") {
            $class = new Major3();
            $class->get_major_courses($mysqli, $program['id']);
            $remaining_major_courses = $class->requirement_major($completedCourses, $class->major_courses);
            $remaining_cs_courses = $class->requirement_cs($completedCourses);
            $remaining_arts_courses = $class->requirement_arts($num_arts);
            $remaining_soc_courses = $class->requirement_soc($num_soc);
            $remaining_artssoc_courses = $class->requirement_ArtsOrSoc($class->get_min_Arts_courses(), $class->get_min_Arts_courses());
            $remaining_electives = $class->requirement_electives($completedCourses, $num_elec);

            for ($i = 0; $i < ceil((($class->get_num_courses()) - $num_completed_courses) / 5); $i++) { //Create enough tables of 5 to cover all terms the user needs to graduate
                $courses_this_term = array();
                $courses_added = $class->addMajorCourses($mysqli, $term, $year, $remaining_major_courses, $courses_this_term, $completedCoursesClean);
                $current_num_courses_added = $courses_added; //creating this variable to store the number of courses we have added up to this point. Do this to avoid using "$j < (5-$courses_added)" because we want to increment $courses_added
                echo "" . $term . " " . $year;
                $class->buildCourseTable($mysqli, $year, $current_num_courses_added, $courses_this_term, $remaining_cs_courses, $remaining_arts_courses, $remaining_soc_courses, $remaining_artssoc_courses, $remaining_electives);
                increment_time($term, $year);
                foreach ($courses_this_term as $course) { //add group of 5 courses to list of all completed courses
                    $completedCoursesClean[] = $course;
                }
            }
        } else if ($program == "Bachelor of Science (Honours Computer Information Systems)") {
            $class = new Major4();
            $class->get_major_courses($mysqli, $program['id']);
            $remaining_major_courses = $class->requirement_major($completedCourses, $class->major_courses);
            $remaining_business_courses = $class->requirement_business($completedCourses);
            $remaining_cs_3000 = $class->requirement_cs_3000($completedCourses);
            $remaining_arts_courses = $class->requirement_arts($num_arts);
            $remaining_soc_courses = $class->requirement_soc($num_soc);
            $remaining_artssoc_courses = $class->requirement_ArtsOrSoc($class->get_min_Arts_courses(), $class->get_min_Arts_courses());
            $remaining_electives = $class->requirement_electives($completedCourses, $num_elec);

            for ($i = 0; $i < ceil((($class->get_num_courses()) - $num_completed_courses) / 5); $i++) { //Create enough tables of 5 to cover all terms the user needs to graduate
                $courses_this_term = array();
                $courses_added = $class->addMajorCourses($mysqli, $term, $year, $remaining_major_courses, $courses_this_term, $completedCoursesClean);
                $current_num_courses_added = $courses_added; //creating this variable to store the number of courses we have added up to this point. Do this to avoid using "$j < (5-$courses_added)" because we want to increment $courses_added
                echo "" . $term . " " . $year;
                $class->buildCourseTable($mysqli, $year, $current_num_courses_added, $courses_this_term, $remaining_business_courses, $remaining_cs_3000, $remaining_arts_courses, $remaining_soc_courses, $remaining_artssoc_courses, $remaining_electives);
                increment_time($term, $year);
                foreach ($courses_this_term as $course) { //add group of 5 courses to list of all completed courses
                    $completedCoursesClean[] = $course;
                }
            }
        } else if ($program == "Bachelor of Science (Honours Computer Science with Software Engineering Specialization)") {
            $class = new Major5();
            $class->get_major_courses($mysqli, $program['id']);
            $remaining_major_courses = $class->requirement_major($completedCourses, $class->major_courses);
            $remaining_cs_2000 = $class->requirement_cs_2000($completedCourses);
            $remaining_arts_courses = $class->requirement_arts($num_arts);
            $remaining_soc_courses = $class->requirement_soc($num_soc);
            $remaining_artssoc_courses = $class->requirement_ArtsOrSoc($class->get_min_Arts_courses(), $class->get_min_Arts_courses());
            $remaining_dynamics_courses = $class->requirement_dynamics($completedCourses);
            $remaining_communication_courses = $class->requirement_communication($completedCourses);
            $remaining_professionalism_courses = $class->requirement_professionalism($completedCourses);
            $remaining_business_courses = $class->requirement_business($completedCourses);
            $remaining_electives = $class->requirement_electives($completedCourses, $num_elec);

            for ($i = 0; $i < ceil((($class->get_num_courses()) - $num_completed_courses) / 5); $i++) { //Create enough tables of 5 to cover all terms the user needs to graduate
                $courses_this_term = array();
                $courses_added = $class->addMajorCourses($mysqli, $term, $year, $remaining_major_courses, $courses_this_term, $completedCoursesClean);
                $current_num_courses_added = $courses_added; //creating this variable to store the number of courses we have added up to this point. Do this to avoid using "$j < (5-$courses_added)" because we want to increment $courses_added
                echo "" . $term . " " . $year;
                $class->buildCourseTable($mysqli, $year, $current_num_courses_added, $courses_this_term, $remaining_cs_2000, $remaining_dynamics_courses, $remaining_communication_courses, $remaining_professionalism_courses, $remaining_business_courses, $remaining_arts_courses, $remaining_soc_courses, $remaining_artssoc_courses, $remaining_electives);
                increment_time($term, $year);
                foreach ($courses_this_term as $course) { //add group of 5 courses to list of all completed courses
                    $completedCoursesClean[] = $course;
                }
            }
        }

        return $this->render('fourth', [
            'tables' => [],
        ]);
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
