<?php

class Program4
{
    private int $program_id;
    private int $num_courses;
    private int $num_electives;
    private int $total_ArtsSoc_courses;
    private int $min_Arts_courses;
    private int $min_Soc_courses;
    private int $compsci_courses;
    public array $major_courses = [];

    private int $compsci_courses_3000;
    private int $business_courses;

    public function __construct(int $program)
    {
        $program = Program::find($program);

        $this->program_id = $program['id'];
        $this->num_courses = $program['total_courses'];
        $this->num_electives = $program['elective_courses'];
        $this->total_ArtsSoc_courses = $program['art_social_courses'];
        $this->min_Arts_courses = $program['art_courses'];
        $this->min_Soc_courses = $program['social_courses'];
        $this->compsci_courses = $program['additional_courses'];    //program does not have this requirement
        $this->major_courses = Program::getRequiredCourses($program['id'], true);
        array_push($this->major_courses, "ACCT-1510", "ACCT-2550", "FINA-2700", "MKTG-1310", "STEN-1000", "ECON-1100", "ECON-1110"); //add business courses to list of majors

        $compsci_courses_3000 = 2;
        $business_courses = 4;

    }

    public function get_num_courses()
    {
        return $this->num_courses;
    }

    public function get_min_Arts_courses()
    {
        return $this->min_Arts_courses;
    }

    public function get_min_Soc_courses()
    {
        return $this->min_Soc_courses;
    }

    public function requirement_major(array &$user_courses, array &$major_courses)
    {
        foreach ($major_courses as $course) {
            if (in_array($course, $user_courses)) { //check if user has completed a course within the list of major courses, or if they have completed its substitute
                //We must call $this->substitute to let the compiler know we are referring to the "substitute" function located in this class
                $user_key = array_search($course, $user_courses); //remove course from array of completed courses and from list of major courses
                unset($user_courses[$user_key]);
                $user_courses = array_values($user_courses);
                $major_key = array_search($course, $major_courses);
                unset($major_courses[$major_key]);
                $major_courses = array_values($major_courses);
            } else if (in_array(Helper::substitute($course, $this->program_id), $user_courses)) {
                $user_key = array_search(Helper::substitute($course, $this->program_id), $user_courses);
                unset($user_courses[$user_key]);
                $user_courses = array_values($user_courses);
                $major_key = array_search($course, $major_courses);
                unset($major_courses[$major_key]);
                $major_courses = array_values($major_courses);
            }
        }
        return $major_courses; //we return the remaining major courses
    }

    public function requirement_cs_3000(array &$user_courses)
    {
        $viable = 0;
        foreach ($user_courses as $course) {
            $lettercode = explode("-", $course);
            if ($lettercode[0] == "COMP" && (int)$lettercode[1] >= 3000 && !in_array($course, $major_courses)) { //check if a non-major course is a COMP course
                $user_key = array_search($course, $user_courses);
                unset($user_courses[$user_key]);
                $user_courses = array_values($user_courses);
                $viable++;
            }
        }
        return ($this->compsci_courses_3000 - $viable); //return number of additional CS courses we need
    }

    public function requirement_business(array &$user_courses)
    {
        $viable = 0;
        foreach ($user_courses as $course) {
            $lettercode = explode("-", $course);
            $exceptions = array("MSCI-2020", "MSCI-2130", "MSCI-2200", "MSCI-3200");
            if (($lettercode[0] == "ACCT" || $lettercode[0] == "MGMT" || $lettercode[0] == "MKTG" || $lettercode[0] == "STEN" || $lettercode[0] == "MSCI") && !in_array($course, $major_courses) && !in_array($course, $exceptions)) { //check if a non-major course is a business course
                $user_key = array_search($course, $user_courses);
                unset($user_courses[$user_key]);
                $user_courses = array_values($user_courses);
                $viable++;
            }
        }
        return ($this->business_courses - $viable); //return number of additional business courses we need
    }

    public function requirement_arts(int $num_Arts_courses)
    { //user still needs an arts course
        return $this->min_Arts_courses - $num_Arts_courses;
    }

