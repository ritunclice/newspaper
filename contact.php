<?php
include 'header.php';

$msg = '';
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    $subject = $conn->real_escape_string($_POST['subject']);
    $message = $conn->real_escape_string($_POST['message']);

    // Auto-create contacts table if it doesn't exist
    $table_check = "CREATE TABLE IF NOT EXISTS `contacts` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `name` varchar(255) NOT NULL,
        `email` varchar(255) NOT NULL,
        `subject` varchar(255) NOT NULL,
        `message` text NOT NULL,
        `status` enum('unread','read') DEFAULT 'unread',
        `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
    $conn->query($table_check);

    $sql = "INSERT INTO contacts (name, email, subject, message) VALUES ('$name', '$email', '$subject', '$message')";
    if($conn->query($sql)) {
        $msg = '<div class="alert alert-success">Thank you for contacting us! We will get back to you soon.</div>';
    } else {
        $msg = '<div class="alert alert-danger">Something went wrong. Please try again.</div>';
    }
}
?>

<div class="container my-5">
    <div class="row">
        <div class="col-lg-8">
            <h2 class="mb-4">Contact Us</h2>
            <?php echo $msg; ?>
            <form action="" method="POST">
                <div class="mb-3">
                    <label for="name" class="form-label">Full Name</label>
                    <input type="text" class="form-control" id="name" name="name" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email Address</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                <div class="mb-3">
                    <label for="subject" class="form-label">Subject</label>
                    <input type="text" class="form-control" id="subject" name="subject" required>
                </div>
                <div class="mb-3">
                    <label for="message" class="form-label">Message</label>
                    <textarea class="form-control" id="message" name="message" rows="5" required></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Send Message</button>
            </form>
        </div>
        <div class="col-lg-4">
            <h4 class="mb-4">Our Location</h4>
            <!-- Mock Map -->
            <div class="bg-secondary bg-opacity-10 d-flex justify-content-center align-items-center mb-4" style="height: 300px;">
                <span class="text-muted"><i class="fas fa-map-marked-alt fa-3x"></i><br>Google Map Placeholder</span>
            </div>
            
            <h5>News Portal Inc.</h5>
            <p>123 News Street, Media City<br>
            New York, NY 10001<br>
            United States</p>
            
            <p><strong>Email:</strong> info@newsportal.com<br>
            <strong>Phone:</strong> +1 (555) 123-4567</p>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
