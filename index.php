<?php include 'includes/db.php'; ?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Employee Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"/>
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet"/>
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
    <h2 class="mb-4">Employee Management</h2>

    <button class="btn btn-success mb-3" id="addBtn">➕ Add New Employee</button>

    <table class="table table-bordered table-striped" id="employeeTable">
        <thead class="table-dark">
        <tr>
            <th>ID</th>
            <th>Photo</th>
            <th>Name</th>
            <th>Phone</th>
            <th>Address</th>
            <th>Doc</th>
            <th>Action</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $stmt = $conn->query("SELECT * FROM employees ORDER BY id DESC");
        while ($row = $stmt->fetch()):
            $photo = !empty($row['photo']) && file_exists("assets/uploads/" . $row['photo'])
                ? "assets/uploads/" . $row['photo']
                : "assets/uploads/default.jpg";
            ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><img src="<?= $photo ?>" width="50" height="50" style="object-fit: cover; border-radius: 50%;"></td>
                <td><?= htmlspecialchars($row['name']) ?></td>
                <td><?= htmlspecialchars($row['phone']) ?></td>
                <td><?= htmlspecialchars($row['address']) ?></td>
                <td>
                    <?php if (!empty($row['document'])): ?>
                        <a href="assets/uploads/<?= $row['document'] ?>" target="_blank">View</a>
                    <?php endif; ?>
                </td>
                <td>
                    <button class="btn btn-sm btn-primary editBtn" data-id="<?= $row['id'] ?>">Edit</button>
                    <button class="btn btn-sm btn-danger deleteBtn" data-id="<?= $row['id'] ?>">Delete</button>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>

<!-- Modal -->
<div class="modal fade" id="employeeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" id="modalContent">
            <!-- Form content loaded here -->
        </div>
    </div>
</div>

<!-- JS Libraries -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function () {
    $('#employeeTable').DataTable();

    $('#addBtn').click(function () {
        $.get("employee_form.php", {mode: "add"}, function (form) {
            $('#modalContent').html(form);
            new bootstrap.Modal(document.getElementById('employeeModal')).show();
        });
    });

    $('.editBtn').click(function () {
        const id = $(this).data('id');
        $.get("employee_form.php", {mode: "edit", id: id}, function (form) {
            $('#modalContent').html(form);
            new bootstrap.Modal(document.getElementById('employeeModal')).show();
        });
    });

    $('.deleteBtn').click(function () {
        const id = $(this).data('id');
        Swal.fire({
            title: 'Are you sure?',
            text: "This employee will be deleted!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonText: 'Cancel',
            confirmButtonText: 'Yes, delete'
        }).then((result) => {
            if (result.isConfirmed) {
                $.get("employee_delete.php", {id: id}, function () {
                    Swal.fire('Deleted!', 'Employee has been deleted.', 'success')
                        .then(() => location.reload());
                });
            }
        });
    });
});
</script>
</body>
</html>
