<?php
session_start();
require "env.php";

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
}
*/

$num_arts = $_POST['ArtsCourses'];
$num_soc = $_POST['SocCourses'];
$num_elec = $_POST['ElecCourses'];
$program = $_SESSION["program"];
$year = $_SESSION["year"];
$term = $_SESSION["term"];
$courseCodes = $_SESSION["courseCodes"];
$courses_this_year = $_SESSION["courses_this_year"];
$incomplete_count = 1;
$complete_count = 0;
$major_id = $_SESSION["major_id"];


function get_prereqs($mysqli, $coursecode){ //have to pass $mysqli as an argument because we are using a global variable within a local scope
	if($numReqs = $mysqli -> query("SELECT num_requirements FROM course_requirements WHERE course_id = " . $coursecode)){
		$row = $numReqs -> fetch_row();
		if($reqsList = $mysqli -> query("SELECT * FROM ".$row[0]."_requirements WHERE course_id = " . $coursecode)){
			$reqsRow = $reqsList -> fetch_row();
			$reqsRow = array_shift($reqsRow);
			print_r($reqsRow);
		}
		else{
			echo "query failed: " . $mysqli -> error;
		}

	}
	else{
		echo "query failed: " . $mysqli->error;
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
			<h1>Required courses for <?php echo $year?> for <?php echo $program ?></h1>
            <h2>Page 3</h2>
            <p>Incomplete Courses</p>
			
			<?php 
			
			$completedCourses = [];
			for($i = 0; $i < ($courses_this_year-1); $i++){
				$temp = "completed_" . $courseCodes[$i]; //check if completed checkbox was checked for each course
	
				if(isset($_POST[$temp])){
					$completedCourses[] = $courseCodes[$i]; //add the course to the list of completed courses
					$numeric_code = explode("-", $courseCodes[$i]);
					get_prereqs($mysqli, $numeric_code[1]);
				}
			}
			
			//perform a query to find the major id according to the program
			
			if($getcourses = $mysqli -> query("SELECT course_code, name FROM courses WHERE id IN (SELECT course_id FROM major_requirements WHERE major_id = " . $major_id . ")")){
				echo "
					<div class=\"col\">
						<table class=\"table\">
						  <thead>
							<tr>
							  <th scope=\"col\">#</th>
							  <th scope=\"col\">Course</th>
							</tr>
						  </thead>
						  <tbody>
				";
				
				//dynamically create table and add courses to it
				$preplist = [];
				while($row = $getcourses -> fetch_row()){
					
					if(!in_array($row[0], $completedCourses)){
						echo "<tr>";
						echo "<th scope=\"row\">". $incomplete_count . "</th>";
						$incomplete_count += 1;
						echo "<td>". $row[0]. " - ". $row[1] ."</td>";
						echo "</tr>";
					}
					else{ //Since we fetch through query results iteratively and we need data to be added to another table instead of this one if the course is complete, we'll prep the HTML we want to display in the other table
						
						$complete_count++;
						$prep = "
						<tr>
						<th scope=\"row\">". $complete_count . "</th>
						<td>". $row[0]. " - ". $row[1] ."</td>
						</tr>";
						$preplist[] = $prep;
					}
				}
				
				echo "
					</tbody>
				  </table>
				</div>
				";
			}
			else{
				echo "failed : ".$mysqli -> error;
			}
			
			?>
			
			<br>
			<p>Completed Courses</p>
			
			<?php
			
				
				echo "
						<div class=\"col\">
							<table class=\"table\">
							  <thead>
								<tr>
								  <th scope=\"col\">#</th>
								  <th scope=\"col\">Course</th>
								</tr>
							  </thead>
							  <tbody>
					";
					
				for($i = 0; $i < ($complete_count); $i++){ //fill completed courses table with prepped html
					echo $preplist[$i];
				}
				
				echo "
					</tbody>
				  </table>
				</div>
				";
				
			?>
			
			<!--
            <div class="col">
              <table class="table">
                  <thead>
                    <tr>
                      <th scope="col">#</th>
                      <th scope="col">Course</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <th scope="row">1</th>
                      <td>COMP1000 - INTRO TO COMP SCI</td>
                      
                    
                    </tr>
                    <tr>
                      <th scope="row">2</th>
                      <td>COMP2310 - NAME HERE</td>
              
                    </tr>
                    <tr>
                      <th scope="row">3</th>
                      <td>COMP4990 - PROJECT</td>
                    </tr>
                  </tbody>
              </table>
            </div>
            <div class="col">
              <div class="row">
                <p>Add Course</p>
              </div>
              
              <button type="submit" class="btn btn-danger">Add</button>
              
              <!-- <div class="col">
                <button type="submit" class="btn btn-danger">Add</button>
              <!-- </div> 
            </div> 
			-->
			
			
          </div>
            <!--<button type="submit" class="btn btn-primary">Back</button>--><!--dont use?-->
            <a class="btn btn-primary" href="test2.html" role="button">Back</a> <!--how to submit form data with link?-->
            <!--<button type="submit" class="btn btn-primary">Continue</button>--><!--dont use?-->
            <a class="btn btn-primary" href="test4.html" role="button">Continue</a> <!--how to submit form data with link?-->

        </div>

        <!-- Popper and Bootstrap JS -->
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js" integrity="sha384-7+zCNj/IqJ95wo16oMtfsKbZ9ccEh31eOz1HGyDuCQ6wgnyJNSYdrPa03rtR1zdB" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js" integrity="sha384-QJHtvGhmr9XOIpI6YVutG+2QOK9T+ZnN4kzFN1RtK3zEFEIsxhlmWl5/YESvpZ13" crossorigin="anonymous"></script>
        
		<?php $mysqli -> close(); ?>
    </body>
</html>