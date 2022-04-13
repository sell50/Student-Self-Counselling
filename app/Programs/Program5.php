<?php

class Program5
{
    private $num_courses = 40;
    private $num_electives = 4;
    private $total_ArtsSoc_courses = 2;
    private $min_Arts_courses = 1;
    private $min_Soc_courses = 1;
    private $compsci_courses_2000 = 1;
    private $dynamics_courses = 1;
    private $communication_courses = 1;
    private $professionalism_courses = 1;
    private $business_courses = 1;

    public $major_courses = array();

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

    public function get_major_courses($mysqli, $major_id)
    {
        if ($courses = $mysqli->query("SELECT course_code FROM courses WHERE id IN (SELECT course_id FROM major_requirements WHERE major_id = " . $major_id . ")")) {
            $rows = $courses->fetch_all(MYSQLI_NUM);
            $this->major_courses = flatten_array($mysqli, $rows);
        }
    }

    public function substitute(string $value)
    {
        if ($value == "MATH-1250") {
            return "MATH-1260";
        } else if ($value == "MATH-1720") {
            return "MATH-1760";
        } else if ($value == "STAT-2920") {
            return "STAT-2910";
        } else if ($value == "COMP-4960A") {
            return "COMP-4990A";
        } else if ($value == "COMP-4960B") {
            return "COMP-4990B";
        } else {
            return false;
        }
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
            } else if (in_array($this->substitute($course), $user_courses)) {
                $user_key = array_search($this->substitute($course), $user_courses);
                unset($user_courses[$user_key]);
                $user_courses = array_values($user_courses);
                $major_key = array_search($course, $major_courses);
                unset($major_courses[$major_key]);
                $major_courses = array_values($major_courses);
            }
        }
        return $major_courses; //we return the remaining major courses
    }

    public function requirement_cs_2000(array &$user_courses)
    {
        $viable = 0;
        foreach ($user_courses as $course) {
            $lettercode = explode("-", $course);
            if ($lettercode[0] == "COMP" && (int)$lettercode[1] >= 2000 && !in_array($course, $major_courses)) { //check if a non-major course is a COMP course
                $user_key = array_search($course, $user_courses);
                unset($user_courses[$user_key]);
                $user_courses = array_values($user_courses);
                $viable++;
            }
        }
        return ($this->compsci_courses_2000 - $viable); //return number of additional CS courses we need
    }

    public function requirement_dynamics(array &$user_courses)
    {
        $viable = 0;
        foreach ($user_courses as $course) {
            $lettercode = explode("-", $course);
            if ($lettercode[0] == "PSYC" && ($lettercode[1] == "1150" || $lettercode[1] == "2180") || $lettercode[0] == "PHIL" && ($lettercode[1] == "1290" || $lettercode[1] == "2280")) { //check if a non-major course is a business course
                $user_key = array_search($course, $user_courses);
                unset($user_courses[$user_key]);
                $user_courses = array_values($user_courses);
                $viable++;
            }
        }
        return ($this->dynamics_courses - $viable); //return number of additional dynamics courses we need
    }

    public function requirement_communication(array &$user_courses)
    {
        $viable = 0;
        foreach ($user_courses as $course) {
            $lettercode = explode("-", $course);
            if ($lettercode[0] == "CMAF" && ($lettercode[1] == "2210" || $lettercode[1] == "2100") || $lettercode[0] == "DRAM" && $lettercode[1] == "2100" || $lettercode[0] == "ENGL" && $lettercode[1] == "1001") { //check if a non-major course is a business course
                $user_key = array_search($course, $user_courses);
                unset($user_courses[$user_key]);
                $user_courses = array_values($user_courses);
                $viable++;
            }
        }
        return ($this->communication_courses - $viable); //return number of additional communication courses we need
    }

    public function requirement_professionalism(array &$user_courses)
    {
        $viable = 0;
        foreach ($user_courses as $course) {
            $lettercode = explode("-", $course);
            if ($lettercode[0] == "PHIL" && ($lettercode[1] == "2210" || $lettercode[1] == "2240") || $lettercode[0] == "GART" && $lettercode[1] == "2090" || $lettercode[0] == "ENGL" && $lettercode[1] == "1005") { //check if a non-major course is a business course
                $user_key = array_search($course, $user_courses);
                unset($user_courses[$user_key]);
                $user_courses = array_values($user_courses);
                $viable++;
            }
        }
        return ($this->professionalism_courses - $viable); //return number of additional professionalism courses we need
    }

    public function requirement_business(array &$user_courses)
    {
        $viable = 0;
        foreach ($user_courses as $course) {
            $lettercode = explode("-", $course);
            if ($lettercode[0] == "MKTG" && $lettercode[1] == "1310" || $lettercode[0] == "MGMT" && $lettercode[1] == "2400" || $lettercode[0] == "STEN" && $lettercode[1] == "1000") { //check if a non-major course is a business course
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
        foreach ($remaining_major_courses as $course) { //Try to add as many major courses as possible
            if ($courses_added == 5) { //stop when we have 5 courses
                break;
            } else if (($course == "COMP-4960A" || ($course == "COMP-4960B" && get_prereqs($mysqli, $term, $course) == array_intersect(get_prereqs($mysqli, $term, $course), $completedCoursesClean)) || $course == "COMP-4990A" || ($course == "COMP-4990B" && get_prereqs($mysqli, $term, $course) == array_intersect(get_prereqs($mysqli, $term, $course), $completedCoursesClean))) && term_available($mysqli, $term, $course) && $year == "Fourth Year") {
                $courses_this_term[] = $course;
                $key = array_search($course, $remaining_major_courses); //remove course from array of completed courses and from list of major courses
                unset($remaining_major_courses[$key]);
                $remaining_major_courses = array_values($remaining_major_courses);
                $courses_added++;
            } else if (($course != "COMP-4960A" && $course != "COMP-4960B" && $course != "COMP-4990A" && $course != "COMP-4990B") && term_available($mysqli, $term, $course) && get_prereqs($mysqli, $term, $course) == -1) { //course has no requirements, can be taken right away
                $courses_this_term[] = $course;
                $key = array_search($course, $remaining_major_courses); //remove course from array of completed courses and from list of major courses
                unset($remaining_major_courses[$key]);
                $remaining_major_courses = array_values($remaining_major_courses);
                $courses_added++;
            } else if (($course != "COMP-4960A" && $course != "COMP-4960B" && $course != "COMP-4990A" && $course != "COMP-4990B") && term_available($mysqli, $term, $course) && get_prereqs($mysqli, $term, $course) == array_intersect(get_prereqs($mysqli, $term, $course), $completedCoursesClean)) { //does the student have all requirements?
                $courses_this_term[] = $course;
                $key = array_search($course, $remaining_major_courses); //remove course from array of completed courses and from list of major courses
                unset($remaining_major_courses[$key]);
                $remaining_major_courses = array_values($remaining_major_courses);
                $courses_added++;
            }
        }
        return $courses_added;
    }

    public function buildCourseTable($mysqli, $year, $current_num_courses_added, &$courses_this_term, &$remaining_cs_2000, &$remaining_dynamics_courses, &$remaining_communication_courses, &$remaining_professionalism_courses, &$remaining_business_courses, &$remaining_arts_courses, &$remaining_soc_courses, &$remaining_artssoc_courses, &$remaining_electives)
    {
        for ($j = 0; $j < (5 - $current_num_courses_added); $j++) {
            if ($remaining_cs_2000 > 0 && $year != "First Year") {
                $courses_this_term[] = "CS course 2000+";
                $remaining_cs_2000--;
            } else if ($remaining_dynamics_courses > 0) {
                $courses_this_term[] = "Dynamics course";
                $remaining_dynamics_courses--;
            } else if ($remaining_communication_courses > 0) {
                $courses_this_term[] = "Communication course";
                $remaining_communication_courses--;
            } else if ($remaining_professionalism_courses > 0) {
                $courses_this_term[] = "Professionalism course";
                $remaining_professionalism_courses--;
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
            if ($course == "CS course 2000+") {
                $counter++;
                echo "<tr>";
                echo "<th scope=\"row\">" . $counter . "</th>";
                echo "<td scope=\"row\">" . "CS Course level 2XXX-4XXX" . "</td>";
                echo "</tr>";
            } else if ($course == "Dynamics course") {
                $counter++;
                echo "<tr>";
                echo "<th scope=\"row\">" . $counter . "</th>";
                echo "<td scope=\"row\">" . "Dynamics course" . "</td>";
                echo "</tr>";
            } else if ($course == "Communication course") {
                $counter++;
                echo "<tr>";
                echo "<th scope=\"row\">" . $counter . "</th>";
                echo "<td scope=\"row\">" . "Communication course" . "</td>";
                echo "</tr>";
            } else if ($course == "Professionalism course") {
                $counter++;
                echo "<tr>";
                echo "<th scope=\"row\">" . $counter . "</th>";
                echo "<td scope=\"row\">" . "Professionalism course" . "</td>";
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