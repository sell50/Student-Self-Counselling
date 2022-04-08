<?php
session_start();
include "env.php";
//include "classes.php";

//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);

//create connection
$conn = mysqli_connect();
$mysqli = new mysqli($servername, $username, $password, $db_name);

// Check connection
if ($mysqli -> connect_errno) {
  echo "Failed to connect to MySQL: " . $mysqli -> connect_error;
  exit();
}

/*
foreach($_POST as $key=>$value)
{
	echo "$key=$value" . " ";
}*/

$num_arts = $_SESSION["ArtsCourses"];
$num_soc = $_SESSION["SocCourses"];
$num_elec = $_SESSION["ElecCourses"];
$program = $_SESSION["program"];
$year = $_SESSION["year"];
$term = $_SESSION["term"];
$major_courses = $_SESSION["major_courses"];
$incomplete_count = 1;
$complete_count = 0;
$major_id = $_SESSION["major_id"];
$completedCourses = array(); //This array represents the list of courses the user has completed, and we will be removing elements from this list as the courses are used to satisfy requirements
$completedCoursesClean = array(); //This array is the same as completedCourses but we will keep this array unmodified for situations where we need to know all the courses we have completed

function term_available($mysqli, $term, $coursecode){
	$numeric_code = explode("-", $coursecode);
	if($numeric_code[1] == "4960A"){
		if($numReqs = $mysqli -> query("SELECT * FROM course_offerings WHERE course_id = 49601")){ //query db for all rows in course_offerings
			$row = $numReqs -> fetch_all(MYSQLI_NUM); 
			$temp = flatten_array($mysqli, $row);
			if(in_array($term, $temp)){
				return 1;
			}
		}
		return 0;
	}
	else if($numeric_code[1] == "4960B"){
		if($numReqs = $mysqli -> query("SELECT * FROM course_offerings WHERE course_id = 49602")){ //query db for all rows in course_offerings
			$row = $numReqs -> fetch_all(MYSQLI_NUM); 
			$temp = flatten_array($mysqli, $row);
			if(in_array($term, $temp)){
				return 1;
			}
		}
		return 0;
	}
	else if($numeric_code[1] == "4990A"){
		if($numReqs = $mysqli -> query("SELECT * FROM course_offerings WHERE course_id = 49901")){ //query db for all rows in course_offerings
			$row = $numReqs -> fetch_all(MYSQLI_NUM); 
			$temp = flatten_array($mysqli, $row);
			if(in_array($term, $temp)){
				return 1;
			}
		}
		return 0;
	}
	else if($numeric_code[1] == "4990B"){
		if($numReqs = $mysqli -> query("SELECT * FROM course_offerings WHERE course_id = 49902")){ //query db for all rows in course_offerings
			$row = $numReqs -> fetch_all(MYSQLI_NUM); 
			$temp = flatten_array($mysqli, $row);
			if(in_array($term, $temp)){
				return 1;
			}
		}
		return 0;
	}
	else if($numReqs = $mysqli -> query("SELECT * FROM course_offerings WHERE course_id = " . $numeric_code[1])){ //query db for all rows in course_offerings
		$row = $numReqs -> fetch_all(MYSQLI_NUM); 
		$temp = flatten_array($mysqli, $row);
		if(in_array($term, $temp)){
			return 1;
		}
		else{
			return 0;
		}
	}
	return 0;
}

function get_prereqs($mysqli, $term, $coursecode){ //have to pass $mysqli as an argument because we are using a global variable within a local scope
	$numeric_code = explode("-", $coursecode);
	if($numeric_code[1] == "4960B"){
		if($numReqs = $mysqli -> query("SELECT course_code FROM courses WHERE id IN (SELECT req1 FROM 1_requirements WHERE course_id = 49602)")){ //query db for all rows in course_offerings
			$row = $numReqs -> fetch_all(MYSQLI_NUM); 
			$temp = flatten_array($mysqli, $row);
			return $temp;
		}
		return 0;
	}
	else if($numeric_code[1] == "4990B"){
		if($numReqs = $mysqli -> query("SELECT course_code FROM courses WHERE id IN (SELECT req1 FROM 1_requirements WHERE course_id = 49902)")){ //query db for all rows in course_offerings
			$row = $numReqs -> fetch_all(MYSQLI_NUM); 
			$temp = flatten_array($mysqli, $row);
			return $temp;
		}
		return 0;
	}
	else if($numReqs = $mysqli -> query("SELECT num_requirements FROM course_requirements WHERE course_id = " . $numeric_code[1])){
		$row = $numReqs -> fetch_row();
		if($row && $row[0] == 1 && $reqsList = $mysqli -> query("SELECT course_code FROM courses WHERE id IN (SELECT req1 FROM 1_requirements WHERE course_id = " . $numeric_code[1].")")){
			$reqsRow = $reqsList -> fetch_row();
			return $reqsRow;
		}
		else if($row && $row[0] == 2 && $reqsList = $mysqli -> query("SELECT course_code FROM courses WHERE id IN (SELECT req1 FROM 2_requirements WHERE course_id = " . $numeric_code[1].") OR id IN (SELECT req2 FROM 2_requirements WHERE course_id = " . $numeric_code[1].")")){
			$reqsRow = $reqsList -> fetch_all(MYSQLI_NUM);
			$temp = flatten_array($mysqli, $reqsRow);
			return $temp;
		}
		else if($row && $row[0] == 3 && $reqsList = $mysqli -> query("SELECT course_code FROM courses WHERE id IN (SELECT req1 FROM 3_requirements WHERE course_id = " . $numeric_code[1].") OR id IN (SELECT req2 FROM 3_requirements WHERE course_id = " . $numeric_code[1].") OR id IN (SELECT req3 FROM 3_requirements WHERE course_id = " . $numeric_code[1].")")){
			$reqsRow = $reqsList -> fetch_all(MYSQLI_NUM);
			$temp = flatten_array($mysqli, $reqsRow);
			return $temp;
		}
		else if($row && $row[0] == 4 && $reqsList = $mysqli -> query("SELECT course_code FROM courses WHERE id IN (SELECT req1 FROM 4_requirements WHERE course_id = " . $numeric_code[1].") OR id IN (SELECT req2 FROM 4_requirements WHERE course_id = " . $numeric_code[1].") OR id IN (SELECT req3 FROM 4_requirements WHERE course_id = " . $numeric_code[1].") OR id IN (SELECT req4 FROM 4_requirements WHERE course_id = " . $numeric_code[1].")")){
			$reqsRow = $reqsList -> fetch_all(MYSQLI_NUM);
			$temp = flatten_array($mysqli, $reqsRow);
			return $temp;
		}
		else{ //return -1 if course has no prerequisites
			return -1;
		}
	}
	else{
		echo "query failed: " . $mysqli->error;
	}
}

function flatten_array($mysqli, $array){
	$temp = array();
	for ($i = 0; $i < count($array); $i++) {
		for ($j = 0; $j < count($array[$i]); $j++) {
			$temp[] = $array[$i][$j];
		}
	}
	return $temp;
}

function increment_time(&$term, &$year){
	if($term == "Fall"){ //switch to next term (we ignore summer)
		$term = "Winter";
	}
	else if($term == "Winter"){
		$term = "Fall";
		if($year == "First Year"){
			$year = "Second Year";
		}
		else if($year == "Second Year"){
			$year = "Third Year";
		}
		else if($year == "Third Year"){
			$year = "Fourth Year";
		}
	}
}

class Major1{ //Bachelor of Computer Science (General)
	private $num_courses = 30;
	private $num_electives = 11;
	private $total_ArtsSoc_courses = 2;
	private $min_Arts_courses = 1;
	private $min_Soc_courses = 1;
	private $compsci_courses = 2;
	
	public $major_courses = array();
	
	public function get_num_courses(){
		return $this -> num_courses;
	}
	
	public function get_min_Arts_courses(){
		return $this -> min_Arts_courses;
	}
	public function get_min_Soc_courses(){
		return $this -> min_Soc_courses;
	}
	
	public function get_major_courses($mysqli, $major_id){
		if($courses = $mysqli -> query("SELECT course_code FROM courses WHERE id IN (SELECT course_id FROM major_requirements WHERE major_id = " . $major_id . ")")){
			$rows = $courses -> fetch_all(MYSQLI_NUM);
			$this -> major_courses = flatten_array($mysqli, $rows);
		}
	}

	public function substitute(string $value){
		if($value == "MATH-1250"){
			return "MATH-1260";
		}
		else if($value == "MATH-1720"){
			return "MATH-1760";
		}
		else if($value == "COMP-3340"){
			return "COMP-3670";
		}
		else{
			return false;
		}
	}

