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
                    <th scope="col">Code</th>
                    <th scope="col">Name</th>
                    <th scope="col">Semesters Offered</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($courses as $index => $course): ?>
                    <tr>
                        <th scope="row"><?php echo $index + 1 ?></th>
                        <td class="fw-bold"><?php echo $course['code'] ?></td>
                        <td class="fw-bold"><?php echo $course['name'] ?></td>
                        <td class="fw-bold"><?php echo $course['semesters'] ?></td>
                    </tr>
                    <?php foreach ($course['requirements'] as $i => $requirement): ?>
                        <tr>
                            <th></th>
                            <td><?php echo $requirement['code'] ?></td>
                            <td><?php echo $requirement['name'] ?></td>
                            <td><?php echo $requirement['semesters'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            </tbody>
        </table>

        <input type="hidden" name="program" value="<?php echo $_GET['program'] ?>">
        <input type="hidden" name="year" value="<?php echo $_GET['year'] ?>">
        <input type="hidden" name="semester" value="<?php echo $_GET['semester'] ?>">

        <a href="/" class="btn btn-primary">Back</a>
        <button type="submit" class="btn btn-primary">Next</button>
    </form>

</div>

</body>
</html>
