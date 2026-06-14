<?php
include 'header.php';

// Protection: Only admin can see and clear logs
if($_SESSION['role'] !== 'admin') {
    echo "<script>window.location.href='dashboard.php';</script>";
    exit();
}

// Handle Clear All
if(isset($_POST['clear_all'])) {
    $conn->query("DELETE FROM activity_logs");
    echo "<script>window.location.href='logs.php?msg=all_cleared';</script>";
}

// Handle Clear Single
if(isset($_GET['clear_id'])) {
    $id = intval($_GET['clear_id']);
    $conn->query("DELETE FROM activity_logs WHERE id=$id");
    echo "<script>window.location.href='logs.php?msg=cleared';</script>";
}

// Mark all as seen
$conn->query("UPDATE activity_logs SET is_seen = 1 WHERE is_seen = 0");
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0 fw-bold text-dark"><i class="fas fa-list-ul me-2"></i> Editor Activity Logs</h4>
    <form method="POST" onsubmit="return confirm('Are you sure you want to clear ALL activity logs? This cannot be undone.');">
        <button type="submit" name="clear_all" class="btn btn-sm btn-outline-danger">
            <i class="fas fa-trash-alt me-1"></i> Clear All Logs
        </button>
    </form>
</div>

<?php 
if(isset($_GET['msg'])) {
    if($_GET['msg']=='all_cleared') echo "<div class='alert alert-success py-2 small'>All activity logs have been cleared.</div>";
    if($_GET['msg']=='cleared') echo "<div class='alert alert-success py-2 small'>Activity log entry removed.</div>";
}
?>

<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">Date & Time</th>
                        <th>Editor Name</th>
                        <th>Action</th>
                        <th>Details</th>
                        <th>Review</th>
                        <th class="text-end pe-4">Manage</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT * FROM activity_logs ORDER BY created_at DESC";
                    $result = $conn->query($sql);
                    if($result && $result->num_rows > 0):
                        while($row = $result->fetch_assoc()):
                            $badge_class = 'bg-secondary';
                            if($row['action'] == 'Creation') $badge_class = 'bg-success';
                            if($row['action'] == 'Deletion') $badge_class = 'bg-danger';
                            if($row['action'] == 'Status Change') $badge_class = 'bg-info';
                            if($row['action'] == 'Modification') $badge_class = 'bg-primary';
                    ?>
                    <tr>
                        <td class="ps-4 text-muted small">
                            <?php echo date('M d, Y', strtotime($row['created_at'])); ?><br>
                            <?php echo date('h:i A', strtotime($row['created_at'])); ?>
                        </td>
                        <td class="fw-bold"><?php echo htmlspecialchars($row['user_name']); ?></td>
                        <td><span class="badge <?php echo $badge_class; ?>"><?php echo $row['action']; ?></span></td>
                        <td class="text-muted small"><?php echo htmlspecialchars($row['details']); ?></td>
                        <td>
                            <?php if($row['news_id'] > 0 && $row['action'] !== 'Deletion'): ?>
                                <a href="../news.php?id=<?php echo $row['news_id']; ?>" target="_blank" class="text-primary text-decoration-none small fw-bold me-2">
                                    <i class="fas fa-external-link-alt"></i> View
                                </a>
                                <a href="news.php?action=edit&id=<?php echo $row['news_id']; ?>" class="text-success text-decoration-none small fw-bold">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                            <?php else: ?>
                                <span class="text-muted small">-</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-end pe-4">
                            <a href="logs.php?clear_id=<?php echo $row['id']; ?>" class="btn btn-sm btn-light border" onclick="return confirm('Clear this log entry?')" title="Clear Entry">
                                <i class="fas fa-times text-danger"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; else: ?>
                    <tr><td colspan="6" class="text-center py-5 text-muted">No activity recorded yet.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
