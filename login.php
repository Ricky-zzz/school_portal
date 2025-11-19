<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootswatch@5.3.8/dist/zephyr/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="col-md-4">
            <div class="card shadow">
                <div class="card-body">
                    <h3 class="card-title text-center mb-4">Login</h3>
                    
                    <?php if (!empty($_SESSION["error"])): ?>
                        <div class="alert alert-danger">
                            <?= htmlspecialchars($_SESSION["error"]) ?>
                        </div>
                        <?php unset($_SESSION["error"]); ?>
                    <?php endif; ?>

                    <form action="sql/login_actions.php" method="POST">
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" id="username" name="username" class="form-control" autofocus required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" id="password" name="password" class="form-control" required>
                        </div>
                        
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">Login</button>
                        </div>
                    </form>
                </div>
                <div class="d-flex  justify-content-between">
                <a href="index.php" class="p-2 mx-3"><small>Student Login</small></a>
                <a href="login2.php" class="p-2 mx-3"><small>Teacher Login</small></a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>