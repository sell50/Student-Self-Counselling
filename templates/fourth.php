<html lang="en">

<?php include('includes/header.html') ?>

<body>

<div class="container py-4">

    <header class="pb-3 mb-4 border-bottom">
        <a href="/" class="d-flex align-items-center text-dark text-decoration-none">
            <span class="fs-4">Computer Science Course Scheduler - Page 4</span>
        </a>
    </header>

    <!--    <?php /*foreach ($tables as $table): */ ?>
        <h3><?php /*echo $table['name'] */ ?></h3>
        <table class="table">
            <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Code</th>
                <th scope="col">Name</th>
                <th scope="col">Semester Taken</th>
            </tr>
            </thead>
            <tbody>
            <?php /*foreach ($table['courses'] as $index => $course): */ ?>
                <tr>
                    <th scope="row"><?php /*echo $index + 1 */ ?></th>
                    <td class="fw-bold"><?php /*echo $course['code'] */ ?></td>
                    <td class="fw-bold"><?php /*echo $course['name'] */ ?></td>
                </tr>
            <?php /*endforeach; */ ?>
            </tbody>
        </table>
    --><?php /*endforeach; */ ?>

    <?php

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
    $courses = $_POST['courses'] ?? [];

    //$program = Program::find($_POST['program']);

    $completedCourses = $courses;
    $completedCoursesClean = $completedCourses;
    $num_completed_courses = count($completedCoursesClean);
    $num_completed_courses += ($num_arts + $num_soc + $num_elec);

    for ($i = 0; $i < intdiv($num_completed_courses, 5); $i++) {
        //increment_time($term, $year);
        Helper::increment_time($semester, $year);
    }

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
            Helper::increment_time($semester, $year);
            foreach ($courses_this_term as $course) { //add group of 5 courses to list of all completed courses
                $completedCoursesClean[] = $course;
            }
        }

    } else if ($program == 2) {

        $class = new Program2($_POST['program']);

        //$class->get_major_courses($mysqli, $program['id']);
        $remaining_major_courses = $class->requirement_major($completedCourses, $class->major_courses);

        // this array is not empty
        //var_dump($remaining_major_courses);

        $remaining_cs_2000 = $class->requirement_cs_2000($completedCourses);
        $remaining_cs_3000 = $class->requirement_cs_3000($completedCourses);
        $remaining_arts_courses = $class->requirement_arts($num_arts);
        $remaining_soc_courses = $class->requirement_soc($num_soc);
        $remaining_artssoc_courses = $class->requirement_ArtsOrSoc($class->get_min_Arts_courses(), $class->get_min_Arts_courses());
        $remaining_electives = $class->requirement_electives($completedCourses, $num_elec);

        for ($i = 0; $i < ceil((($class->get_num_courses()) - $num_completed_courses) / 5); $i++) { //Create enough tables of 5 to cover all terms the user needs to graduate
            $courses_this_term = [];
            $courses_added = $class->addMajorCourses($semester, $year, $remaining_major_courses, $courses_this_term, $completedCoursesClean);
            $current_num_courses_added = $courses_added; //creating this variable to store the number of courses we have added up to this point. Do this to avoid using "$j < (5-$courses_added)" because we want to increment $courses_added
            echo "" . $semester . " " . $year;
            $class->buildCourseTable($year, $current_num_courses_added, $courses_this_term, $remaining_cs_2000, $remaining_cs_3000, $remaining_arts_courses, $remaining_soc_courses, $remaining_artssoc_courses, $remaining_electives);
            Helper::increment_time($semester, $year);
            foreach ($courses_this_term as $course) { //add group of 5 courses to list of all completed courses
                $completedCoursesClean[] = $course;
            }
        }

    } else if ($program == 3) {
        $class = new Program3($_POST['program']);
        //$class->get_major_courses($mysqli, $program['id']);
        $remaining_major_courses = $class->requirement_major($completedCourses, $class->major_courses);
        $remaining_cs_courses = $class->requirement_cs($completedCourses);
        $remaining_arts_courses = $class->requirement_arts($num_arts);
        $remaining_soc_courses = $class->requirement_soc($num_soc);
        $remaining_artssoc_courses = $class->requirement_ArtsOrSoc($class->get_min_Arts_courses(), $class->get_min_Arts_courses());
        $remaining_electives = $class->requirement_electives($completedCourses, $num_elec);

        for ($i = 0; $i < ceil((($class->get_num_courses()) - $num_completed_courses) / 5); $i++) { //Create enough tables of 5 to cover all terms the user needs to graduate
            $courses_this_term = array();
            $courses_added = $class->addMajorCourses($semester, $year, $remaining_major_courses, $courses_this_term, $completedCoursesClean);
            $current_num_courses_added = $courses_added; //creating this variable to store the number of courses we have added up to this point. Do this to avoid using "$j < (5-$courses_added)" because we want to increment $courses_added
            echo "" . $semester . " " . $year;
            $class->buildCourseTable($year, $current_num_courses_added, $courses_this_term, $remaining_cs_courses, $remaining_arts_courses, $remaining_soc_courses, $remaining_artssoc_courses, $remaining_electives);
            Helper::increment_time($semester, $year);
            foreach ($courses_this_term as $course) { //add group of 5 courses to list of all completed courses
                $completedCoursesClean[] = $course;
            }
        }
    } else if ($program == 4) {
        $class = new Program4($_POST['program']);
        //$class->get_major_courses($mysqli, $program['id']);
        $remaining_major_courses = $class->requirement_major($completedCourses, $class->major_courses);
        $remaining_business_courses = $class->requirement_business($completedCourses);
        $remaining_cs_3000 = $class->requirement_cs_3000($completedCourses);
        $remaining_arts_courses = $class->requirement_arts($num_arts);
        $remaining_soc_courses = $class->requirement_soc($num_soc);
        $remaining_artssoc_courses = $class->requirement_ArtsOrSoc($class->get_min_Arts_courses(), $class->get_min_Arts_courses());
        $remaining_electives = $class->requirement_electives($completedCourses, $num_elec);

        for ($i = 0; $i < ceil((($class->get_num_courses()) - $num_completed_courses) / 5); $i++) { //Create enough tables of 5 to cover all terms the user needs to graduate
            $courses_this_term = array();
            $courses_added = $class->addMajorCourses($semester, $year, $remaining_major_courses, $courses_this_term, $completedCoursesClean);
            $current_num_courses_added = $courses_added; //creating this variable to store the number of courses we have added up to this point. Do this to avoid using "$j < (5-$courses_added)" because we want to increment $courses_added
            echo "" . $semester . " " . $year;
            $class->buildCourseTable($year, $current_num_courses_added, $courses_this_term, $remaining_business_courses, $remaining_cs_3000, $remaining_arts_courses, $remaining_soc_courses, $remaining_artssoc_courses, $remaining_electives);
            Helper::increment_time($semester, $year);
            foreach ($courses_this_term as $course) { //add group of 5 courses to list of all completed courses
                $completedCoursesClean[] = $course;
            }
        }
    } else if ($program == 5) {
        $class = new Program5($_POST['program']);
        //$class->get_major_courses($mysqli, $program['id']);
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
            $courses_added = $class->addMajorCourses($semester, $year, $remaining_major_courses, $courses_this_term, $completedCoursesClean);
            $current_num_courses_added = $courses_added; //creating this variable to store the number of courses we have added up to this point. Do this to avoid using "$j < (5-$courses_added)" because we want to increment $courses_added
            echo "" . $semester . " " . $year;
            $class->buildCourseTable($year, $current_num_courses_added, $courses_this_term, $remaining_cs_2000, $remaining_dynamics_courses, $remaining_communication_courses, $remaining_professionalism_courses, $remaining_business_courses, $remaining_arts_courses, $remaining_soc_courses, $remaining_artssoc_courses, $remaining_electives);
            Helper::increment_time($semester, $year);
            foreach ($courses_this_term as $course) { //add group of 5 courses to list of all completed courses
                $completedCoursesClean[] = $course;
            }
        }
    }

    ?>

</div>

</body>
</html>
