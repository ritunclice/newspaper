<?php
include 'header.php';

$action = isset($_GET['action']) ? $_GET['action'] : 'list';
$msg = '';

// Helper function to log activity
function log_activity($action_name, $details, $news_id = 0) {
    global $conn;
    $user_id = $_SESSION['user_id'];
    $user_name = $_SESSION['user_name'];
    $action_name = $conn->real_escape_string($action_name);
    $details = $conn->real_escape_string($details);
    $news_id = intval($news_id);
    $conn->query("INSERT INTO activity_logs (user_id, user_name, action, details, news_id) VALUES ($user_id, '$user_name', '$action_name', '$details', $news_id)");
}

// Handle Status Toggle
if($action == 'status' && isset($_GET['id']) && isset($_GET['set'])) {
    $id = intval($_GET['id']);
    $status = $_GET['set'] == 'published' ? 'published' : 'unpublished';
    
    // Get news title for log
    $news_title = $conn->query("SELECT title FROM news WHERE id=$id")->fetch_assoc()['title'];
    
    if($conn->query("UPDATE news SET status='$status' WHERE id=$id")) {
        log_activity("Status Change", "Changed status of '$news_title' to $status", $id);
        echo "<script>window.location.href='news.php?msg=status_updated';</script>";
    }
}

// Handle Delete (ADMIN ONLY)
if($action == 'delete' && isset($_GET['id'])) {
    if($_SESSION['role'] !== 'admin') {
        echo "<script>alert('Permission Denied: Only admins can delete news.'); window.location.href='news.php';</script>";
        exit();
    }
    
    $id = intval($_GET['id']);
    // Get title for log before deletion
    $news_title_query = $conn->query("SELECT title FROM news WHERE id=$id");
    if($news_title_query->num_rows > 0) {
        $news_title = $news_title_query->fetch_assoc()['title'];
        
        // Get image to delete
        $img_res = $conn->query("SELECT image FROM news WHERE id=$id");
        if($img_res->num_rows > 0){
            $img = $img_res->fetch_assoc()['image'];
            if($img && file_exists("../uploads/".$img)) {
                unlink("../uploads/".$img);
            }
        }
        if($conn->query("DELETE FROM news WHERE id=$id")) {
            log_activity("Deletion", "Deleted news article: $news_title");
            echo "<script>window.location.href='news.php?msg=deleted';</script>";
        }
    }
}

// Handle Add News
if($action == 'add' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $conn->real_escape_string($_POST['title']);
    $category_id = intval($_POST['category_id']);
    $description = $conn->real_escape_string($_POST['description']);
    $author = $conn->real_escape_string($_POST['author']);
    $status = $conn->real_escape_string($_POST['status']);
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    
    $image = '';
    if(isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "../uploads/";
        $image = time() . "_" . basename($_FILES["image"]["name"]);
        move_uploaded_file($_FILES["image"]["tmp_name"], $target_dir . $image);
    }
    
    $sql = "INSERT INTO news (title, category_id, image, description, author, is_featured, status) VALUES ('$title', $category_id, '$image', '$description', '$author', $is_featured, '$status')";
    if($conn->query($sql)) {
        $new_id = $conn->insert_id;
        log_activity("Creation", "Published new article: $title", $new_id);
        echo "<script>window.location.href='news.php?msg=added';</script>";
    } else {
        $msg = "<div class='alert alert-danger'>Error: " . $conn->error . "</div>";
    }
}

// Handle Edit News
if($action == 'edit' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    if($_SERVER['REQUEST_METHOD'] == 'POST') {
        $title = $conn->real_escape_string($_POST['title']);
        $category_id = intval($_POST['category_id']);
        $description = $conn->real_escape_string($_POST['description']);
        $author = $conn->real_escape_string($_POST['author']);
        $status = $conn->real_escape_string($_POST['status']);
        $is_featured = isset($_POST['is_featured']) ? 1 : 0;
        
        $sql = "UPDATE news SET title='$title', category_id=$category_id, description='$description', author='$author', is_featured=$is_featured, status='$status' WHERE id=$id";
        
        if(isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $target_dir = "../uploads/";
            $image = time() . "_" . basename($_FILES["image"]["name"]);
            move_uploaded_file($_FILES["image"]["tmp_name"], $target_dir . $image);
            $sql = "UPDATE news SET title='$title', category_id=$category_id, description='$description', author='$author', image='$image', is_featured=$is_featured, status='$status' WHERE id=$id";
        }
        
        if($conn->query($sql)) {
            log_activity("Modification", "Updated article: $title", $id);
            echo "<script>window.location.href='news.php?msg=updated';</script>";
        } else {
             $msg = "<div class='alert alert-danger'>Error: " . $conn->error . "</div>";
        }
    }
    
    $edit_sql = "SELECT * FROM news WHERE id=$id";
    $edit_res = $conn->query($edit_sql);
    $edit_row = $edit_res->fetch_assoc();
}

?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><?php echo ucfirst($action); ?> News</h1>
    <?php if($action == 'list'): ?>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="news.php?action=add" class="btn btn-sm btn-primary">
            <i class="fas fa-plus"></i> Add New
        </a>
    </div>
    <?php else: ?>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="news.php" class="btn btn-sm btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to List
        </a>
    </div>
    <?php endif; ?>
