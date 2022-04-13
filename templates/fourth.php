<html lang="en">

<?php include('includes/header.html') ?>

<body>

<div class="container py-4">

    <header class="pb-3 mb-4 border-bottom">
        <a href="/" class="d-flex align-items-center text-dark text-decoration-none">
            <span class="fs-4">Computer Science Course Scheduler - Page 4</span>
        </a>
    </header>

<!--    <?php /*foreach ($tables as $table): */?>
        <h3><?php /*echo $table['name'] */?></h3>
        <table class="table">
            <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Code</th>
                <th scope="col">Name</th>
                <th scope="col">Semester Taken</th>
            </tr>
            </thead>
            <tbody>
            <?php /*foreach ($table['courses'] as $index => $course): */?>
                <tr>
                    <th scope="row"><?php /*echo $index + 1 */?></th>
                    <td class="fw-bold"><?php /*echo $course['code'] */?></td>
                    <td class="fw-bold"><?php /*echo $course['name'] */?></td>
                </tr>
            <?php /*endforeach; */?>
            </tbody>
        </table>
    --><?php /*endforeach; */?>

</div>

</body>
</html>
