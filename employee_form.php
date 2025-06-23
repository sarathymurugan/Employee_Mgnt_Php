<?php
include 'includes/db.php';

$id = $_GET['id'] ?? null;
$mode = $_GET['mode'] ?? 'add';

$data = [
    'name' => '',
    'phone' => '',
    'address' => '',
    'photo' => '',
    'document' => '',
];

if ($mode === 'edit' && $id) {
    $stmt = $conn->prepare("SELECT * FROM employees WHERE id = ?");
    $stmt->execute([$id]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<form id="employeeForm" enctype="multipart/form-data">
    <input type="hidden" name="id" value="<?= $id ?>">
    <input type="hidden" name="mode" value="<?= $mode ?>">

    <div class="modal-header">
        <h5 class="modal-title"><?= $mode === 'edit' ? 'Edit' : 'Add' ?> Employee</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
    </div>

    <div class="modal-body">
        <div class="mb-3">
            <label>Name *</label>
            <input type="text" name="name" value="<?= htmlspecialchars($data['name']) ?>" required class="form-control">
        </div>

        <div class="mb-3">
            <label>Phone *</label>
            <input type="text" name="phone" value="<?= htmlspecialchars($data['phone']) ?>" required class="form-control" pattern="[0-9]{10}" title="10 digit number only">
        </div>

        <div class="mb-3">
            <label>Address *</label>
            <textarea name="address" required class="form-control"><?= htmlspecialchars($data['address']) ?></textarea>
        </div>

        <div class="mb-3">
            <label>Profile Photo <?= $mode === 'add' ? '*' : '' ?></label>
            <input type="file" name="photo" accept="image/*" class="form-control">
            <?php
            $photo = !empty($data['photo']) && file_exists("assets/uploads/" . $data['photo'])
                ? "assets/uploads/" . $data['photo']
                : "assets/uploads/default.png";
            ?>
            <?php if ($mode === 'edit'): ?>
                <img src="<?= $photo ?>" width="60" height="60" class="mt-2 rounded-circle" style="object-fit: cover;">
            <?php endif; ?>
        </div>

        <div class="mb-3">
            <label>Document <?= $mode === 'add' ? '*' : '' ?></label>
            <input type="file" name="document" accept=".pdf,.doc,.docx" class="form-control">
            <?php if ($mode === 'edit' && $data['document']): ?>
                <a href="assets/uploads/<?= $data['document'] ?>" target="_blank" class="d-block mt-2">View Existing</a>
            <?php endif; ?>
        </div>
    </div>

    <div class="modal-footer">
        <button type="submit" class="btn btn-success">Save</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
    </div>
</form>

<script>
$('#employeeForm').on('submit', function (e) {
    e.preventDefault();
    const formData = new FormData(this);

    $.ajax({
        url: 'employee_save.php',
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function (res) {
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: res,
                showConfirmButton: false,
                timer: 1200
            }).then(() => location.reload());
        },
        error: function () {
            Swal.fire('Error', 'Something went wrong.', 'error');
        }
    });
});
</script>