</div>

<?php 
if(isset($_GET['msg'])) {
    if($_GET['msg']=='deleted') echo "<div class='alert alert-success'>News deleted successfully.</div>";
    if($_GET['msg']=='added') echo "<div class='alert alert-success'>News added successfully.</div>";
    if($_GET['msg']=='updated') echo "<div class='alert alert-success'>News updated successfully.</div>";
    if($_GET['msg']=='status_updated') echo "<div class='alert alert-success'>Status updated successfully.</div>";
}
echo $msg;
?>

<?php if($action == 'list'): ?>
<div class="table-responsive">
    <table class="table table-striped table-sm align-middle">
        <thead>
            <tr>
                <th>ID</th>
                <th>Image</th>
                <th>Title</th>
                <th>Category</th>
                <th>Status</th>
                <th>Featured</th>
                <th>Date</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $sql = "SELECT news.*, categories.category_name FROM news LEFT JOIN categories ON news.category_id = categories.id ORDER BY created_at DESC";
            $result = $conn->query($sql);
            if($result->num_rows > 0):
                while($row = $result->fetch_assoc()):
            ?>
            <tr>
                <td><?php echo $row['id']; ?></td>
                <td>
                    <?php if($row['image']): ?>
                    <img src="../uploads/<?php echo $row['image']; ?>" width="50" height="30" style="object-fit:cover;">
                    <?php else: ?>
                    <span class="text-muted small">No Image</span>
                    <?php endif; ?>
                </td>
                <td><?php echo limit_words($row['title'], 5); ?></td>
                <td><?php echo $row['category_name']; ?></td>
                <td>
                    <?php if($row['status'] == 'published'): ?>
                        <a href="news.php?action=status&id=<?php echo $row['id']; ?>&set=unpublished" class="badge bg-success text-decoration-none">Published</a>
                    <?php else: ?>
                        <a href="news.php?action=status&id=<?php echo $row['id']; ?>&set=published" class="badge bg-warning text-decoration-none text-dark">Unpublished</a>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if($row['is_featured']): ?>
                        <span class="badge bg-info text-dark">Yes</span>
                    <?php else: ?>
                        <span class="badge bg-light text-dark border">No</span>
                    <?php endif; ?>
                </td>
                <td><?php echo date('d M Y', strtotime($row['created_at'])); ?></td>
                <td>
                    <a href="news.php?action=edit&id=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></a>
                    <?php if($_SESSION['role'] == 'admin'): ?>
                    <a href="news.php?action=delete&id=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this news?')"><i class="fas fa-trash"></i></a>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endwhile; else: ?>
            <tr><td colspan="8" class="text-center py-4 text-muted">No news articles found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php elseif($action == 'add' || $action == 'edit'): ?>
<div class="row">
    <div class="col-md-8">
        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label class="form-label fw-bold">News Title</label>
                <input type="text" name="title" class="form-control" required value="<?php echo isset($edit_row)?$edit_row['title']:''; ?>">
            </div>
            
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-bold">Category</label>
                    <select name="category_id" class="form-select" required>
                        <option value="">Select Category</option>
                        <?php
                        $cats = $conn->query("SELECT * FROM categories");
                        while($c = $cats->fetch_assoc()):
                            $selected = (isset($edit_row) && $edit_row['category_id'] == $c['id']) ? 'selected' : '';
                        ?>
                        <option value="<?php echo $c['id']; ?>" <?php echo $selected; ?>><?php echo $c['category_name']; ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-bold">Author Name</label>
                    <input type="text" name="author" class="form-control" required value="<?php echo isset($edit_row)?$edit_row['author']:$_SESSION['user_name']; ?>">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-bold">Publishing Status</label>
                    <select name="status" class="form-select">
                        <option value="published" <?php echo (isset($edit_row) && $edit_row['status']=='published')?'selected':''; ?>>Published</option>
                        <option value="unpublished" <?php echo (isset($edit_row) && $edit_row['status']=='unpublished')?'selected':''; ?>>Unpublished</option>
                    </select>
                </div>
            </div>
            
            <div class="mb-3">
                <label class="form-label fw-bold">Description</label>
                <textarea name="description" class="form-control" rows="10" required><?php echo isset($edit_row)?$edit_row['description']:''; ?></textarea>
            </div>
            
            <div class="mb-3">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="is_featured" value="1" id="isFeatured" <?php echo (isset($edit_row) && $edit_row['is_featured']==1)?'checked':''; ?>>
                    <label class="form-check-label fw-bold" for="isFeatured">Mark as Featured News</label>
                </div>
            </div>
            
            <div class="mb-3">
                <label class="form-label fw-bold">Featured Image</label>
                <input type="file" name="image" class="form-control">
                <?php if(isset($edit_row) && $edit_row['image']): ?>
                <div class="mt-2">
                    <img src="../uploads/<?php echo $edit_row['image']; ?>" width="100" class="img-thumbnail">
                    <small class="d-block text-muted">Current Image</small>
                </div>
                <?php endif; ?>
            </div>
            
            <button type="submit" class="btn btn-primary px-5 py-2 fw-bold"><?php echo $action=='add'?'Publish Now':'Update News'; ?></button>
        </form>
    </div>
</div>
<?php endif; ?>

<?php include 'footer.php'; ?>