	public function requirement_major(array &$user_courses, array &$major_courses){
		foreach ($major_courses as $course){
			if(in_array($course, $user_courses)){ //check if user has completed a course within the list of major courses, or if they have completed its substitute
												//We must call $this->substitute to let the compiler know we are referring to the "substitute" function located in this class
				$user_key = array_search($course, $user_courses); //remove course from array of completed courses and from list of major courses
				unset($user_courses[$user_key]);
				$user_courses=array_values($user_courses);
				$major_key = array_search($course, $major_courses);
				unset($major_courses[$major_key]);
				$major_courses = array_values($major_courses);
			}
			else if(in_array($this->substitute($course), $user_courses)){
				$user_key = array_search($this->substitute($course), $user_courses); 
				unset($user_courses[$user_key]);
				$user_courses=array_values($user_courses);
				$major_key = array_search($course, $major_courses);
				unset($major_courses[$major_key]);
				$major_courses = array_values($major_courses);
			}
		}
		return $major_courses; //we return the remaining major courses
	}
	
	public function requirement_cs(array &$user_courses){ //check for "additional computer science" requirements. For BCS (General) they are not limited by course level.
		$viable=0;
		foreach ($user_courses as $course){
			$lettercode = explode("-", $course);
			if($lettercode[0] == "COMP" && !in_array($course, $major_courses)){ //check if a non-major course is a COMP course
				$user_key = array_search($course, $user_courses);
				unset($user_courses[$user_key]);
				$user_courses = array_values($user_courses);
				$viable++;
			}
		}
		return ($this -> compsci_courses - $viable); //return number of additional CS courses we need
	}
	
	public function requirement_arts(int $num_Arts_courses){ //user still needs an arts course
		return $this->min_Arts_courses - $num_Arts_courses;
	}
	public function requirement_soc(int $num_Soc_courses){ //user still needs a soc course
		return $this -> min_Soc_courses - $num_Soc_courses;
	}
	public function requirement_ArtsOrSoc(int $num_Arts_courses, int $num_Soc_courses){ //user still needs X arts or soc courses
		return ($this -> total_ArtsSoc_courses - ($num_Arts_courses + $num_Soc_courses));
	}
	
	public function requirement_electives(array &$user_courses, $electives_completed){ //Take extra courses that were completed but don't account for any other requirement as extra electives
		$count = 0;
		foreach($user_courses as $course){
			$user_key = array_search($course, $user_courses);
			unset($user_courses[$user_key]);
			$user_courses = array_values($user_courses);
			$count++;
		}
		return $this -> num_electives - $electives_completed - $count;
	}
	
	public function addMajorCourses($mysqli, $term, &$remaining_major_courses, &$courses_this_term, $completedCoursesClean){
		$courses_added = 0;
		foreach($remaining_major_courses as $course){ //Try to add as many major courses as possible
			if($courses_added == 5){ //stop when we have 5 courses
				break;
			}
			if(term_available($mysqli, $term, $course) && get_prereqs($mysqli, $term, $course) == -1){ //course has no requirements, can be taken right away
				$courses_this_term[] = $course;
				$key = array_search($course, $remaining_major_courses); //remove course from array of completed courses and from list of major courses
				unset($remaining_major_courses[$key]);
				$remaining_major_courses = array_values($remaining_major_courses);
				$courses_added++;
			}
			else if(term_available($mysqli, $term, $course) && get_prereqs($mysqli, $term, $course) == array_intersect(get_prereqs($mysqli, $term, $course), $completedCoursesClean)){
				$courses_this_term[] = $course;
				$key = array_search($course, $remaining_major_courses); //remove course from array of completed courses and from list of major courses
				unset($remaining_major_courses[$key]);
				$remaining_major_courses = array_values($remaining_major_courses);
				$courses_added++;
			}
		}
		return $courses_added;
	}
	
