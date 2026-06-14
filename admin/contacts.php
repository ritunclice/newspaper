<?php
include 'header.php';

// Handle Delete Message
if(isset($_GET['delete'])) {
    if($_SESSION['role'] == 'admin') {
        $id = intval($_GET['delete']);
        $conn->query("DELETE FROM contacts WHERE id=$id");
        echo "<script>window.location.href='contacts.php';</script>";
    } else {
        echo "<script>alert('Only admin can delete message'); window.location.href='contacts.php';</script>";
    }
}

// Handle Mark as Read (via URL)
if(isset($_GET['read'])) {
    $id = intval($_GET['read']);
    $conn->query("UPDATE contacts SET status='read' WHERE id=$id");
    echo "<script>window.location.href='contacts.php';</script>";
}

// Handle AJAX Mark as Read
if(isset($_POST['mark_as_read_id'])) {
    $id = intval($_POST['mark_as_read_id']);
    $conn->query("UPDATE contacts SET status='read' WHERE id=$id");
    exit;
}
?>

<div class="card shadow-sm border-0">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-bold text-dark">Contact Messages</h5>
        <span class="badge bg-primary rounded-pill">
            <?php echo $conn->query("SELECT COUNT(*) FROM contacts WHERE status='unread'")->fetch_row()[0]; ?> Unread
        </span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">Date</th>
                        <th>Sender</th>
                        <th>Subject</th>
                        <th>Status</th>
                        <th class="text-end pe-4">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT * FROM contacts ORDER BY created_at DESC";
                    $result = $conn->query($sql);
                    if($result && $result->num_rows > 0):
                        while($row = $result->fetch_assoc()):
                            $is_unread = ($row['status'] == 'unread');
                    ?>
                    <tr class="<?php echo $is_unread ? 'table-light fw-bold' : ''; ?>">
                        <td class="ps-4">
                            <small class="text-muted d-block"><?php echo date('M d, Y', strtotime($row['created_at'])); ?></small>
                            <small class="text-muted"><?php echo date('h:i A', strtotime($row['created_at'])); ?></small>
                        </td>
                        <td>
                            <div class="text-dark"><?php echo htmlspecialchars($row['name']); ?></div>
                            <div class="small text-muted"><?php echo htmlspecialchars($row['email']); ?></div>
                        </td>
                        <td>
                            <div class="text-dark text-truncate" style="max-width: 250px;">
                                <?php echo htmlspecialchars($row['subject']); ?>
                            </div>
                        </td>
                        <td>
                            <?php if($is_unread): ?>
                                <span class="badge bg-warning text-dark">Unread</span>
                            <?php else: ?>
                                <span class="badge bg-light text-muted border">Read</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-end pe-4">
                            <button type="button" class="btn btn-sm btn-light border btn-view-msg" 
                                    data-id="<?php echo $row['id']; ?>"
                                    data-name="<?php echo htmlspecialchars($row['name']); ?>" 
                                    data-email="<?php echo htmlspecialchars($row['email']); ?>"
                                    data-subject="<?php echo htmlspecialchars($row['subject']); ?>"
                                    data-message="<?php echo htmlspecialchars($row['message']); ?>"
                                    data-date="<?php echo date('M d, Y h:i A', strtotime($row['created_at'])); ?>"
                                    data-bs-toggle="modal" data-bs-target="#viewMessageModal">
                                <i class="fas fa-eye text-primary"></i>
                            </button>
                            <?php if($_SESSION['role'] == 'admin'): ?>
                            <a href="contacts.php?delete=<?php echo $row['id']; ?>" class="btn btn-sm btn-light border ms-1" onclick="return confirm('Delete message?')">
                                <i class="fas fa-trash text-danger"></i>
                            </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php 
                        endwhile; 
                    else:
                        echo "<tr><td colspan='5' class='text-center py-5 text-muted'>No messages found in your inbox.</td></tr>";
                    endif;
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- View Message Modal -->
<div class="modal fade" id="viewMessageModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow">
      <div class="modal-header border-bottom-0 pt-4 px-4">
        <h5 class="modal-title fw-bold" id="viewMessageModalLabel">Message Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body px-4">
        <div class="d-flex justify-content-between mb-4">
            <div>
                <small class="text-muted d-block text-uppercase fw-bold" style="font-size: 0.7rem;">From</small>
                <div class="fw-bold text-dark" id="modal-name"></div>
                <div class="small text-muted" id="modal-email"></div>
            </div>
            <div class="text-end">
                <small class="text-muted d-block text-uppercase fw-bold" style="font-size: 0.7rem;">Date</small>
                <div class="small text-dark" id="modal-date"></div>
            </div>
        </div>
        <div class="mb-4">
            <small class="text-muted d-block text-uppercase fw-bold" style="font-size: 0.7rem;">Subject</small>
            <div class="fw-bold text-dark" id="modal-subject"></div>
        </div>
        <div class="p-3 bg-light rounded" id="modal-message" style="white-space: pre-wrap; min-height: 100px;"></div>
      </div>
      <div class="modal-footer border-top-0 pb-4 px-4">
        <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const viewButtons = document.querySelectorAll('.btn-view-msg');
    viewButtons.forEach(button => {
        button.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            document.getElementById('modal-name').textContent = this.getAttribute('data-name');
            document.getElementById('modal-email').textContent = this.getAttribute('data-email');
            document.getElementById('modal-subject').textContent = this.getAttribute('data-subject');
            document.getElementById('modal-message').textContent = this.getAttribute('data-message');
            document.getElementById('modal-date').textContent = this.getAttribute('data-date');

            // Mark as read via AJAX
            fetch('contacts.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'mark_as_read_id=' + id
            });
        });
    });

    // Refresh page on modal close to update UI status
    document.getElementById('viewMessageModal').addEventListener('hidden.bs.modal', function () {
        window.location.reload();
    });
});
</script>

<?php include 'footer.php'; ?>

<?php include 'footer.php'; ?>
