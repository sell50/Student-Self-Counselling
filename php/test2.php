<?php
session_start();
require "env.php";

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
	
		<?php
		
		//Trying to find which year the user is in (numerically). This looks ugly but I'm not sure how we could parse words like "first", "second" to integers.
		//Alternatively we could have the user enter their year as a number.
		$year = 0;
		
		if($_GET['yearq'] == "First Year"){
			$year = 1;
		}
		else if($_GET['yearq'] == "Second Year"){
			$year = 2;
		}
		else if($_GET['yearq'] == "Third Year"){
			$year = 3;
		}
		else if($_GET['yearq'] == "Fourth Year"){
			$year = 4;
		}
		
		$_SESSION["year"] = $_GET['yearq'];
		$_SESSION["term"] = $_GET['termq'];
		$_SESSION["program"] = $_GET['programq'];
		$year_min = $year * 1000;
		$year_max = ($year +1)*1000;
		$count = 1; //counter we will use for numbering table entries later
		
		//perform a query to find the major id according to the program
		if ($getmajor_id = $mysqli -> query("SELECT id FROM majors WHERE name = '" . $_GET['programq'] . "'")) {
			$row = $getmajor_id -> fetch_row();
			$major_id = $row[0];
			$_SESSION["major_id"] = $major_id;
		}
		else{
			echo "Failed 1: ". $mysqli -> error;
		}

		?>
		
		<script type = "text/javascript">
					function emptyFields(){
						if(document.getElementById("numArtsCourses").value == ""){
							<?php $message = "Please fill all fields"?>
							location.href = "https://roata.myweb.cs.uwindsor.ca/Self-Student%20Counselling/test2.php?message=Please+fill+in+all+fields";
							return false;
						}
						else if(document.getElementById("numSocCourses").value == ""){
							<?php $message = "Please fill all fields"?>
							location.href = "https://roata.myweb.cs.uwindsor.ca/Self-Student%20Counselling/test2.php?message=Please+fill+in+all+fields";

							return false;
						}
						else if(document.getElementById("numElecCourses").value == ""){
							<?php $message = "Please fill all fields"?>
							location.href = "https://roata.myweb.cs.uwindsor.ca/Self-Student%20Counselling/test2.php?message=Please+fill+in+all+fields";

							return false;
						}
						else{
							return true;
						}
					}
		</script>
		
        <div class="container-fluid">
          <div class="row">            
			<?php if($_GET['termq']){ ?>
				<h3>Required courses for <?php echo $_GET['yearq']?> for <?php echo $_GET['programq']?></h3>
			<?php } ?>
			
            <p>Please select the courses you have already completed.</p>
			
			<?php
			$year_min_byTen = $year_min*10;
			$year_max_byTen = $year_max*10;
			if($getcourses = $mysqli -> query("SELECT course_code, name FROM courses WHERE (id BETWEEN ". $year_min . " AND " . $year_max . " OR id BETWEEN ". $year_min_byTen ." AND " . $year_max_byTen .") AND id IN (SELECT course_id FROM major_requirements WHERE major_id = " . $major_id . ")")){
				$courseCodes = [];
				echo "
				<div class=\"col\">
					<table class=\"table\">
					  <thead>
						<tr>
						  <th scope=\"col\">#</th>
						  <th scope=\"col\">Course</th>
						  <th scope=\"col\">When/Typically Offered</th>
						</tr>
					  </thead>
					  <tbody>
				";
				
				//dynamically create table and add courses to it
				while($row = $getcourses -> fetch_row()){
					$courseCodes[] = $row[0]; #row[x] should look like "COMP-2120, Object-Oriented Programming Using Java"
					echo "<tr>";
					echo "<th scope=\"row\">". $count . "</th>";
					$count += 1;
					echo "<td>". $row[0]. " - ". $row[1] ."</td>";
					$prep_term = "";
					if($getterm = $mysqli -> query("SELECT semester FROM course_offerings WHERE course_id = (SELECT id FROM courses WHERE course_code = '". $row[0] ."')")){
						while($termrow = $getterm -> fetch_row()){
							$prep_term .= " " . $termrow[0];
						}
					}
					else{
						echo "" . $mysqli -> error;
					}
					echo "<td>" . $prep_term . "</td>";
					echo "</tr>";
				}
				
				echo "
							</tbody>
						</table>
						<form autocomplete=\"off\" action=\"test3.php\" method=\"post\">
							<label for=\"ArtsCourses\">Number of Arts/Languages courses completed:</label>
							<input type=\"text\" id=\"numArtsCourses\" name=\"ArtsCourses\"><br><br>
							<label for=\"SocCourses\">Number of Social Sciences courses completed:</label>
							<input type=\"text\" id=\"numSocCourses\" name=\"SocCourses\"><br><br>
							<label for=\"ElecCourses\">Number of Elective courses completed:</label>
							<input type=\"text\" id=\"numElecCourses\" name=\"ElecCourses\"><br><br>
							<input type=\"hidden\" name=\"program\" value=\""
							
							. $_SESSION["program"] . "\">
							<input type=\"hidden\" name=\"year\" value=\""
							. $_SESSION["year"] . "\">
							<input type=\"hidden\" name=\"term\" value=\""
							. $_SESSION["term"] . "\">
						<button type=\"button\" class=\"btn btn-primary mb-3\" onclick = \"location.href='https://roata.myweb.cs.uwindsor.ca/Self-Student%20Counselling/test.php';\">Back</button>
						<button type=\"submit\" class=\"btn btn-primary mb-3\" onclick = \"return emptyFields()\">Submit</button>
						</form>
				</div>
				";
				
				$_SESSION["courseCodes"] = $courseCodes;
				$_SESSION["courses_this_year"] = $count;
			}





			

			if($getcourses = $mysqli -> query("SELECT course_code, name FROM extra_courses WHERE (id BETWEEN ". $year_min . " AND " . $year_max . " OR id BETWEEN ". $year_min_byTen ." AND " . $year_max_byTen .") AND id IN (SELECT course_id FROM major_requirements WHERE major_id = " . $major_id . ")")){
				$courseCodes = [];
				echo "
				<div class=\"col\">
					<table class=\"table\">
					  <thead>
						<tr>
						  <th scope=\"col\">#</th>
						  <th scope=\"col\">Course</th>
						  <th scope=\"col\">When/Typically Offered</th>
						</tr>
					  </thead>
					  <tbody>
				";
				
				//dynamically create table and add courses to it
				while($row = $getcourses -> fetch_row()){
					$courseCodes[] = $row[0]; #row[x] should look like "COMP-2120, Object-Oriented Programming Using Java"
					echo "<tr>";
					echo "<th scope=\"row\">". $count . "</th>";
					$count += 1;
					echo "<td>". $row[0]. " - ". $row[1] ."</td>";
					$prep_term = "";
					if($getterm = $mysqli -> query("SELECT semester FROM course_offerings WHERE course_id = (SELECT id FROM extra_courses WHERE course_code = '". $row[0] ."')")){
						while($termrow = $getterm -> fetch_row()){
							$prep_term .= " " . $termrow[0];
						}
					}
					else{
						echo "" . $mysqli -> error;
					}
					echo "<td>" . $prep_term . "</td>";
					echo "</tr>";
				}
			}















			
			?>
			
          </div>
        </div>

        <!-- Popper and Bootstrap JS -->
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js" integrity="sha384-7+zCNj/IqJ95wo16oMtfsKbZ9ccEh31eOz1HGyDuCQ6wgnyJNSYdrPa03rtR1zdB" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js" integrity="sha384-QJHtvGhmr9XOIpI6YVutG+2QOK9T+ZnN4kzFN1RtK3zEFEIsxhlmWl5/YESvpZ13" crossorigin="anonymous"></script>
        
		<?php $mysqli -> close(); ?>
    </body>
</html>