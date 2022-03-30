<?php
session_start();
include "env.php";
//include "classes.php";

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

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
$remaining_major_courses = $_SESSION["major_courses"]; //copying the list of major courses so we can maintain the original list and edit this one
$courses_this_year = $_SESSION["courses_this_year"];
$incomplete_count = 1;
$complete_count = 0;
$major_id = $_SESSION["major_id"];
$completedCourses = array(); //This array represents the list of courses the user has completed, and we will be removing elements from this list as the courses are used to satisfy requirements
$completedCoursesClean = array(); //This array is the same as completedCourses but we will keep this array unmodified for situations where we need to know all the courses we have completed

function get_prereqs($mysqli, $coursecode){ //have to pass $mysqli as an argument because we are using a global variable within a local scope
	if($numReqs = $mysqli -> query("SELECT num_requirements FROM course_requirements WHERE course_id = " . $coursecode)){
		$row = $numReqs -> fetch_row();
		if($reqsList = $mysqli -> query("SELECT * FROM ".$row[0]."_requirements WHERE course_id = " . $coursecode)){
			$reqsRow = $reqsList -> fetch_row();
			array_shift($reqsRow); //pop first element in the array (this value is the course id of the course we are getting the requirements of)
			//print_r($reqsRow);
			
			return $reqsRow;
			
		}
		else{ //return -1 if course has no prerequisites
			return -1;
		}

	}
	else{
		echo "query failed: " . $mysqli->error;
	}
}

class Major1{
	
	private $num_courses = 30;
	private $num_electives = 11;
	private $total_ArtsSoc_courses = 2;
	private $min_Arts_courses = 1;
	private $min_Soc_courses = 1;
	private $compsci_courses = 2;
	
	public function get_num_courses(){
		return $this -> num_courses;
	}

	public $major_courses = array("COMP-1000", "COMP-1400", "COMP-1410", "COMP-2120", "COMP-2540", "COMP-2560", "COMP-2650", "COMP-2660", "COMP-3150", "COMP-3220", "COMP-3300", "COMP-3340", "MATH-1250", "MATH-1720", "STAT-2910");


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

	public function requirement_major(array &$user_courses, array $major_courses){
		foreach ($major_courses as $course){
			if(in_array($course, $user_courses) || in_array($this->substitute($course), $user_courses)){ //check if user has completed a course within the list of major courses, or if they have completed its substitute
																										//We must call $this->substitute to let the compiler know we are referring to the "substitute" function located in this class
				$user_key = array_search($course, $user_courses); //remove course from array of completed courses and from list of major courses
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
			if($lettercode == "COMP" && !in_array($course, $major_courses)){ //check if a non-major course is a COMP course
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
		return $this -> total_ArtsSoc_courses - ($num_Arts_courses + $num_Soc_courses);
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
    <body>
        <div class="container"> 
          <div class="row"> 
			<h1>Recommended Schedule <?php echo $program ?></h1>
            <h2>Page 4</h2>
            
			
			<?php 
			
				//Create list of courses that were checkmarked as completed in the last page
				for($i = 0; $i < ($_SESSION['incomplete_count']); $i++){
					$temp = "completed_" . $major_courses[$i]; //check if completed checkbox was checked for each course

					if(isset($_POST[$temp])){ //form data was submitted through POST method so we can get it through POST on this webpage
						$completedCourses[] = $major_courses[$i]; //add the course to the list of completed courses
					}
				}
				$completedCoursesClean = $completedCourses;
				
				print_r("completed courses CLEAN: ");
				print_r($completedCoursesClean);
				
				
				echo "<br><br>";
				
				$class = new Major1();
				print_r("remaining major courses: ");
				print_r($class -> requirement_major($completedCourses, $class -> major_courses));
				echo "<br><br>";
				print_r("remaining CS courses: ");
				print_r($class -> requirement_cs($completedCourses));
				echo "<br>";
				print_r("Arts Courses remaining: ");
				print_r($class -> requirement_arts($num_arts));
				echo "<br>";
				print_r("Soc Courses remaining: ");
				print_r($class -> requirement_soc($num_soc));
				echo "<br>";
				print_r("Either Arts or Soc Courses remaining: ");
				print_r($class -> requirement_ArtsOrSoc($num_arts, $num_soc));
				echo "<br>";
				print_r("Elective courses remaining: ");
				print_r($class -> requirement_electives($completedCourses, $num_elec));
				echo "<br>";
				
				foreach ($completedCourses as $course){
					$split = explode("-", $course);
					echo "Prereqs of ". $course . " are: ";
					print_r(get_prereqs($mysqli, $split[1]));
				}
				
				/*
				if($getcourses = $mysqli -> query("SELECT course_code, name FROM courses WHERE id IN (SELECT course_id FROM major_requirements WHERE major_id = " . $major_id . ")")){
					while($row = $getcourses -> fetch_row()){
						if(!in_array($row[0], $completedCourses)){ //if the course is not complete, add to list of incompletes
							$temp = "completed_" . $row[0]; //check if completed checkbox was checked for each course
							if(isset($_POST[$temp])){ //form data was submitted through POST method so we can get it through POST on this webpage
								$completedCourses[] = $row[0]; //add the course to the list of completed courses
							}
						}
					}
				}
				*/
				
				//print_r($remaining_major_courses);
				//echo "<br><br>";
				//print_r($completedCourses);
				
				$num_completed_courses = count($completedCoursesClean);
				$num_completed_courses += ($num_arts + $num_soc + $num_elec);
				for($i = 0; $i<ceil((($class -> get_num_courses()) - $num_completed_courses)/5); $i++){
					
				}
				
				
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