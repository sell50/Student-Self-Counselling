<?php
session_start();
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
            <h1>Welcome to the Computer Science Course Scheduler!</h1>
            <h2>Page 1</h2>
            <p>Please fill out some information to get your schedule.</p>
        </div>
		
		<?php if ($_GET['message']) { ?>
        <div class="alert alert-danger" role="alert">
            <?= $_GET['message']; ?>
        </div>
    <?php } ?>

        <div class="container-md">
            <form autocomplete="off" action="test2.php" method="get">
 
                <div class="mb-3 autocomplete">
                    <label for="program" class="form-label">Select your major</label>
                    <input type="search" list="datalistOptions" class="form-control" id="program" aria-describedby="programHelp" name="programq" >
                     <datalist id="datalistOptions">
                        <option value="Bachelor of Computer Science (General)">
                        <option value="Bachelor of Computer Science (Honours)">
                        <option value="Bachelor of Computer Science (Honours Applied Computing)">
                        <option value="Bachelor of Science (Honours Computer Science with Software Engineering Specialization)">
                        <option value="Bachelor of Commerce (Honours Business Administration and Computer Science)">
                        <option value="Bachelor of Mathematics (Honours Mathematics and Computer Science)">
                    </datalist>
                      
                    <div id="programHelp" class="form-text"><a class="help"href="contact.html" >Let us know if you don't see your program!</a></div>
                </div>
                <div class="mb-3 autocomplete">
                    <label for="year" class="form-label">Select your year</label>
                    <input type="search" list="datalistOptions2" class="form-control" id="year" name="yearq" >
                    <datalist id="datalistOptions2">
                        <option value="First Year">
                        <option value="Second Year">
                        <option value="Third Year">
                        <option value="Fourth Year">  
                    </datalist>
                </div>
                <div class="mb-3 autocomplete">
                    <label for="term" class="form-label">Select your term</label>
                    <input type="search" list="datalistOptions3" class="form-control" id="term" name="termq" >
                    <datalist id="datalistOptions3">
                        <option value="Fall">
                        <option value="Winter">
                        <option value="Summer">
                    </datalist>
                </div>
				
				<script type = "text/javascript">
					function emptyFields(){
						if(document.getElementById("year").value == ""){
							<?php $message = "Please fill all fields"?>
							location.href = "https://roata.myweb.cs.uwindsor.ca/Self-Student%20Counselling/test.php?message=Please+fill+in+all+fields";

							return false;
						}
						else if(document.getElementById("program").value == ""){
							<?php $message = "Please fill all fields"?>
							location.href = "https://roata.myweb.cs.uwindsor.ca/Self-Student%20Counselling/test.php?message=Please+fill+in+all+fields";

							return false;
						}
						else if(document.getElementById("term").value == ""){
							location.href = "https://roata.myweb.cs.uwindsor.ca/Self-Student%20Counselling/test.php?message=Please+fill+in+all+fields";
							return false;
						}
						else{
							return true;
						}
					}
				</script>
				
				<button type="submit" class="btn btn-primary mb-3" onclick="return emptyFields()">Submit</button>

            </form>
        </div>


        <!-- Popper and Bootstrap JS -->
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js" integrity="sha384-7+zCNj/IqJ95wo16oMtfsKbZ9ccEh31eOz1HGyDuCQ6wgnyJNSYdrPa03rtR1zdB" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js" integrity="sha384-QJHtvGhmr9XOIpI6YVutG+2QOK9T+ZnN4kzFN1RtK3zEFEIsxhlmWl5/YESvpZ13" crossorigin="anonymous"></script>
        
    </body>
</html>