<html lang="en">

<?php include('includes/header.html') ?>

<body>

<div class="container py-4">

    <header class="pb-3 mb-4 border-bottom">
        <a href="/" class="d-flex align-items-center text-dark text-decoration-none">
            <span class="fs-4">Computer Science Course Scheduler - Page 1</span>
        </a>
    </header>


    <form action="test2.php" method="get">

        <div class="mb-3">
            <label class="form-label" for="program">Select your major</label>
            <select class="form-select" name="programq" id="program" required>
                <?php foreach ($majors as $major): ?>
                    <option><?php echo $major['name'] ?></option>
                <?php endforeach; ?>
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

        <button type="submit" class="btn btn-primary mb-3">Submit</button>
    </form>

</div>

<?php //include('includes/footer.html') ?>

</body>
</html>