	public function buildCourseTable($mysqli, $current_num_courses_added, &$courses_this_term, &$remaining_cs_courses, &$remaining_arts_courses, &$remaining_soc_courses, &$remaining_artssoc_courses, $remaining_electives){
		for($j = 0; $j < (5-$current_num_courses_added); $j++){
			if($remaining_cs_courses > 0){
				$courses_this_term[] = "CS course";
				$remaining_cs_courses--;
			}
			else if($remaining_arts_courses > 0){
				$courses_this_term[] = "Arts course";
				$remaining_arts_courses--;
			}
			else if($remaining_soc_courses > 0){
				$courses_this_term[] = "Soc course";
				$remaining_soc_courses--;
			}
			else if($remaining_artssoc_courses > 0){
				$courses_this_term[] = "Arts/Soc course";
				$remaining_artssoc_courses--;
			}
			else if($remaining_electives > 0){
				$courses_this_term[] = "Elective course";
				$remaining_electives--;
			}
			else{
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
		foreach($courses_this_term as $course){ //Read list of courses for this term, query db for related information (course name) and add to table
			if($course == "CS course"){
				$counter++;
				echo "<tr>";
				echo "<th style = \"text-align: left\" scope=\"row\">". $counter . "</th>";
				echo "<td>". "Computer science course" ."</td>";
				echo "</tr>";
			}
			else if($course == "Arts course"){
				$counter++;
				echo "<tr>";
				echo "<th scope=\"row\">". $counter . "</th>";
				echo "<td scope=\"row\">". "Arts/languages course" ."</td>";
				echo "</tr>";
			}
			else if($course == "Soc course"){
				$counter++;
				echo "<tr>";
				echo "<th scope=\"row\">". $counter . "</th>";
				echo "<td scope=\"row\">". "Social science course" ."</td>";
				echo "</tr>";
			}
			else if($course == "Arts/Soc course"){
				$counter++;
				echo "<tr>";
				echo "<th scope=\"row\">". $counter . "</th>";
				echo "<td scope=\"row\">". "Arts/languages or Social science course" ."</td>";
				echo "</tr>";
			}
			else if($course == "Elective course"){
				$counter++;
				echo "<tr>";
				echo "<th scope=\"row\">". $counter . "</th>";
				echo "<td scope=\"row\">". "Elective course" ."</td>";
				echo "</tr>";
			}
			else if($course == "No course"){
				$counter++;
				echo "<tr>";
				echo "<th scope=\"row\">". $counter . "</th>";
				echo "<td scope=\"row\">". "No course required" ."</td>";
				echo "</tr>";
			}
			else{
				if($getcourses = $mysqli -> query("SELECT name FROM courses WHERE course_code = \"". $course ."\"")){
					$row = $getcourses -> fetch_row();
					$counter++;
					echo "<tr>";
					echo "<th style = \"text-align: left\" scope=\"row\">". $counter . "</th>";
					echo "<td scope=\"row\">". $course. " - ". $row[0] ."</td>";
					echo "</tr>";
				}
				else{
					echo "query failed: " . $mysqli -> error;
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

class Major2{ //Bachelor of Computer Science (Honors)
	private $num_courses = 40;
	private $num_electives = 7;
	private $total_ArtsSoc_courses = 3;
	private $min_Arts_courses = 1;
	private $min_Soc_courses = 1;
	private $compsci_courses_3000 = 1;
	private $compsci_courses_2000 = 3;
	
	public $major_courses = array();
	
	public function get_num_courses(){
		return $this -> num_courses;
	}

	public function get_major_courses($mysqli, $major_id){
		if($courses = $mysqli -> query("SELECT course_code FROM courses WHERE id IN (SELECT course_id FROM major_requirements WHERE major_id = " . $major_id . ")")){
			$rows = $courses -> fetch_all(MYSQLI_NUM);
			$this -> major_courses = flatten_array($mysqli, $rows);
		}
	}
	
	public function get_min_Arts_courses(){
		return $this -> min_Arts_courses;
	}
	public function get_min_Soc_courses(){
		return $this -> min_Soc_courses;
	}

	public function substitute(string $value){
		if($value == "MATH-1250"){
			return "MATH-1260";
		}
		else if($value == "MATH-1720"){
			return "MATH-1760";
		}
		else if($value == "MATH-3940"){
			return "MATH-3800";
		}
		else if($value == "STAT-2910"){
			return "STAT-2920";
		}
		else if($value == "COMP-4960A"){
			return "COMP-4990A";
		}
		else if($value == "COMP-4960B"){
			return "COMP-4990B";
		}
		else{
			return false;
		}
	}

	public function requirement_major(array &$user_courses, array &$major_courses){
		foreach ($major_courses as $course){
			if(in_array($course, $user_courses)){ //check if user has completed a course within the list of major courses, or if they have completed its substitute
												//We must call $this->substitute to let the compiler know we are referring to the "substitute" function located in this class
				$user_key = array_search($course, $user_courses); //remove course from array of completed courses and from list of major courses
				unset($user_courses[$user_key]);
				$user_courses=array_values($user_courses);
				$major_key = array_search($course, $major_courses);
				unset($major_courses[$major_key]);
				$major_courses = array_values($major_courses);
			}
			else if(in_array($this->substitute($course), $user_courses)){
				$user_key = array_search($this->substitute($course), $user_courses); 
				unset($user_courses[$user_key]);
				$user_courses=array_values($user_courses);
				$major_key = array_search($course, $major_courses);
				unset($major_courses[$major_key]);
				$major_courses = array_values($major_courses);
			}
		}
		return $major_courses; //we return the remaining major courses
	}
	
	public function requirement_cs_2000(array &$user_courses){ //check for "additional computer science" requirements. For BCS Honors they ARE limited by course level.
		$viable=0;
		foreach ($user_courses as $course){
			$lettercode = explode("-", $course);
			$exceptions = array("COMP-2057", "COMP-2077", "COMP-2097", "COMP-2707", "COMP-3057", "COMP-3077"); //These courses do not count for this requirement
			if($lettercode[0] == "COMP" && (int)$lettercode[1] >= 2000 && !in_array($course, $major_courses) && !in_array($course, $exceptions)){ //check if a non-major course is a COMP course
				$user_key = array_search($course, $user_courses);
				unset($user_courses[$user_key]);
				$user_courses = array_values($user_courses);
				$viable++;
			}
		}
		return ($this -> compsci_courses_2000 - $viable); //return number of additional CS courses we need
	}
	
	public function requirement_cs_3000(array &$user_courses){
		$viable=0;
		foreach ($user_courses as $course){
			$lettercode = explode("-", $course);
			if($lettercode[0] == "COMP" && (int)$lettercode[1] >= 3000 && !in_array($course, $major_courses)){ //check if a non-major course is a COMP course
				$user_key = array_search($course, $user_courses);
				unset($user_courses[$user_key]);
				$user_courses = array_values($user_courses);
				$viable++;
			}
		}
		return ($this -> compsci_courses_3000 - $viable); //return number of additional CS courses we need
	}
	
	public function requirement_arts(int $num_Arts_courses){ //user still needs an arts course
		return $this->min_Arts_courses - $num_Arts_courses;
	}
	public function requirement_soc(int $num_Soc_courses){ //user still needs a soc course
		return $this -> min_Soc_courses - $num_Soc_courses;
	}
	public function requirement_ArtsOrSoc(int $num_Arts_courses, int $num_Soc_courses){ //user still needs X arts or soc courses
		return ($this -> total_ArtsSoc_courses - ($num_Arts_courses + $num_Soc_courses));
	}
	
	public function requirement_electives(array &$user_courses, $electives_completed){ //Take extra courses that were completed but don't account for any other requirement as extra electives
		$count = 0;
		foreach($user_courses as $course){
			$user_key = array_search($course, $user_courses);
			unset($user_courses[$user_key]);
			$user_courses = array_values($user_courses);
			$count++;
		}
		return $this -> num_electives - $electives_completed - $count;
	}
	
	public function addMajorCourses($mysqli, $term, $year, &$remaining_major_courses, &$courses_this_term, $completedCoursesClean){
		$courses_added = 0;
		foreach($remaining_major_courses as $course){ //Try to add as many major courses as possible
			if($courses_added == 5){ //stop when we have 5 courses
				break;
			}
			else if(($course == "COMP-4960A" || ($course == "COMP-4960B" && get_prereqs($mysqli, $term, $course) == array_intersect(get_prereqs($mysqli, $term, $course), $completedCoursesClean)) || $course == "COMP-4990A" || ($course == "COMP-4990B" && get_prereqs($mysqli, $term, $course) == array_intersect(get_prereqs($mysqli, $term, $course), $completedCoursesClean))) && term_available($mysqli, $term, $course) && $year == "Fourth Year"){
				$courses_this_term[] = $course;
				$key = array_search($course, $remaining_major_courses); //remove course from array of completed courses and from list of major courses
				unset($remaining_major_courses[$key]);
				$remaining_major_courses = array_values($remaining_major_courses);
				$courses_added++;
			}
			else if(($course != "COMP-4960A" && $course != "COMP-4960B" && $course != "COMP-4990A" && $course != "COMP-4990B") && term_available($mysqli, $term, $course) && get_prereqs($mysqli, $term, $course) == -1){ //course has no requirements, can be taken right away
				$courses_this_term[] = $course;
				$key = array_search($course, $remaining_major_courses); //remove course from array of completed courses and from list of major courses
				unset($remaining_major_courses[$key]);
				$remaining_major_courses = array_values($remaining_major_courses);
				$courses_added++;
			}
			else if(($course != "COMP-4960A" && $course != "COMP-4960B" && $course != "COMP-4990A" && $course != "COMP-4990B") && term_available($mysqli, $term, $course) && get_prereqs($mysqli, $term, $course) == array_intersect(get_prereqs($mysqli, $term, $course), $completedCoursesClean)){ //does the student have all requirements?
				$courses_this_term[] = $course;
				$key = array_search($course, $remaining_major_courses); //remove course from array of completed courses and from list of major courses
				unset($remaining_major_courses[$key]);
				$remaining_major_courses = array_values($remaining_major_courses);
				$courses_added++;
			}
		}
		return $courses_added;
	}
	
	public function buildCourseTable($mysqli, $year, $current_num_courses_added, &$courses_this_term, &$remaining_cs_2000, &$remaining_cs_3000, &$remaining_arts_courses, &$remaining_soc_courses, &$remaining_artssoc_courses, &$remaining_electives){
		for($j = 0; $j < (5-$current_num_courses_added); $j++){
			if($remaining_cs_2000 > 0 && $year != "First Year"){
				$courses_this_term[] = "CS course 2000+";
				$remaining_cs_2000--;
			}
			else if($remaining_cs_3000 > 0 && $year != "First Year" && $year != "Second Year"){
				$courses_this_term[] = "CS course 3000+";
				$remaining_cs_3000--;
			}
			else if($remaining_arts_courses > 0){
				$courses_this_term[] = "Arts course";
				$remaining_arts_courses--;
			}
			else if($remaining_soc_courses > 0){
				$courses_this_term[] = "Soc course";
				$remaining_soc_courses--;
			}
			else if($remaining_artssoc_courses > 0){
				$courses_this_term[] = "Arts/Soc course";
				$remaining_artssoc_courses--;
			}
			else if($remaining_electives > 0){
				$courses_this_term[] = "Elective course";
				$remaining_electives--;
			}
			else{
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
		foreach($courses_this_term as $course){ //Read list of courses for this term, query db for related information (course name) and add to table
			if($course == "Arts course"){
				$counter++;
				echo "<tr>";
				echo "<th scope=\"row\">". $counter . "</th>";
				echo "<td scope=\"row\">". "Arts/languages course" ."</td>";
				echo "</tr>";
			}
			else if($course == "Soc course"){
				$counter++;
				echo "<tr>";
				echo "<th scope=\"row\">". $counter . "</th>";
				echo "<td scope=\"row\">". "Social science course" ."</td>";
				echo "</tr>";
			}
			else if($course == "Arts/Soc course"){
				$counter++;
				echo "<tr>";
				echo "<th scope=\"row\">". $counter . "</th>";
				echo "<td scope=\"row\">". "Arts/languages or Social science course" ."</td>";
				echo "</tr>";
			}
			else if($course == "Elective course"){
				$counter++;
				echo "<tr>";
				echo "<th scope=\"row\">". $counter . "</th>";
				echo "<td scope=\"row\">". "Elective course" ."</td>";
				echo "</tr>";
			}
			else if($course == "No course"){
				$counter++;
				echo "<tr>";
				echo "<th scope=\"row\">". $counter . "</th>";
				echo "<td scope=\"row\">". "No course required" ."</td>";
				echo "</tr>";
			}
			else if($course == "CS course 2000+"){
				$counter++;
				echo "<tr>";
				echo "<th scope=\"row\">". $counter . "</th>";
				echo "<td scope=\"row\">". "CS Course level 2XXX-4XXX" ."</td>";
				echo "</tr>";
			}
			else if($course == "CS course 3000+"){
				$counter++;
				echo "<tr>";
				echo "<th scope=\"row\">". $counter . "</th>";
				echo "<td scope=\"row\">". "CS Course level 3XXX-4XXX" ."</td>";
				echo "</tr>";
			}
			else{
				if($getcourses = $mysqli -> query("SELECT name FROM courses WHERE course_code = \"". $course ."\"")){
					$row = $getcourses -> fetch_row();
					$counter++;
					echo "<tr>";
					echo "<th style = \"text-align: left\" scope=\"row\">". $counter . "</th>";
					echo "<td scope=\"row\">". $course. " - ". $row[0] ."</td>";
					//echo "<td scope=\"row\">". $course. " - "."</td>";
					echo "</tr>";
				}
				else{
					echo "query failed: " . $mysqli -> error;
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

class Major3{
	private $num_courses = 40;
	private $num_electives = 13;
	private $total_ArtsSoc_courses = 2;
	private $min_Arts_courses = 1;
	private $min_Soc_courses = 1;
	private $compsci_courses = 2;
	
	public $major_courses = array();
	
	public function get_num_courses(){
		return $this -> num_courses;
	}
	
	public function get_min_Arts_courses(){
		return $this -> min_Arts_courses;
	}
	public function get_min_Soc_courses(){
		return $this -> min_Soc_courses;
	}
	
	public function get_major_courses($mysqli, $major_id){
		if($courses = $mysqli -> query("SELECT course_code FROM courses WHERE id IN (SELECT course_id FROM major_requirements WHERE major_id = " . $major_id . ")")){
			$rows = $courses -> fetch_all(MYSQLI_NUM);
			$this -> major_courses = flatten_array($mysqli, $rows);
		}
	}

	public function substitute(string $value){
		if($value == "MATH-1250"){
			return "MATH-1260";
		}
		else if($value == "MATH-1720"){
			return "MATH-1760";
		}
		else{
			return false;
		}
	}

	public function requirement_major(array &$user_courses, array &$major_courses){
		foreach ($major_courses as $course){
			if(in_array($course, $user_courses)){ //check if user has completed a course within the list of major courses, or if they have completed its substitute
												//We must call $this->substitute to let the compiler know we are referring to the "substitute" function located in this class
				$user_key = array_search($course, $user_courses); //remove course from array of completed courses and from list of major courses
				unset($user_courses[$user_key]);
				$user_courses=array_values($user_courses);
				$major_key = array_search($course, $major_courses);
				unset($major_courses[$major_key]);
				$major_courses = array_values($major_courses);
			}
			else if(in_array($this->substitute($course), $user_courses)){
				$user_key = array_search($this->substitute($course), $user_courses); 
				unset($user_courses[$user_key]);
				$user_courses=array_values($user_courses);
				$major_key = array_search($course, $major_courses);
				unset($major_courses[$major_key]);
				$major_courses = array_values($major_courses);
			}
		}
		return $major_courses; //we return the remaining major courses
	}
	
	public function requirement_cs(array &$user_courses){ //check for "additional computer science" requirements. For BCS (General) they are not limited by course level.
		$viable=0;
		foreach ($user_courses as $course){
			$lettercode = explode("-", $course);
			if($lettercode[0] == "COMP" && !in_array($course, $major_courses)){ //check if a non-major course is a COMP course
				$user_key = array_search($course, $user_courses);
				unset($user_courses[$user_key]);
				$user_courses = array_values($user_courses);
				$viable++;
			}
		}
		return ($this -> compsci_courses - $viable); //return number of additional CS courses we need
	}
	
	public function requirement_arts(int $num_Arts_courses){ //user still needs an arts course
		return $this->min_Arts_courses - $num_Arts_courses;
	}
	public function requirement_soc(int $num_Soc_courses){ //user still needs a soc course
		return $this -> min_Soc_courses - $num_Soc_courses;
	}
	public function requirement_ArtsOrSoc(int $num_Arts_courses, int $num_Soc_courses){ //user still needs X arts or soc courses
		return ($this -> total_ArtsSoc_courses - ($num_Arts_courses + $num_Soc_courses));
	}
	
	public function requirement_electives(array &$user_courses, $electives_completed){ //Take extra courses that were completed but don't account for any other requirement as extra electives
		$count = 0;
		foreach($user_courses as $course){
			$user_key = array_search($course, $user_courses);
			unset($user_courses[$user_key]);
			$user_courses = array_values($user_courses);
			$count++;
		}
		return $this -> num_electives - $electives_completed - $count;
	}
	
	public function addMajorCourses($mysqli, $term, $year, &$remaining_major_courses, &$courses_this_term, $completedCoursesClean){
		$courses_added = 0;
		foreach($remaining_major_courses as $course){ //Try to add as many major courses as possible
			if($courses_added == 5){ //stop when we have 5 courses
				break;
			}
			else if(($course == "COMP-4990A" || ($course == "COMP-4990B" && get_prereqs($mysqli, $term, $course) == array_intersect(get_prereqs($mysqli, $term, $course), $completedCoursesClean))) && term_available($mysqli, $term, $course) && $year == "Fourth Year"){
				$courses_this_term[] = $course;
				$key = array_search($course, $remaining_major_courses); //remove course from array of completed courses and from list of major courses
				unset($remaining_major_courses[$key]);
				$remaining_major_courses = array_values($remaining_major_courses);
				$courses_added++;
			}
			else if(($course != "COMP-4990A" && $course != "COMP-4990B") && term_available($mysqli, $term, $course) && get_prereqs($mysqli, $term, $course) == -1){ //course has no requirements, can be taken right away
				$courses_this_term[] = $course;
				$key = array_search($course, $remaining_major_courses); //remove course from array of completed courses and from list of major courses
				unset($remaining_major_courses[$key]);
				$remaining_major_courses = array_values($remaining_major_courses);
				$courses_added++;
			}
			else if(($course != "COMP-4990A" && $course != "COMP-4990B") && term_available($mysqli, $term, $course) && get_prereqs($mysqli, $term, $course) == array_intersect(get_prereqs($mysqli, $term, $course), $completedCoursesClean)){ //does the student have all requirements?
				$courses_this_term[] = $course;
				$key = array_search($course, $remaining_major_courses); //remove course from array of completed courses and from list of major courses
				unset($remaining_major_courses[$key]);
				$remaining_major_courses = array_values($remaining_major_courses);
				$courses_added++;
			}
		}
		return $courses_added;
	}
	
	public function buildCourseTable($mysqli, $year, $current_num_courses_added, &$courses_this_term, &$remaining_cs_courses, &$remaining_arts_courses, &$remaining_soc_courses, &$remaining_artssoc_courses, &$remaining_electives){
		for($j = 0; $j < (5-$current_num_courses_added); $j++){
			if($remaining_cs_courses > 0){
				$courses_this_term[] = "CS course";
				$remaining_cs_courses--;
			}
			else if($remaining_arts_courses > 0){
				$courses_this_term[] = "Arts course";
				$remaining_arts_courses--;
			}
			else if($remaining_soc_courses > 0){
				$courses_this_term[] = "Soc course";
				$remaining_soc_courses--;
			}
			else if($remaining_artssoc_courses > 0){
				$courses_this_term[] = "Arts/Soc course";
				$remaining_artssoc_courses--;
			}
			else if($remaining_electives > 0){
				$courses_this_term[] = "Elective course";
				$remaining_electives--;
			}
			else{
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
		foreach($courses_this_term as $course){ //Read list of courses for this term, query db for related information (course name) and add to table
			if($course == "CS course"){
				$counter++;
				echo "<tr>";
				echo "<th style = \"text-align: left\" scope=\"row\">". $counter . "</th>";
				echo "<td>". "Computer science course" ."</td>";
				echo "</tr>";
			}
			else if($course == "Arts course"){
				$counter++;
				echo "<tr>";
				echo "<th scope=\"row\">". $counter . "</th>";
				echo "<td scope=\"row\">". "Arts/languages course" ."</td>";
				echo "</tr>";
			}
			else if($course == "Soc course"){
				$counter++;
				echo "<tr>";
				echo "<th scope=\"row\">". $counter . "</th>";
				echo "<td scope=\"row\">". "Social science course" ."</td>";
				echo "</tr>";
			}
			else if($course == "Arts/Soc course"){
				$counter++;
				echo "<tr>";
				echo "<th scope=\"row\">". $counter . "</th>";
				echo "<td scope=\"row\">". "Arts/languages or Social science course" ."</td>";
				echo "</tr>";
			}
			else if($course == "Elective course"){
				$counter++;
				echo "<tr>";
				echo "<th scope=\"row\">". $counter . "</th>";
				echo "<td scope=\"row\">". "Elective course" ."</td>";
				echo "</tr>";
			}
			else if($course == "No course"){
				$counter++;
				echo "<tr>";
				echo "<th scope=\"row\">". $counter . "</th>";
				echo "<td scope=\"row\">". "No course required" ."</td>";
				echo "</tr>";
			}
			else{
				if($getcourses = $mysqli -> query("SELECT name FROM courses WHERE course_code = \"". $course ."\"")){
					$row = $getcourses -> fetch_row();
					$counter++;
					echo "<tr>";
					echo "<th style = \"text-align: left\" scope=\"row\">". $counter . "</th>";
					echo "<td scope=\"row\">". $course. " - ". $row[0] ."</td>";
					echo "</tr>";
				}
				else{
					echo "query failed: " . $mysqli -> error;
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

class Major4{
	private $num_courses = 40;
	private $num_electives = 6;
	private $total_ArtsSoc_courses = 3;
	private $min_Arts_courses = 1;
	private $min_Soc_courses = 1;
	private $compsci_courses_3000 = 2;
	private $business_courses = 4;
	
	public $major_courses = array();
	
	public function get_num_courses(){
		return $this -> num_courses;
	}
	
	public function get_min_Arts_courses(){
		return $this -> min_Arts_courses;
	}
	public function get_min_Soc_courses(){
		return $this -> min_Soc_courses;
	}

	public function get_major_courses($mysqli, $major_id){
		if($courses = $mysqli -> query("SELECT course_code FROM courses WHERE id IN (SELECT course_id FROM major_requirements WHERE major_id = " . $major_id . ")")){
			$rows = $courses -> fetch_all(MYSQLI_NUM);
			$this -> major_courses = flatten_array($mysqli, $rows);
		}
		array_push($this -> major_courses, "ACCT-1510", "ACCT-2550", "FINA-2700", "MKTG-1310", "STEN-1000", "ECON-1100", "ECON-1110");
	}

	public function substitute(string $value){
		if($value == "MATH-1250"){
			return "MATH-1260";
		}
		else if($value == "MATH-1720"){
			return "MATH-1760";
		}
		else{
			return false;
		}
	}

	public function requirement_major(array &$user_courses, array &$major_courses){
		foreach ($major_courses as $course){
			if(in_array($course, $user_courses)){ //check if user has completed a course within the list of major courses, or if they have completed its substitute
												//We must call $this->substitute to let the compiler know we are referring to the "substitute" function located in this class
				$user_key = array_search($course, $user_courses); //remove course from array of completed courses and from list of major courses
				unset($user_courses[$user_key]);
				$user_courses=array_values($user_courses);
				$major_key = array_search($course, $major_courses);
				unset($major_courses[$major_key]);
				$major_courses = array_values($major_courses);
			}
			else if(in_array($this->substitute($course), $user_courses)){
				$user_key = array_search($this->substitute($course), $user_courses); 
				unset($user_courses[$user_key]);
				$user_courses=array_values($user_courses);
				$major_key = array_search($course, $major_courses);
				unset($major_courses[$major_key]);
				$major_courses = array_values($major_courses);
			}
		}
		return $major_courses; //we return the remaining major courses
	}
	
	public function requirement_cs_3000(array &$user_courses){
		$viable=0;
		foreach ($user_courses as $course){
			$lettercode = explode("-", $course);
			if($lettercode[0] == "COMP" && (int)$lettercode[1] >= 3000 && !in_array($course, $major_courses)){ //check if a non-major course is a COMP course
				$user_key = array_search($course, $user_courses);
				unset($user_courses[$user_key]);
				$user_courses = array_values($user_courses);
				$viable++;
			}
		}
		return ($this -> compsci_courses_3000 - $viable); //return number of additional CS courses we need
	}
	
	public function requirement_business(array &$user_courses){
		$viable=0;
		foreach ($user_courses as $course){
			$lettercode = explode("-", $course);
			$exceptions = array("MSCI-2020", "MSCI-2130", "MSCI-2200", "MSCI-3200");
			if(($lettercode[0] == "ACCT" || $lettercode[0] == "MGMT" || $lettercode[0] == "MKTG" || $lettercode[0] == "STEN" || $lettercode[0] == "MSCI") && !in_array($course, $major_courses) && !in_array($course, $exceptions)){ //check if a non-major course is a business course
				$user_key = array_search($course, $user_courses);
				unset($user_courses[$user_key]);
				$user_courses = array_values($user_courses);
				$viable++;
			}
		}
		return ($this -> business_courses - $viable); //return number of additional business courses we need
	}
	
	public function requirement_arts(int $num_Arts_courses){ //user still needs an arts course
		return $this->min_Arts_courses - $num_Arts_courses;
	}
	public function requirement_soc(int $num_Soc_courses){ //user still needs a soc course
		return $this -> min_Soc_courses - $num_Soc_courses;
	}
	public function requirement_ArtsOrSoc(int $num_Arts_courses, int $num_Soc_courses){ //user still needs X arts or soc courses
		return ($this -> total_ArtsSoc_courses - ($num_Arts_courses + $num_Soc_courses));
	}
	
	public function requirement_electives(array &$user_courses, $electives_completed){ //Take extra courses that were completed but don't account for any other requirement as extra electives
		$count = 0;
		foreach($user_courses as $course){
			$user_key = array_search($course, $user_courses);
			unset($user_courses[$user_key]);
			$user_courses = array_values($user_courses);
			$count++;
		}
		return $this -> num_electives - $electives_completed - $count;
	}
	
	public function addMajorCourses($mysqli, $term, $year, &$remaining_major_courses, &$courses_this_term, $completedCoursesClean){
		$courses_added = 0;
		$first_year_business = array("ECON-1100", "ECON-1110", "STEN-1000", "MKTG-1310", "ACCT-1510");
		foreach($remaining_major_courses as $course){ //Try to add as many major courses as possible
			if($courses_added == 5){ //stop when we have 5 courses
				break;
			}
			else if(($course == "COMP-4990A" || ($course == "COMP-4990B" && get_prereqs($mysqli, $term, $course) == array_intersect(get_prereqs($mysqli, $term, $course), $completedCoursesClean))) && term_available($mysqli, $term, $course) && $year == "Fourth Year"){
				$courses_this_term[] = $course;
				$key = array_search($course, $remaining_major_courses); //remove course from array of completed courses and from list of major courses
				unset($remaining_major_courses[$key]);
				$remaining_major_courses = array_values($remaining_major_courses);
				$courses_added++;
			}
			else if(($course != "COMP-4990A" && $course != "COMP-4990B") && term_available($mysqli, $term, $course) && get_prereqs($mysqli, $term, $course) == -1){ //course has no requirements, can be taken right away
				$courses_this_term[] = $course;
				$key = array_search($course, $remaining_major_courses);
				unset($remaining_major_courses[$key]);
				$remaining_major_courses = array_values($remaining_major_courses);
				$courses_added++;
			}
			else if(($course != "COMP-4990A" && $course != "COMP-4990B") && term_available($mysqli, $term, $course) && get_prereqs($mysqli, $term, $course) == array_intersect(get_prereqs($mysqli, $term, $course), $completedCoursesClean)){ //does the student have all requirements?
				$courses_this_term[] = $course;
				$key = array_search($course, $remaining_major_courses);
				unset($remaining_major_courses[$key]);
				$remaining_major_courses = array_values($remaining_major_courses);
				$courses_added++;
			}
			
			//I got a bit lazy with this, I didn't want to add the business courses to the database because I would have to add a lot of entries,
			//What we're doing here is trying to add any business courses we can after we add the comp sci/math major courses.
			else if($course == "ECON-1100" || $course == "ECON-1110" || $course == "STEN-1000" || $course == "MKTG-1310" || $course == "ACCT-1510"){ //ECON-1110 is the part 2 of ECON-1100 but doesn't strictly require that you have ECON-1100 as a prerequisite
				$courses_this_term[] = $course;
				$key = array_search($course, $remaining_major_courses);
				unset($remaining_major_courses[$key]);
				$remaining_major_courses = array_values($remaining_major_courses);
				$courses_added++;
			}
			else if(($course == "ACCT-2550" || $course == "FINA-2700") && in_array($first_year_business, $completedCoursesClean)){
				$courses_this_term[] = $course;
				$key = array_search($course, $remaining_major_courses);
				unset($remaining_major_courses[$key]);
				$remaining_major_courses = array_values($remaining_major_courses);
				$courses_added++;
			}
		}
		return $courses_added;
	}
	
	public function buildCourseTable($mysqli, $year, $current_num_courses_added, &$courses_this_term, &$remaining_business_courses, &$remaining_cs_3000, &$remaining_arts_courses, &$remaining_soc_courses, &$remaining_artssoc_courses, &$remaining_electives){
		for($j = 0; $j < (5-$current_num_courses_added); $j++){
			if($remaining_cs_3000 > 0 && $year != "First Year" && $year != "Second Year"){
				$courses_this_term[] = "CS course 3000+";
				$remaining_cs_3000--;
			}
			else if($remaining_business_courses > 0){
				$courses_this_term[] = "Business course";
				$remaining_business_courses--;
			}
			else if($remaining_arts_courses > 0){
				$courses_this_term[] = "Arts course";
				$remaining_arts_courses--;
			}
			else if($remaining_soc_courses > 0){
				$courses_this_term[] = "Soc course";
				$remaining_soc_courses--;
			}
			else if($remaining_artssoc_courses > 0){
				$courses_this_term[] = "Arts/Soc course";
				$remaining_artssoc_courses--;
			}
			else if($remaining_electives > 0){
				$courses_this_term[] = "Elective course";
				$remaining_electives--;
			}
			else{
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
		foreach($courses_this_term as $course){ //Read list of courses for this term, query db for related information (course name) and add to table
			if($course == "CS course 3000+"){
				$counter++;
				echo "<tr>";
				echo "<th scope=\"row\">". $counter . "</th>";
				echo "<td scope=\"row\">". "CS Course level 3XXX-4XXX" ."</td>";
				echo "</tr>";
			}
			else if($course == "Business course"){
				$counter++;
				echo "<tr>";
				echo "<th scope=\"row\">". $counter . "</th>";
				echo "<td scope=\"row\">". "Business course" ."</td>";
				echo "</tr>";
			}
			else if($course == "Arts course"){
				$counter++;
				echo "<tr>";
				echo "<th scope=\"row\">". $counter . "</th>";
				echo "<td scope=\"row\">". "Arts/languages course" ."</td>";
				echo "</tr>";
			}
			else if($course == "Soc course"){
				$counter++;
				echo "<tr>";
				echo "<th scope=\"row\">". $counter . "</th>";
				echo "<td scope=\"row\">". "Social science course" ."</td>";
				echo "</tr>";
			}
			else if($course == "Arts/Soc course"){
				$counter++;
				echo "<tr>";
				echo "<th scope=\"row\">". $counter . "</th>";
				echo "<td scope=\"row\">". "Arts/languages or Social science course" ."</td>";
				echo "</tr>";
			}
			else if($course == "Elective course"){
				$counter++;
				echo "<tr>";
				echo "<th scope=\"row\">". $counter . "</th>";
				echo "<td scope=\"row\">". "Elective course" ."</td>";
				echo "</tr>";
			}
			else if($course == "No course"){
				$counter++;
				echo "<tr>";
				echo "<th scope=\"row\">". $counter . "</th>";
				echo "<td scope=\"row\">". "No course required" ."</td>";
				echo "</tr>";
			}
			else{
				if($getcourses = $mysqli -> query("SELECT name FROM courses WHERE course_code = \"". $course ."\"")){
					$row = $getcourses -> fetch_row();
					$counter++;
					echo "<tr>";
					echo "<th style = \"text-align: left\" scope=\"row\">". $counter . "</th>";
					echo "<td scope=\"row\">". $course. " - ". $row[0] ."</td>";
					echo "</tr>";
				}
				else{
					echo "query failed: " . $mysqli -> error;
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

class Major5{
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
	
	public function get_num_courses(){
		return $this -> num_courses;
	}
	
	public function get_min_Arts_courses(){
		return $this -> min_Arts_courses;
	}
	public function get_min_Soc_courses(){
		return $this -> min_Soc_courses;
	}

	public function get_major_courses($mysqli, $major_id){
		if($courses = $mysqli -> query("SELECT course_code FROM courses WHERE id IN (SELECT course_id FROM major_requirements WHERE major_id = " . $major_id . ")")){
			$rows = $courses -> fetch_all(MYSQLI_NUM);
			$this -> major_courses = flatten_array($mysqli, $rows);
		}
	}

	public function substitute(string $value){
		if($value == "MATH-1250"){
			return "MATH-1260";
		}
		else if($value == "MATH-1720"){
			return "MATH-1760";
		}
		else if($value == "STAT-2920"){
			return "STAT-2910";
		}
		else if($value == "COMP-4960A"){
			return "COMP-4990A";
		}
		else if($value == "COMP-4960B"){
			return "COMP-4990B";
		}
		else{
			return false;
		}
	}

	public function requirement_major(array &$user_courses, array &$major_courses){
		foreach ($major_courses as $course){
			if(in_array($course, $user_courses)){ //check if user has completed a course within the list of major courses, or if they have completed its substitute
												//We must call $this->substitute to let the compiler know we are referring to the "substitute" function located in this class
				$user_key = array_search($course, $user_courses); //remove course from array of completed courses and from list of major courses
				unset($user_courses[$user_key]);
				$user_courses=array_values($user_courses);
				$major_key = array_search($course, $major_courses);
				unset($major_courses[$major_key]);
				$major_courses = array_values($major_courses);
			}
			else if(in_array($this->substitute($course), $user_courses)){
				$user_key = array_search($this->substitute($course), $user_courses); 
				unset($user_courses[$user_key]);
				$user_courses=array_values($user_courses);
				$major_key = array_search($course, $major_courses);
				unset($major_courses[$major_key]);
				$major_courses = array_values($major_courses);
			}
		}
		return $major_courses; //we return the remaining major courses
	}
	
	public function requirement_cs_2000(array &$user_courses){
		$viable=0;
		foreach ($user_courses as $course){
			$lettercode = explode("-", $course);
			if($lettercode[0] == "COMP" && (int)$lettercode[1] >= 2000 && !in_array($course, $major_courses)){ //check if a non-major course is a COMP course
				$user_key = array_search($course, $user_courses);
				unset($user_courses[$user_key]);
				$user_courses = array_values($user_courses);
				$viable++;
			}
		}
		return ($this -> compsci_courses_2000 - $viable); //return number of additional CS courses we need
	}
	
	public function requirement_dynamics(array &$user_courses){
		$viable=0;
		foreach ($user_courses as $course){
			$lettercode = explode("-", $course);
			if($lettercode[0] == "PSYC" && ($lettercode[1] == "1150" || $lettercode[1] == "2180") || $lettercode[0] == "PHIL" && ($lettercode[1] == "1290" || $lettercode[1] == "2280")){ //check if a non-major course is a business course
				$user_key = array_search($course, $user_courses);
				unset($user_courses[$user_key]);
				$user_courses = array_values($user_courses);
				$viable++;
			}
		}
		return ($this -> dynamics_courses - $viable); //return number of additional dynamics courses we need
	}
	public function requirement_communication(array &$user_courses){
		$viable=0;
		foreach ($user_courses as $course){
			$lettercode = explode("-", $course);
			if($lettercode[0] == "CMAF" && ($lettercode[1] == "2210" || $lettercode[1] == "2100") || $lettercode[0] == "DRAM" && $lettercode[1] == "2100" || $lettercode[0] == "ENGL" && $lettercode[1] == "1001"){ //check if a non-major course is a business course
				$user_key = array_search($course, $user_courses);
				unset($user_courses[$user_key]);
				$user_courses = array_values($user_courses);
				$viable++;
			}
		}
		return ($this -> communication_courses - $viable); //return number of additional communication courses we need
	}
	public function requirement_professionalism(array &$user_courses){
		$viable=0;
		foreach ($user_courses as $course){
			$lettercode = explode("-", $course);
			if($lettercode[0] == "PHIL" && ($lettercode[1] == "2210" || $lettercode[1] == "2240") || $lettercode[0] == "GART" && $lettercode[1] == "2090" || $lettercode[0] == "ENGL" && $lettercode[1] == "1005"){ //check if a non-major course is a business course
				$user_key = array_search($course, $user_courses);
				unset($user_courses[$user_key]);
				$user_courses = array_values($user_courses);
				$viable++;
			}
		}
		return ($this -> professionalism_courses - $viable); //return number of additional professionalism courses we need
	}
	public function requirement_business(array &$user_courses){
		$viable=0;
		foreach ($user_courses as $course){
			$lettercode = explode("-", $course);
			if($lettercode[0] == "MKTG" && $lettercode[1] == "1310" || $lettercode[0] == "MGMT" && $lettercode[1] == "2400" || $lettercode[0] == "STEN" && $lettercode[1] == "1000" ){ //check if a non-major course is a business course
				$user_key = array_search($course, $user_courses);
				unset($user_courses[$user_key]);
				$user_courses = array_values($user_courses);
				$viable++;
			}
		}
		return ($this -> business_courses - $viable); //return number of additional business courses we need
	}
	
	public function requirement_arts(int $num_Arts_courses){ //user still needs an arts course
		return $this->min_Arts_courses - $num_Arts_courses;
	}
	public function requirement_soc(int $num_Soc_courses){ //user still needs a soc course
		return $this -> min_Soc_courses - $num_Soc_courses;
	}
	public function requirement_ArtsOrSoc(int $num_Arts_courses, int $num_Soc_courses){ //user still needs X arts or soc courses
		return ($this -> total_ArtsSoc_courses - ($num_Arts_courses + $num_Soc_courses));
	}
	
	public function requirement_electives(array &$user_courses, $electives_completed){ //Take extra courses that were completed but don't account for any other requirement as extra electives
		$count = 0;
		foreach($user_courses as $course){
			$user_key = array_search($course, $user_courses);
			unset($user_courses[$user_key]);
			$user_courses = array_values($user_courses);
			$count++;
		}
		return $this -> num_electives - $electives_completed - $count;
	}
	
	public function addMajorCourses($mysqli, $term, $year, &$remaining_major_courses, &$courses_this_term, $completedCoursesClean){
		$courses_added = 0;
		foreach($remaining_major_courses as $course){ //Try to add as many major courses as possible
			if($courses_added == 5){ //stop when we have 5 courses
				break;
			}
			else if(($course == "COMP-4960A" || ($course == "COMP-4960B" && get_prereqs($mysqli, $term, $course) == array_intersect(get_prereqs($mysqli, $term, $course), $completedCoursesClean)) || $course == "COMP-4990A" || ($course == "COMP-4990B" && get_prereqs($mysqli, $term, $course) == array_intersect(get_prereqs($mysqli, $term, $course), $completedCoursesClean))) && term_available($mysqli, $term, $course) && $year == "Fourth Year"){
				$courses_this_term[] = $course;
				$key = array_search($course, $remaining_major_courses); //remove course from array of completed courses and from list of major courses
				unset($remaining_major_courses[$key]);
				$remaining_major_courses = array_values($remaining_major_courses);
				$courses_added++;
			}
			else if(($course != "COMP-4960A" && $course != "COMP-4960B" && $course != "COMP-4990A" && $course != "COMP-4990B") && term_available($mysqli, $term, $course) && get_prereqs($mysqli, $term, $course) == -1){ //course has no requirements, can be taken right away
				$courses_this_term[] = $course;
				$key = array_search($course, $remaining_major_courses); //remove course from array of completed courses and from list of major courses
				unset($remaining_major_courses[$key]);
				$remaining_major_courses = array_values($remaining_major_courses);
				$courses_added++;
			}
			else if(($course != "COMP-4960A" && $course != "COMP-4960B" && $course != "COMP-4990A" && $course != "COMP-4990B") && term_available($mysqli, $term, $course) && get_prereqs($mysqli, $term, $course) == array_intersect(get_prereqs($mysqli, $term, $course), $completedCoursesClean)){ //does the student have all requirements?
				$courses_this_term[] = $course;
				$key = array_search($course, $remaining_major_courses); //remove course from array of completed courses and from list of major courses
				unset($remaining_major_courses[$key]);
				$remaining_major_courses = array_values($remaining_major_courses);
				$courses_added++;
			}
		}
		return $courses_added;
	}
	
	public function buildCourseTable($mysqli, $year, $current_num_courses_added, &$courses_this_term, &$remaining_cs_2000, &$remaining_dynamics_courses, &$remaining_communication_courses, &$remaining_professionalism_courses, &$remaining_business_courses, &$remaining_arts_courses, &$remaining_soc_courses, &$remaining_artssoc_courses, &$remaining_electives){
		for($j = 0; $j < (5-$current_num_courses_added); $j++){
			if($remaining_cs_2000 > 0 && $year != "First Year"){
				$courses_this_term[] = "CS course 2000+";
				$remaining_cs_2000--;
			}
			else if($remaining_dynamics_courses > 0){
				$courses_this_term[] = "Dynamics course";
				$remaining_dynamics_courses--;
			}
			else if($remaining_communication_courses > 0){
				$courses_this_term[] = "Communication course";
				$remaining_communication_courses--;
			}
			else if($remaining_professionalism_courses > 0){
				$courses_this_term[] = "Professionalism course";
				$remaining_professionalism_courses--;
			}
			else if($remaining_business_courses > 0){
				$courses_this_term[] = "Business course";
				$remaining_business_courses--;
			}
			else if($remaining_arts_courses > 0){
				$courses_this_term[] = "Arts course";
				$remaining_arts_courses--;
			}
			else if($remaining_soc_courses > 0){
				$courses_this_term[] = "Soc course";
				$remaining_soc_courses--;
			}
			else if($remaining_artssoc_courses > 0){
				$courses_this_term[] = "Arts/Soc course";
				$remaining_artssoc_courses--;
			}
			else if($remaining_electives > 0){
				$courses_this_term[] = "Elective course";
				$remaining_electives--;
			}
			else{
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
		foreach($courses_this_term as $course){ //Read list of courses for this term, query db for related information (course name) and add to table
			if($course == "CS course 2000+"){
				$counter++;
				echo "<tr>";
				echo "<th scope=\"row\">". $counter . "</th>";
				echo "<td scope=\"row\">". "CS Course level 2XXX-4XXX" ."</td>";
				echo "</tr>";
			}
			else if($course == "Dynamics course"){
				$counter++;
				echo "<tr>";
				echo "<th scope=\"row\">". $counter . "</th>";
				echo "<td scope=\"row\">". "Dynamics course" ."</td>";
				echo "</tr>";
			}
			else if($course == "Communication course"){
				$counter++;
				echo "<tr>";
				echo "<th scope=\"row\">". $counter . "</th>";
				echo "<td scope=\"row\">". "Communication course" ."</td>";
				echo "</tr>";
			}
			else if($course == "Professionalism course"){
				$counter++;
				echo "<tr>";
				echo "<th scope=\"row\">". $counter . "</th>";
				echo "<td scope=\"row\">". "Professionalism course" ."</td>";
				echo "</tr>";
			}
			else if($course == "Business course"){
				$counter++;
				echo "<tr>";
				echo "<th scope=\"row\">". $counter . "</th>";
				echo "<td scope=\"row\">". "Business course" ."</td>";
				echo "</tr>";
			}
			else if($course == "Arts course"){
				$counter++;
				echo "<tr>";
				echo "<th scope=\"row\">". $counter . "</th>";
				echo "<td scope=\"row\">". "Arts/languages course" ."</td>";
				echo "</tr>";
			}
			else if($course == "Soc course"){
				$counter++;
				echo "<tr>";
				echo "<th scope=\"row\">". $counter . "</th>";
				echo "<td scope=\"row\">". "Social science course" ."</td>";
				echo "</tr>";
			}
			else if($course == "Arts/Soc course"){
				$counter++;
				echo "<tr>";
				echo "<th scope=\"row\">". $counter . "</th>";
				echo "<td scope=\"row\">". "Arts/languages or Social science course" ."</td>";
				echo "</tr>";
			}
			else if($course == "Elective course"){
				$counter++;
				echo "<tr>";
				echo "<th scope=\"row\">". $counter . "</th>";
				echo "<td scope=\"row\">". "Elective course" ."</td>";
				echo "</tr>";
			}
			else if($course == "No course"){
				$counter++;
				echo "<tr>";
				echo "<th scope=\"row\">". $counter . "</th>";
				echo "<td scope=\"row\">". "No course required" ."</td>";
				echo "</tr>";
			}
			else{
				if($getcourses = $mysqli -> query("SELECT name FROM courses WHERE course_code = \"". $course ."\"")){
					$row = $getcourses -> fetch_row();
					$counter++;
					echo "<tr>";
					echo "<th style = \"text-align: left\" scope=\"row\">". $counter . "</th>";
					echo "<td scope=\"row\">". $course. " - ". $row[0] ."</td>";
					echo "</tr>";
				}
				else{
					echo "query failed: " . $mysqli -> error;
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

<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- Bootstrap CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">

      
        <title>Course Scheduler</title>
        <meta name="description" content="Computer Science Course Scheduler.">
        <meta name="author" content="Katerina Pace">

        <link rel="stylesheet" href="../css/test.css">
        <script src="../js/test.js"></script>
    </head>
    <body style="font-size:larger;">
        <div class="container-fluid"> 
          <div class="row"> 
			<h3>Recommended Schedule <?php echo $program ?></h3>

			
			<?php 
				//Create list of courses that were checkmarked as completed in the last page
				for($i = 0; $i < ($_SESSION['incomplete_count']); $i++){
					$temp = "completed_" . $major_courses[$i]; //check if completed checkbox was checked for each course

					if(isset($_POST[$temp])){ //form data was submitted through POST method so we can get it through POST on this webpage
						$completedCourses[] = $major_courses[$i]; //add the course to the list of completed courses
					}
				}
				$completedCoursesClean = $completedCourses;
				$num_completed_courses = count($completedCoursesClean);
				$num_completed_courses += ($num_arts + $num_soc + $num_elec);
				
				if($program == "Bachelor of Computer Science (General)"){
					$class = new Major1();
					$class -> get_major_courses($mysqli, $major_id);
					$remaining_major_courses = $class -> requirement_major($completedCourses, $class -> major_courses);
					$remaining_cs_courses = $class -> requirement_cs($completedCourses);
					$remaining_arts_courses = $class -> requirement_arts($num_arts);
					$remaining_soc_courses = $class -> requirement_soc($num_soc);
					$remaining_artssoc_courses = $class -> requirement_ArtsOrSoc($class -> get_min_Arts_courses(), $class -> get_min_Arts_courses());
					$remaining_electives = $class -> requirement_electives($completedCourses, $num_elec);
					
					for($i = 0; $i<ceil((($class -> get_num_courses()) - $num_completed_courses)/5); $i++){ //Create enough tables of 5 to cover all terms the user needs to graduate
						$courses_this_term = array();
						$courses_added = $class -> addMajorCourses($mysqli, $term, $remaining_major_courses, $courses_this_term, $completedCoursesClean);
						$current_num_courses_added = $courses_added; //creating this variable to store the number of courses we have added up to this point. Do this to avoid using "$j < (5-$courses_added)" because we want to increment $courses_added
						echo "<p>". $term . ": " . $year . "</p>";
						$class -> buildCourseTable($mysqli, $current_num_courses_added, $courses_this_term, $remaining_cs_courses, $remaining_arts_courses, $remaining_soc_courses, $remaining_artssoc_courses, $remaining_electives);
						increment_time($term, $year);
						foreach($courses_this_term as $course){ //add group of 5 courses to list of all completed courses
							$completedCoursesClean[] = $course;
						}
					}	
				}
				else if($program == "Bachelor of Computer Science (Honours)"){
					$class = new Major2();
					$class -> get_major_courses($mysqli, $major_id);
					$remaining_major_courses = $class -> requirement_major($completedCourses, $class -> major_courses);
					$remaining_cs_2000 = $class -> requirement_cs_2000($completedCourses);
					$remaining_cs_3000 = $class -> requirement_cs_3000($completedCourses);
					$remaining_arts_courses = $class -> requirement_arts($num_arts);
					$remaining_soc_courses = $class -> requirement_soc($num_soc);
					$remaining_artssoc_courses = $class -> requirement_ArtsOrSoc($class -> get_min_Arts_courses(), $class -> get_min_Arts_courses());
					$remaining_electives = $class -> requirement_electives($completedCourses, $num_elec);
					
					for($i = 0; $i<ceil((($class -> get_num_courses()) - $num_completed_courses)/5); $i++){ //Create enough tables of 5 to cover all terms the user needs to graduate
						$courses_this_term = array();
						$courses_added = $class -> addMajorCourses($mysqli, $term, $year, $remaining_major_courses, $courses_this_term, $completedCoursesClean);
						$current_num_courses_added = $courses_added; //creating this variable to store the number of courses we have added up to this point. Do this to avoid using "$j < (5-$courses_added)" because we want to increment $courses_added
						echo "<p>". $term . ": " . $year . "</p>";
						$class -> buildCourseTable($mysqli, $year, $current_num_courses_added, $courses_this_term, $remaining_cs_2000, $remaining_cs_3000, $remaining_arts_courses, $remaining_soc_courses, $remaining_artssoc_courses, $remaining_electives);
						increment_time($term, $year);
						foreach($courses_this_term as $course){ //add group of 5 courses to list of all completed courses
							$completedCoursesClean[] = $course;
						}
					}	
				}
				else if($program == "Bachelor of Computer Science (Honours Applied Computing)"){
					$class = new Major3();
					$class -> get_major_courses($mysqli, $major_id);
					$remaining_major_courses = $class -> requirement_major($completedCourses, $class -> major_courses);
					$remaining_cs_courses = $class -> requirement_cs($completedCourses);
					$remaining_arts_courses = $class -> requirement_arts($num_arts);
					$remaining_soc_courses = $class -> requirement_soc($num_soc);
					$remaining_artssoc_courses = $class -> requirement_ArtsOrSoc($class -> get_min_Arts_courses(), $class -> get_min_Arts_courses());
					$remaining_electives = $class -> requirement_electives($completedCourses, $num_elec);
					
					for($i = 0; $i<ceil((($class -> get_num_courses()) - $num_completed_courses)/5); $i++){ //Create enough tables of 5 to cover all terms the user needs to graduate
						$courses_this_term = array();
						$courses_added = $class -> addMajorCourses($mysqli, $term, $year, $remaining_major_courses, $courses_this_term, $completedCoursesClean);
						$current_num_courses_added = $courses_added; //creating this variable to store the number of courses we have added up to this point. Do this to avoid using "$j < (5-$courses_added)" because we want to increment $courses_added
						echo "<p>". $term . ": " . $year . "</p>";
						$class -> buildCourseTable($mysqli, $year, $current_num_courses_added, $courses_this_term, $remaining_cs_courses, $remaining_arts_courses, $remaining_soc_courses, $remaining_artssoc_courses, $remaining_electives);
						increment_time($term, $year);
						foreach($courses_this_term as $course){ //add group of 5 courses to list of all completed courses
							$completedCoursesClean[] = $course;
						}
					}	
				}
				else if($program == "Bachelor of Science (Honours Computer Information Systems)"){
					$class = new Major4();
					$class -> get_major_courses($mysqli, $major_id);
					$remaining_major_courses = $class -> requirement_major($completedCourses, $class -> major_courses);
					$remaining_business_courses = $class -> requirement_business($completedCourses);
					$remaining_cs_3000 = $class -> requirement_cs_3000($completedCourses);
					$remaining_arts_courses = $class -> requirement_arts($num_arts);
					$remaining_soc_courses = $class -> requirement_soc($num_soc);
					$remaining_artssoc_courses = $class -> requirement_ArtsOrSoc($class -> get_min_Arts_courses(), $class -> get_min_Arts_courses());
					$remaining_electives = $class -> requirement_electives($completedCourses, $num_elec);
					
					for($i = 0; $i<ceil((($class -> get_num_courses()) - $num_completed_courses)/5); $i++){ //Create enough tables of 5 to cover all terms the user needs to graduate
						$courses_this_term = array();
						$courses_added = $class -> addMajorCourses($mysqli, $term, $year, $remaining_major_courses, $courses_this_term, $completedCoursesClean);
						$current_num_courses_added = $courses_added; //creating this variable to store the number of courses we have added up to this point. Do this to avoid using "$j < (5-$courses_added)" because we want to increment $courses_added
						echo "<p>". $term . ": " . $year . "</p>";
						$class -> buildCourseTable($mysqli, $year, $current_num_courses_added, $courses_this_term, $remaining_business_courses, $remaining_cs_3000, $remaining_arts_courses, $remaining_soc_courses, $remaining_artssoc_courses, $remaining_electives);
						increment_time($term, $year);
						foreach($courses_this_term as $course){ //add group of 5 courses to list of all completed courses
							$completedCoursesClean[] = $course;
						}
					}	
				}
				else if($program == "Bachelor of Science (Honours Computer Science with Software Engineering Specialization)"){
					$class = new Major5();
					$class -> get_major_courses($mysqli, $major_id);
					$remaining_major_courses = $class -> requirement_major($completedCourses, $class -> major_courses);
					$remaining_cs_2000 = $class -> requirement_cs_2000($completedCourses);
					$remaining_arts_courses = $class -> requirement_arts($num_arts);
					$remaining_soc_courses = $class -> requirement_soc($num_soc);
					$remaining_artssoc_courses = $class -> requirement_ArtsOrSoc($class -> get_min_Arts_courses(), $class -> get_min_Arts_courses());
					$remaining_dynamics_courses = $class -> requirement_dynamics($completedCourses);
					$remaining_communication_courses = $class ->  requirement_communication($completedCourses);
					$remaining_professionalism_courses = $class -> requirement_professionalism($completedCourses);
					$remaining_business_courses = $class -> requirement_business($completedCourses);
					$remaining_electives = $class -> requirement_electives($completedCourses, $num_elec);
					
					for($i = 0; $i<ceil((($class -> get_num_courses()) - $num_completed_courses)/5); $i++){ //Create enough tables of 5 to cover all terms the user needs to graduate
						$courses_this_term = array();
						$courses_added = $class -> addMajorCourses($mysqli, $term, $year, $remaining_major_courses, $courses_this_term, $completedCoursesClean);
						$current_num_courses_added = $courses_added; //creating this variable to store the number of courses we have added up to this point. Do this to avoid using "$j < (5-$courses_added)" because we want to increment $courses_added
						echo "<p>". $term . ": " . $year . "</p>";
						$class -> buildCourseTable($mysqli, $year, $current_num_courses_added, $courses_this_term, $remaining_cs_2000, $remaining_dynamics_courses, $remaining_communication_courses, $remaining_professionalism_courses, $remaining_business_courses, $remaining_arts_courses, $remaining_soc_courses, $remaining_artssoc_courses, $remaining_electives);
						increment_time($term, $year);
						foreach($courses_this_term as $course){ //add group of 5 courses to list of all completed courses
							$completedCoursesClean[] = $course;
						}
					}	
				}
				
				/*
				The approach to generate the schedule of courses for the user is to find how many courses are still required,
				then to group them into groups of 5 (5 courses representing 1 term at a time). We will give priority to some
				courses and add them first, then fill in as we need to. Priority goes to the major courses (the courses 
				absolutely needed to graduate) then to more general requirements such as social science courses, and finally to electives.
				*/
				
				
				
			?>
			
          </div>
            <!--<button type="submit" class="btn btn-primary">Back</button>--><!--dont use?-->
            <a class="btn btn-primary" href="test3.php" role="button">Back</a> <!--how to submit form data with link?-->
            <!--<button type="submit" class="btn btn-primary">Continue</button>--><!--dont use?-->
            <a class="btn btn-primary" href="test4.php" role="button">Continue</a> <!--how to submit form data with link?-->

        </div>

        <!-- Popper and Bootstrap JS -->
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js" integrity="sha384-7+zCNj/IqJ95wo16oMtfsKbZ9ccEh31eOz1HGyDuCQ6wgnyJNSYdrPa03rtR1zdB" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js" integrity="sha384-QJHtvGhmr9XOIpI6YVutG+2QOK9T+ZnN4kzFN1RtK3zEFEIsxhlmWl5/YESvpZ13" crossorigin="anonymous"></script>
        
		<?php $mysqli -> close(); ?>
    </body>
</html>