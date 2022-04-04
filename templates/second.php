<html lang="en">

<?php include('includes/header.html') ?>

<body>

<div class="container py-4">

    <header class="pb-3 mb-4 border-bottom">
        <a href="/" class="d-flex align-items-center text-dark text-decoration-none">
            <span class="fs-4">Computer Science Course Scheduler - Page 2</span>
        </a>
    </header>

    <form action="/third" method="get">

        <table class="table">
            <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Course</th>
                <th scope="col">Semesters Offered</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($courses as $index => $course): ?>
                <tr>
                    <th scope="row"><?php echo $index + 1 ?></th>
                    <td><?php echo $course['name'] ?></td>
                    <td><?php echo $course['semesters'] ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>

        <div class="mb-3">
            <label for="art" class="form-label">Number of Arts/Languages courses completed:</label>
            <input type="number" class="form-control" name="art" id="art" required>
        </div>

        <div class="mb-3">
            <label for="social" class="form-label">Number of Social Sciences courses completed:</label>
            <input type="number" class="form-control" name="social" id="social" required>
        </div>

        <div class="mb-3">
            <label for="electives" class="form-label">Number of Elective courses completed:</label>
            <input type="number" class="form-control" name="electives" id="electives" required>
        </div>

        <a href="/" class="btn btn-primary">Back</a>
        <button type="submit" class="btn btn-primary">Submit</button>
    </form>

</div>

</body>
</html>