    public function requirement_soc(int $num_Soc_courses)
    { //user still needs a soc course
        return $this->min_Soc_courses - $num_Soc_courses;
    }

    public function requirement_ArtsOrSoc(int $num_Arts_courses, int $num_Soc_courses)
    { //user still needs X arts or soc courses
        return ($this->total_ArtsSoc_courses - ($num_Arts_courses + $num_Soc_courses));
    }

    public function requirement_electives(array &$user_courses, $electives_completed)
    { //Take extra courses that were completed but don't account for any other requirement as extra electives
        $count = 0;
        foreach ($user_courses as $course) {
            $user_key = array_search($course, $user_courses);
            unset($user_courses[$user_key]);
            $user_courses = array_values($user_courses);
            $count++;
        }
        return $this->num_electives - $electives_completed - $count;
    }

    public function addMajorCourses($mysqli, $term, $year, &$remaining_major_courses, &$courses_this_term, $completedCoursesClean)
    {
        $courses_added = 0;
        $first_year_business = array("ECON-1100", "ECON-1110", "STEN-1000", "MKTG-1310", "ACCT-1510"); //list of first year business courses which are important for this program
        foreach ($remaining_major_courses as $course) { //Try to add as many major courses as possible
            if ($courses_added == 5) { //stop when we have 5 courses
                break;
            } else if (($course == "COMP-4990A" || ($course == "COMP-4990B" && Course::getPrerequisites($course, true) == array_intersect(Course::getPrerequisites($course, true), $completedCoursesClean))) && Semester::isCourseAvailable($term, $course) && $year == "Fourth Year") {
                $courses_this_term[] = $course;
                $key = array_search($course, $remaining_major_courses); //remove course from array of completed courses and from list of major courses
                unset($remaining_major_courses[$key]);
                $remaining_major_courses = array_values($remaining_major_courses);
                $courses_added++;
            } else if (($course != "COMP-4990A" && $course != "COMP-4990B") && Semester::isCourseAvailable($term, $course) && empty(Course::getPrerequisites($course, true))) { //course has no requirements, can be taken right away
                $courses_this_term[] = $course;
                $key = array_search($course, $remaining_major_courses);
                unset($remaining_major_courses[$key]);
                $remaining_major_courses = array_values($remaining_major_courses);
                $courses_added++;
            } else if (($course != "COMP-4990A" && $course != "COMP-4990B") && Semester::isCourseAvailable($term, $course) && Course::getPrerequisites($course, true) == array_intersect(Course::getPrerequisites($course, true), $completedCoursesClean)) { //does the student have all requirements?
                $courses_this_term[] = $course;
                $key = array_search($course, $remaining_major_courses);
                unset($remaining_major_courses[$key]);
                $remaining_major_courses = array_values($remaining_major_courses);
                $courses_added++;
            }

            //I got a bit lazy with this, I didn't want to add the business courses to the database because I would have to add a lot of entries,
            //What we're doing here is trying to add any business courses we can after we add the comp sci/math major courses.
            else if ($course == "ECON-1100" || $course == "ECON-1110" || $course == "STEN-1000" || $course == "MKTG-1310" || $course == "ACCT-1510") { //ECON-1110 is the part 2 of ECON-1100 but doesn't strictly require that you have ECON-1100 as a prerequisite
                $courses_this_term[] = $course;
                $key = array_search($course, $remaining_major_courses);
                unset($remaining_major_courses[$key]);
                $remaining_major_courses = array_values($remaining_major_courses);
                $courses_added++;
            } else if (($course == "ACCT-2550" || $course == "FINA-2700") && in_array($first_year_business, $completedCoursesClean)) {
                $courses_this_term[] = $course;
                $key = array_search($course, $remaining_major_courses);
                unset($remaining_major_courses[$key]);
                $remaining_major_courses = array_values($remaining_major_courses);
                $courses_added++;
            }
        }
        return $courses_added;
    }

