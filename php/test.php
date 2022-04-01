<?php
session_start();
?>

<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">

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

<?php //if ($_GET['message']) { ?>
<!--<div class="alert alert-danger" role="alert">-->
<!--    --><? //= $_GET['message']; ?>
<!--</div>-->

<div class="container-md">

    <form action="test2.php" method="get">

        <div class="mb-3">
            <label class="form-label" for="program">Select your major</label>
            <select class="form-select" name="programq" id="program" required>
                <option selected>Bachelor of Computer Science (General)</option>
                <option>Bachelor of Computer Science (Honours)</option>
                <option>Bachelor of Computer Science (Honours Applied Computing)</option>
                <option>Bachelor of Science (Honours Computer Science with Software Engineering Specialization)</option>
                <option>Bachelor of Commerce (Honours Business Administration and Computer Science)</option>
                <option>Bachelor of Mathematics (Honours Mathematics and Computer Science)</option>
            </select>
            <div id="programHelp" class="form-text">
                <a class="help" href="contact.html">Let us know if you don't see your program!</a>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label" for="year">Select your year</label>
            <select class="form-select" name="yearq" id="year" required>
                <option selected>First Year</option>
                <option>Second Year</option>
                <option>Third Year</option>
                <option>Fourth Year</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label" for="term">Select your term</label>
            <select class="form-select" name="termq" id="term" required>
                <option selected>Fall</option>
                <option>Winter</option>
                <option>Summer</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary mb-3" onclick="return emptyFields()">Submit</button>
    </form>

</div>

</body>
</html>