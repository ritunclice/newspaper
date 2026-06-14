<?php
include 'header.php';

// Protection: Only admin can access this page
if($_SESSION['role'] !== 'admin') {
    echo "<script>window.location.href='dashboard.php';</script>";
    exit();
}

// Handle Add Ad
if(isset($_POST['add_ad'])) {
    $type = $conn->real_escape_string($_POST['type']);
    $code = $conn->real_escape_string($_POST['code']);
    
    $sql = "INSERT INTO ads (type, code, status) VALUES ('$type', '$code', 1)";
    if($conn->query($sql)) {
        echo "<script>window.location.href='ads.php?msg=added';</script>";
    }
}

// Handle Delete Ad
if(isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM ads WHERE id=$id");
    echo "<script>window.location.href='ads.php?msg=deleted';</script>";
}
?>

<div class="row">
    <div class="col-md-4">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0">Add New Advertisement</h5>
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Ad Position</label>
                        <select name="type" class="form-select">
                            <option value="sidebar">Sidebar (300x250)</option>
                            <option value="content">Content Banner (728x90)</option>
                            <option value="header">Header Top</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Ad Code (HTML/JS)</label>
                        <textarea name="code" class="form-control" rows="5" required placeholder="Paste Google AdSense code here..."></textarea>
                    </div>
                    <button type="submit" name="add_ad" class="btn btn-primary w-100">Add Advertisement</button>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0">Manage Ads</h5>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Position</th>
                            <th>Code Preview</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql = "SELECT * FROM ads ORDER BY created_at DESC";
                        $result = $conn->query($sql);
                        while($row = $result->fetch_assoc()):
                        ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><span class="badge bg-secondary"><?php echo ucfirst($row['type']); ?></span></td>
                            <td><small class="text-muted"><?php echo htmlspecialchars(substr($row['code'], 0, 50)); ?>...</small></td>
                            <td>
                                <a href="ads.php?delete=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete this ad?')"><i class="fas fa-trash"></i></a>
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
