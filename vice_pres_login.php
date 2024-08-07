<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IDIS Authentication</title>
    <link rel="stylesheet" href="style2.css">
    <style>
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid transparent;
            border-radius: 4px;
            color: #721c24;
            background-color: #f8d7da;
            border-color: #f5c6cb;
        }
    </style>
</head>
<body>
    <header>
        <h1>Login as:</h1>
    </header>
    <main>
        <div class="card">
            <div class="title"><p>VICE-PRESIDENT</p></div>
            <div class="content">
                <?php if (!empty($error_message)): ?>
                    <div class="alert" id="alert"><?php echo htmlspecialchars($error_message); ?></div>
                    <script>
                        setTimeout(function() {
                            var alertElement = document.getElementById('alert');
                            if (alertElement) {
                                alertElement.style.display = 'none';
                            }
                        }, 5000);
                    </script>
                <?php endif; ?>
                <form action="login.php" method="post">
                    <input type="number" name="id" placeholder="ID" required>
                    <br>
                    <input type="password" name="password" placeholder="Password" required>
                    <input type="hidden" name="user_type" value="vp">
                    <br>
                    <button type="submit" class="login">LOGIN</button>
                </form>
            </div>
        </div>
    </main>
</body>
</html>