    public function buildCourseTable($mysqli, $year, $current_num_courses_added, &$courses_this_term, &$remaining_business_courses, &$remaining_cs_3000, &$remaining_arts_courses, &$remaining_soc_courses, &$remaining_artssoc_courses, &$remaining_electives)
    {
        for ($j = 0; $j < (5 - $current_num_courses_added); $j++) {
            if ($remaining_cs_3000 > 0 && $year != "First Year" && $year != "Second Year") {
                $courses_this_term[] = "CS course 3000+";
                $remaining_cs_3000--;
            } else if ($remaining_business_courses > 0) {
                $courses_this_term[] = "Business course";
                $remaining_business_courses--;
            } else if ($remaining_arts_courses > 0) {
                $courses_this_term[] = "Arts course";
                $remaining_arts_courses--;
            } else if ($remaining_soc_courses > 0) {
                $courses_this_term[] = "Soc course";
                $remaining_soc_courses--;
            } else if ($remaining_artssoc_courses > 0) {
                $courses_this_term[] = "Arts/Soc course";
                $remaining_artssoc_courses--;
            } else if ($remaining_electives > 0) {
                $courses_this_term[] = "Elective course";
                $remaining_electives--;
            } else {
                $courses_this_term[] = "No course";
            }
        }
        //echo $term . ": ";

        //Create tables with HTML.
        echo "
			<div>
			<table class=\"table\" text-align = \"left\">
			  <thead>
				<tr>
				  <th scope=\"col\">#</th>
				  <th align=\"left\"scope=\"col\">Course</th>
				</tr>
			  </thead>
			  <tbody>
		";

        $counter = 0;
        foreach ($courses_this_term as $course) { //Read list of courses for this term, query db for related information (course name) and add to table
            if ($course == "CS course 3000+") {
                $counter++;
                echo "<tr>";
                echo "<th scope=\"row\">" . $counter . "</th>";
                echo "<td scope=\"row\">" . "CS Course level 3XXX-4XXX" . "</td>";
                echo "</tr>";
            } else if ($course == "Business course") {
                $counter++;
                echo "<tr>";
                echo "<th scope=\"row\">" . $counter . "</th>";
                echo "<td scope=\"row\">" . "Business course" . "</td>";
                echo "</tr>";
            } else if ($course == "Arts course") {
                $counter++;
                echo "<tr>";
                echo "<th scope=\"row\">" . $counter . "</th>";
                echo "<td scope=\"row\">" . "Arts/languages course" . "</td>";
                echo "</tr>";
            } else if ($course == "Soc course") {
                $counter++;
                echo "<tr>";
                echo "<th scope=\"row\">" . $counter . "</th>";
                echo "<td scope=\"row\">" . "Social science course" . "</td>";
                echo "</tr>";
            } else if ($course == "Arts/Soc course") {
                $counter++;
                echo "<tr>";
                echo "<th scope=\"row\">" . $counter . "</th>";
                echo "<td scope=\"row\">" . "Arts/languages or Social science course" . "</td>";
                echo "</tr>";
            } else if ($course == "Elective course") {
                $counter++;
                echo "<tr>";
                echo "<th scope=\"row\">" . $counter . "</th>";
                echo "<td scope=\"row\">" . "Elective course" . "</td>";
                echo "</tr>";
            } else if ($course == "No course") {
                $counter++;
                echo "<tr>";
                echo "<th scope=\"row\">" . $counter . "</th>";
                echo "<td scope=\"row\">" . "No course required" . "</td>";
                echo "</tr>";
            } else {
                if ($getcourses = $mysqli->query("SELECT name FROM courses WHERE course_code = \"" . $course . "\"")) {
                    $row = $getcourses->fetch_row();
                    $counter++;
                    echo "<tr>";
                    echo "<th style = \"text-align: left\" scope=\"row\">" . $counter . "</th>";
                    echo "<td scope=\"row\">" . $course . " - " . $row[0] . "</td>";
                    echo "</tr>";
                } else {
                    echo "query failed: " . $mysqli->error;
                }
            }
        }

        echo "
			</tbody>
			</table>
		</div>
		";
    }
}

?>