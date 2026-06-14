<?php
include 'header.php';

// Handle Add Category
if(isset($_POST['add_category'])) {
    $cat_name = $conn->real_escape_string($_POST['category_name']);
    if(!empty($cat_name)) {
        $conn->query("INSERT INTO categories (category_name) VALUES ('$cat_name')");
        echo "<script>window.location.href='categories.php';</script>";
    }
}

// Handle Update Category
if(isset($_POST['update_category'])) {
    $id = intval($_POST['category_id']);
    $cat_name = $conn->real_escape_string($_POST['category_name']);
    if(!empty($cat_name)) {
        $conn->query("UPDATE categories SET category_name='$cat_name' WHERE id=$id");
        echo "<script>window.location.href='categories.php';</script>";
    }
}

// Handle Delete Category
if(isset($_GET['delete'])) {
    if($_SESSION['role'] == 'admin') {
        $id = intval($_GET['delete']);
        $conn->query("DELETE FROM categories WHERE id=$id");
        echo "<script>window.location.href='categories.php';</script>";
    } else {
        echo "<script>alert('Only admin can delete category'); window.location.href='categories.php';</script>";
    }
}

// Fetch category for editing
$edit_mode = false;
$edit_id = 0;
$edit_name = "";
if(isset($_GET['edit'])) {
    $edit_mode = true;
    $edit_id = intval($_GET['edit']);
    $edit_res = $conn->query("SELECT * FROM categories WHERE id=$edit_id");
    if($edit_row = $edit_res->fetch_assoc()) {
        $edit_name = $edit_row['category_name'];
    }
}
?>

<div class="row">
    <div class="col-md-4">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0"><?php echo $edit_mode ? 'Edit Category' : 'Add Category'; ?></h5>
            </div>
            <div class="card-body">
                <form method="POST">
                    <?php if($edit_mode): ?>
                        <input type="hidden" name="category_id" value="<?php echo $edit_id; ?>">
                    <?php endif; ?>
                    <div class="mb-3">
                        <label class="form-label">Category Name</label>
                        <input type="text" name="category_name" class="form-control" value="<?php echo $edit_name; ?>" required>
                    </div>
                    <button type="submit" name="<?php echo $edit_mode ? 'update_category' : 'add_category'; ?>" class="btn btn-primary w-100">
                        <?php echo $edit_mode ? 'Update Category' : 'Add New Category'; ?>
                    </button>
                    <?php if($edit_mode): ?>
                        <a href="categories.php" class="btn btn-secondary w-100 mt-2">Cancel</a>
                    <?php endif; ?>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0">All Categories</h5>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql = "SELECT * FROM categories";
                        $result = $conn->query($sql);
                        while($row = $result->fetch_assoc()):
                        ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo $row['category_name']; ?></td>
                            <td>
                                <a href="categories.php?edit=<?php echo $row['id']; ?>" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>
                                <?php if($_SESSION['role'] == 'admin'): ?>
                                <a href="categories.php?delete=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')"><i class="fas fa-trash"></i></a>
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
