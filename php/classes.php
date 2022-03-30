<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

class Major1{
	
	$num_courses = 30;
	$num_electives = 11;
	$total_ArtsSoc_courses = 2;
	$min_Arts_courses = 1;
	$min_Soc_courses = 1;
	$compsci_courses = 2;

	$major_courses = array("COMP-1000", "COMP-1400", "COMP-1410", "COMP-2120", "COMP-2540", "COMP-2560", "COMP-2650", "COMP-2660", "COMP-3150", "COMP-3220", "COMP-3300", "COMP-3340", "MATH-1250", "MATH-1720", "STAT-2910");


	public function requirement_major(array $user_courses[]){
		foreach ($major_courses as $course){
			if(in_array($course, $user_courses) || in_array(substitute($course), $user_courses)){ //check if user has completed a course within the list of major courses, or if they have completed its substitute
				$user_key = array_search($course, $user_courses); //remove course from array of completed courses and from list of major courses
				unset($user_courses[$user_key]);
				array_values($user_courses);
				$major_key = array_search($course, $major_courses);
				unset($major_courses[$major_key]);
				array_values($major_courses);
			}
		}
		return $major_courses; //we return the remaining major courses
	}
	
	public function requirement_cs(array $user_courses[]){ //check for "additional computer science" requirements. For BCS (General) they are not limited by course level.
		int $viable=0;
		foreach ($user_courses as $course){
			$lettercode = explode("-", $course);
			if($lettercode == "COMP" && !in_array($course, $major_courses)){ //check if a non-major course is a COMP course
				$user_key = array_search($course, $user_courses);
				unset($user_courses[$user_key]);
				array_values($user_courses);
				$viable++;
			}
		}
		return ($compsci_courses - $viable); //return number of additional CS courses we need
	}
	
	public function requirement_arts(int $num_Arts_courses){ //user still needs an arts course
		return $min_Arts_courses - $num_Arts_courses;
	}
	public function requirement_soc(int $num_Soc_courses){ //user still needs a soc course
		return $min_Soc_courses - $num_Soc_courses;
	}
	public function requirement_ArtsOrSoc(int $num_Arts_courses, int $num_Soc_courses){ //user still needs X arts or soc courses
		return $total_ArtsSoc_courses - ($num_Arts_courses + $num_Soc_courses);
	}
	
	public function requirement_electives(array $user_courses[]){ //Take extra courses that were completed but don't account for any other requirement as extra electives
		int $count = 0;
		foreach($user_courses as $course){
			$user_key = array_search($course, $user_courses);
			unset($user_courses[$user_key]);
			array_values($user_courses);
			$count++;
		}
		return $num_electives - $count;
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
}

class Major2{
	
}

class Major3{
	
}

class Major4{
	
}

class Major5{
	
}

?>