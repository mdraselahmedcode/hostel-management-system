<?php 
    // include('./php_files/guest_only_check_admin.php'); 
    session_start();    
    require_once __DIR__ . '/../config/config.php';
    require_once BASE_PATH . '/admin/includes/header_admin.php';
    require_once BASE_PATH . '/admin/php_files/guest_only_check_admin.php'; 
    
?>


<div class="content container mt-5 d-block">
    <div class="row justify-content-center d-flex align-items-center h-100">
        <div class="col-md-6">

            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Admin Login</h4>
                </div>
                <div class="card-body">
                    <div id="loginAlert" class="alert alert-danger d-none"></div>

                    <form id="adminLoginForm">
                        <div class="mb-3">
                            <label for="email" class="form-label">Email address</label>
                            <input type="email" id="email" name="email" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" id="password" name="password" class="form-control" required>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">Login</button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    $('#adminLoginForm').on('submit', function(e) {
        e.preventDefault();
        const formData = $(this).serialize();

        $.ajax({
            url: 'php_files/login_admin_handler.php',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(res) {
                
                if (res.success) {
                    console.log('Login successful');
                    window.location.href = 'dashboard_admin.php';
                } else {
                    $('#loginAlert').text(res.message).removeClass('d-none');
                }
            },

        });

    });
</script>


    <?php 
        require_once BASE_PATH . '/admin/includes/footer_admin.php'; 
    ?>