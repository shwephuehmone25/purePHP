<?php
require '../database/database.php';

$message = '';
$username = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);
    $role = $_POST["role"] ?? 'user';

    if (empty($username)) {
        $message = "Please enter a username.";
    } elseif (empty($_POST["password"])) {
        $message = "Please enter a password.";
    } else {
        $sql = "SELECT id FROM users WHERE username = ?";

        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("s", $param_username);
            $param_username = $username;

            if ($stmt->execute()) {
                $stmt->store_result();

                if ($stmt->num_rows == 1) {
                    $message = "This username is already taken.";
                } else {
                    $sql = "INSERT INTO users (username, password, role) VALUES (?, ?, ?)";

                    if ($stmt = $conn->prepare($sql)) {
                        $stmt->bind_param("sss", $param_username, $param_password, $param_role);
                        $param_username = $username;
                        $param_password = $password;
                        $param_role = $role;

                        if ($stmt->execute()) {
                            header("Location: login.php");
                            exit;
                        } else {
                            $message = "Something went wrong. Please try again later.";
                        }
                    }
                }
            } else {
                $message = "Oops! Something went wrong. Please try again later.";
            }

            $stmt->close();
        }
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Register - Logistica</title>
    <link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #17a2b8;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .register-box {
            background-color: #ffffff;
            padding: 25px;
            border-radius: 5px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 450px;
        }

        .form-control {
            border-radius: 5px;
        }

        .btn-md {
            width: 100%;
            padding: 10px;
        }

        .text-info {
            color: #17a2b8 !important;
        }
    </style>
</head>

<body>
    <div class="register-box">
        <h3 class="text-center text-info">Register</h3>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
                <label for="username" class="text-info">Username:</label>
                <input type="text" name="username" id="username" class="form-control" value="<?php echo $username; ?>" required pattern="[A-Za-z]+" title="Username must only contain letters. No numbers allowed.">
                <div class="invalid-feedback">
                    Username must only contain letters.
                </div>
            </div>

            <div class="form-group">
                <label for="password" class="text-info">Password:</label>
                <input type="password" name="password" id="password" class="form-control" required>
                <small id="passwordHelp" class="form-text text-warning">*Password must be at least 8 characters long.</small>
                <div class="error-message text-danger" style="display: none;">*Password must be at least 8 characters long.</div>
            </div>

            <div class="form-group">
                <label for="role" class="text-info">Role:</label>
                <select name="role" class="form-control">
                    <option value="user">User</option>
                    <option value="admin">Admin</option>
                </select>
            </div>

            <div class="form-group">
                <input type="submit" name="submit" class="btn btn-info btn-md" value="Register">
            </div>

            <div class="text-right">
                <a href="login.php" class="text-info">Already have an account?</a>
            </div>
            <p class="text-danger text-center"><?php echo $message; ?></p>
        </form>
    </div>
</body>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<script>
    document.getElementById('password').oninput = function() {
        var password = document.getElementById('password');
        var errorMessage = document.querySelector('.error-message');
        if (password.value.length < 8) {
            errorMessage.style.display = 'block';
        } else {
            errorMessage.style.display = 'none';
        }
    };

    //validation for name
    document.addEventListener("DOMContentLoaded", function() {
        var form = document.querySelector("form");
        var username = document.getElementById('username');
        var password = document.getElementById('password');
        var errorMessagePassword = document.querySelector('.error-message');
        var errorMessageUsername = document.createElement('div');
        errorMessageUsername.className = 'text-danger';
        errorMessageUsername.style.display = 'none';
        errorMessageUsername.textContent = 'Username must only contain letters.';
        username.parentNode.insertBefore(errorMessageUsername, username.nextSibling);

        form.onsubmit = function() {
            // Validate password
            if (password.value.length < 8) {
                errorMessagePassword.style.display = 'block';
                return false;
            }
            // Validate username
            if (!/^[A-Za-z]+$/.test(username.value)) {
                errorMessageUsername.style.display = 'block';
                return false;
            }
        };

        username.oninput = function() {
            if (/^[A-Za-z]+$/.test(username.value)) {
                errorMessageUsername.style.display = 'none';
            } else {
                errorMessageUsername.style.display = 'block';
            }
        };

        password.oninput = function() {
        var passwordText = password.value;
        if (passwordText.length >= 8 && /[A-Z]/.test(passwordText) && /[!@#$%^&*(),.?":{}|<>]/.test(passwordText)) {
            errorMessagePassword.style.display = 'none';
        } else {
            errorMessagePassword.textContent = 'Password must be at least 8 characters long, include at least one uppercase letter, and one special character.';
            errorMessagePassword.style.display = 'block';
        }
        };
    });
</script>

</html>