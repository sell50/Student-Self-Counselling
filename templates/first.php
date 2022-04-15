<html lang="en">

<?php include('includes/header.html') ?>

<body>

<div class="container py-4">

    <header class="pb-3 mb-4 border-bottom">
        <a href="/" class="d-flex align-items-center text-dark text-decoration-none">
            <span class="fs-4">Computer Science Course Scheduler - Page 1</span>
        </a>
    </header>


    <form action="/second" method="get">

        <div class="mb-3">
            <label class="form-label" for="program">Select your program</label>
            <select class="form-select" name="program" id="program" required>
                <?php foreach ($programs as $program): ?>
                    <option value="<?php echo $program['id'] ?>"><?php echo $program['name'] ?></option>
                <?php endforeach; ?>
            </select>
            <div id="programHelp" class="form-text">
                <a class="help" href="contact.html">Let us know if you don't see your program!</a>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label" for="year">Select your year</label>
            <select class="form-select" name="year" id="year" required>
                <option value="1" selected>First Year</option>
                <option value="2">Second Year</option>
                <option value="3">Third Year</option>
                <option value="4">Fourth Year</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label" for="semester">Select your semester</label>
            <select class="form-select" name="semester" id="semester" required>
                <?php foreach ($semesters as $semester): ?>
                    <option><?php echo $semester['name'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <button type="submit" class="btn btn-primary mb-3">Submit</button>
    </form>

</div>

<?php //include('includes/footer.html') ?>

</body>
</html>
