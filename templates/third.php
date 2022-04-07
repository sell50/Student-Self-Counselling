<html lang="en">

<?php include('includes/header.html') ?>

<body>

<div class="container py-4">

    <header class="pb-3 mb-4 border-bottom">
        <a href="/" class="d-flex align-items-center text-dark text-decoration-none">
            <span class="fs-4">Computer Science Course Scheduler - Page 3</span>
        </a>
    </header>

    <form action="/fourth" method="post" id="form">

        <table class="table ">
            <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Code</th>
                <th scope="col">Name</th>
                <th scope="col">Completed</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($courses as $index => $course): ?>
                <tr>
                    <th scope="row"><?php echo $index + 1 ?></th>
                    <td class="fw-bold"><?php echo $course['code'] ?></td>
                    <td class="fw-bold"><?php echo $course['name'] ?></td>
                    <td>
                        <input class="form-check-input"
                               type="checkbox"
                               name="courses[]"
                               onclick="checkForm()"
                               value="<?php echo $course['code'] ?>"
                        >
                    </td>
                </tr>
                <?php foreach ($course['requirements'] as $i => $requirement): ?>
                    <tr>
                        <th colspan="2"></th>
                        <td colspan="2"><?php echo $requirement['code'] . ' - ' . $requirement['name'] ?></td>
                    </tr>
                <?php endforeach; ?>
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
        <button class="btn btn-primary" type="submit" id="submit">Submit</button>
    </form>

</div>

<script type="application/javascript">
    function checkForm() {
        const form = new FormData(document.getElementById("form"));
        document.getElementById("submit").disabled = !form.has("courses[]");
    }
</script>

</body>
</html>
