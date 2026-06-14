<?php
include 'header.php';

// Handle Delete Comment
if(isset($_GET['delete'])) {
    if($_SESSION['role'] == 'admin') {
        $id = intval($_GET['delete']);
        $conn->query("DELETE FROM comments WHERE id=$id");
        echo "<script>window.location.href='comments.php?msg=deleted';</script>";
    } else {
        echo "<script>alert('Only admin can delete comment'); window.location.href='comments.php';</script>";
    }
}
?>

<div class="row">
    <div class="col-md-12">
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0">Manage Comments</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Author</th>
                                <th>Comment</th>
                                <th>News Post</th>
                                <th>Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sql = "SELECT comments.*, news.title FROM comments LEFT JOIN news ON comments.news_id = news.id ORDER BY created_at DESC";
                            $result = $conn->query($sql);
                            if($result->num_rows > 0):
                                while($row = $result->fetch_assoc()):
                            ?>
                            <tr>
                                <td><?php echo $row['id']; ?></td>
                                <td><?php echo $row['name']; ?></td>
                                <td><?php echo limit_words($row['comment'], 10); ?></td>
                                <td><a href="../news.php?id=<?php echo $row['news_id']; ?>" target="_blank"><?php echo limit_words($row['title'], 5); ?></a></td>
                                <td><?php echo date('M d, Y', strtotime($row['created_at'])); ?></td>
                                <td>
                                    <?php if($_SESSION['role'] == 'admin'): ?>
                                    <a href="comments.php?delete=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')"><i class="fas fa-trash"></i></a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endwhile; else: ?>
                            <tr><td colspan="6" class="text-center">No comments found.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
