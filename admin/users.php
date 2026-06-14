<?php
include 'header.php';

// Protection: Only admin can access this page
if($_SESSION['role'] !== 'admin') {
    echo "<script>window.location.href='dashboard.php';</script>";
    exit();
}

// Handle Add User
if(isset($_POST['add_user'])) {
    $name = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];

    $sql = "INSERT INTO users (name, email, password, role) VALUES ('$name', '$email', '$password', '$role')";
    if($conn->query($sql)) {
        echo "<script>window.location.href='users.php?msg=added';</script>";
    } else {
        echo "<div class='alert alert-danger'>Error: " . $conn->error . "</div>";
    }
}

// Handle Delete User
if(isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    if($id != $_SESSION['user_id']) { // Prevent deleting self
        $conn->query("DELETE FROM users WHERE id=$id");
        echo "<script>window.location.href='users.php?msg=deleted';</script>";
    } else {
        echo "<script>alert('You cannot delete yourself!'); window.location.href='users.php';</script>";
    }
}
?>

<div class="row">
    <div class="col-md-4">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0">Add New User</h5>
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Role</label>
                        <select name="role" class="form-select">
                            <option value="editor">Editor</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                    <button type="submit" name="add_user" class="btn btn-primary w-100">Create User</button>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0">All Users</h5>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql = "SELECT * FROM users";
                        $result = $conn->query($sql);
                        while($row = $result->fetch_assoc()):
                        ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo $row['name']; ?></td>
                            <td><?php echo $row['email']; ?></td>
                            <td><span class="badge bg-<?php echo $row['role']=='admin'?'danger':'secondary'; ?>"><?php echo ucfirst($row['role']); ?></span></td>
                            <td>
                                <?php if($row['id'] != $_SESSION['user_id']): ?>
                                <a href="users.php?delete=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')"><i class="fas fa-trash"></i></a>
                                <?php else: ?>
                                <button class="btn btn-secondary btn-sm" disabled><i class="fas fa-trash"></i></button>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
