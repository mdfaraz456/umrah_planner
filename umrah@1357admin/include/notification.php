<?php
// Start session if not already started

// Success message
if (isset($_SESSION['msg'])) {
    ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert" style="transition: opacity 0.5s;">
        <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="me-2">
            <polyline points="9 11 12 14 22 4"></polyline>
            <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path>
        </svg>
        <strong>Success!</strong> <?php echo htmlspecialchars($_SESSION['msg']); ?>
        <button type="button" class="btn-close float-end" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <script>
        setTimeout(() => {
            const alert = document.querySelector('.alert-success');
            if (alert) {
                alert.classList.remove('show');
                alert.style.opacity = '0';
                setTimeout(() => alert.style.display = 'none', 500);
            }
        }, 5000);
    </script>
    <?php
    unset($_SESSION['msg']);
}

// Error message
if (isset($_SESSION['errmsg'])) {
    ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert" style="transition: opacity 0.5s;">
        <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="me-2">
            <polygon points="7.86 2 16.14 2 22 7.86 22 16.14 16.14 22 7.86 22 2 16.14 2 7.86 7.86 2"></polygon>
            <line x1="15" y1="9" x2="9" y2="15"></line>
            <line x1="9" y1="9" x2="15" y2="15"></line>
        </svg>
        <strong>Error!</strong> <?php echo htmlspecialchars($_SESSION['errmsg']); ?>
        <button type="button" class="btn-close float-end" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <script>
        setTimeout(() => {
            const alert = document.querySelector('.alert-danger');
            if (alert) {
                alert.classList.remove('show');
                alert.style.opacity = '0';
                setTimeout(() => alert.style.display = 'none', 500);
            }
        }, 5000);
    </script>
    <?php
    unset($_SESSION['errmsg']);
}
?>
