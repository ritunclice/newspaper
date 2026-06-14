<footer class="mt-5">
    <div class="container">
        <div class="row">
            <div class="col-md-4 mb-4">
                <h5 class="mb-3 text-white">About Us</h5>
                <p class="text-secondary">We provide the latest news from around the world. Trusted by millions of readers.</p>
                <div class="social-links mt-3">
                    <a href="#" class="me-3"><i class="fab fa-facebook-f fa-lg"></i></a>
                    <a href="#" class="me-3"><i class="fab fa-twitter fa-lg"></i></a>
                    <a href="#" class="me-3"><i class="fab fa-instagram fa-lg"></i></a>
                    <a href="#" class="me-3"><i class="fab fa-linkedin fa-lg"></i></a>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <h5 class="mb-3 text-white">Quick Links</h5>
                <ul class="list-unstyled">
                    <li><a href="<?php echo base_url('about.php'); ?>">About Us</a></li>
                    <li><a href="<?php echo base_url('contact.php'); ?>">Contact Us</a></li>           
                </ul>
            </div>
            <div class="col-md-4 mb-4">
                <h5 class="mb-3 text-white">Subscribe</h5>
                <p class="text-secondary">Get the latest updates directly in your inbox.</p>
                <form action="#" class="d-flex">
                    <input type="email" class="form-control me-2" placeholder="Enter email">
                    <button class="btn btn-primary">Subscribe</button>
                </form>
            </div>
        </div>
        <hr class="bg-secondary">
        <div class="text-center text-secondary py-2">
            &copy; <?php echo date('Y'); ?> NewsPortal. All Rights Reserved.
        </div>
    </div>
</footer>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- Custom JS -->
<script src="<?php echo base_url('js/main.js'); ?>"></script>
<script>
    // Initialize Dark Mode Switch logic binding
    const switchInput = document.getElementById('darkModeSwitch');
    if(switchInput) {
        // Check localStorage
        if(localStorage.getItem('theme') === 'dark') {
            switchInput.checked = true;
        }
        switchInput.addEventListener('change', function(e) {
            if(e.target.checked) {
                document.documentElement.setAttribute('data-theme', 'dark');
                localStorage.setItem('theme', 'dark');
            } else {
                document.documentElement.setAttribute('data-theme', 'light');
                localStorage.setItem('theme', 'light');
            }
        });
    }
</script>
</body>
</html>
