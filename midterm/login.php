<?php
session_start();
include 'connection.php'; // Include your database connection

$connection = new Connection();
$pdo = $connection->OpenConnection();

// Initialize error and success message variables
$error = '';
$successMessage = '';
$registrationError = '';

// Check if the login form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Fetch user from the database
    $stmt = $pdo->prepare("SELECT * FROM register WHERE username = :username LIMIT 1");
    $stmt->bindParam(':username', $username);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // Check the password directly since you are not hashing it
        if ($password === $user['password']) {
            // Start the session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];

            header("Location: index.php"); // Redirect to dashboard or any protected page
            exit();
        } else {
            $error = "Invalid password.";
        }
    } else {
        $error = "User not found.";
    }
}

// Handle registration via AJAX
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['register'])) {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $address = $_POST['address'];
    $birthdate = $_POST['birthdate'];
    $gender = $_POST['gender'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $rolede = $_POST['role'];

    // Check if the username already exists
    $stmt = $pdo->prepare("SELECT * FROM register WHERE username = :username LIMIT 1");
    $stmt->bindParam(':username', $username);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // Username already taken
        $registrationError = "TANGA NANAY TAG IYA ANA";
    } else {
        // Insert new user into the database
        $stmt = $pdo->prepare("INSERT INTO register (first_name, last_name, address, birthdate, gender, username, password, rolede, date_created) VALUES (:first_name, :last_name, :address, :birthdate, :gender, :username, :password, :rolede, NOW())");
        $stmt->bindParam(':first_name', $first_name);
        $stmt->bindParam(':last_name', $last_name);
        $stmt->bindParam(':address', $address);
        $stmt->bindParam(':birthdate', $birthdate);
        $stmt->bindParam(':gender', $gender);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password', $password);
        $stmt->bindParam(':rolede', $rolede);
        
        if ($stmt->execute()) {
            // Registration successful
            $successMessage = "Registered successfully!";
        } else {
            $registrationError = "Registration failed. Please try again.";
        }
    }

    // Return response as JSON
    echo json_encode(['success' => $successMessage, 'error' => $registrationError]);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            color: #ffffff;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;
            background: linear-gradient(270deg, #73a5ff, #5476b3, #ff8a73, #e3f2fd);
            background-size: 800% 800%;
            animation: gradientAnimation 15s ease infinite;
        }

        /* Gradient background animation */
        @keyframes gradientAnimation {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .container {
            background-color: #ffffff;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            color: #333;
            animation: fadeInUp 1.2s ease;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(50px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        h3 {
            color: #007bff;
            font-weight: 600;
            text-align: center;
            margin-bottom: 20px;
        }

        .btn-primary {
            background-color: #007bff;
            border: none;
            transition: all 0.3s;
        }
        .btn-primary:hover {
            background-color: #0056b3;
            transform: scale(1.05);
        }

        .form-control, .form-select {
            transition: all 0.3s;
            border-radius: 8px;
        }
        .form-control:focus, .form-select:focus {
            box-shadow: 0 0 10px rgba(0, 123, 255, 0.5);
            border-color: #007bff;
        }

        .modal-header, .modal-content {
            background-color: #f0f4f8;
            border: none;
        }
        .modal-title {
            color: #007bff;
        }
        .alert {
            font-weight: 400;
        }

        #registrationError {
            color: #e74c3c;
        }
        .form-label {
            color: #555;
        }

        .link-animation {
            color: #007bff;
            text-decoration: none;
            position: relative;
            transition: color 0.3s;
        }
        .link-animation:hover {
            color: #0056b3;
        }
        .link-animation::after {
            content: "";
            width: 100%;
            height: 2px;
            background-color: #007bff;
            position: absolute;
            left: 0;
            bottom: -2px;
            transition: transform 0.3s;
            transform: scaleX(0);
            transform-origin: right;
        }
        .link-animation:hover::after {
            transform: scaleX(1);
            transform-origin: left;
        }
    </style>
</head>
<body>
<div class="container">
    <h3 class="text-center">LOGIN</h3>
    <?php if ($error) : ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    <form method="post" action="login.php">
        <input type="hidden" name="login" value="1">
        <div class="mb-3">
            <label for="username" class="form-label">Username</label>
            <input type="text" class="form-control" name="username" placeholder="Enter your username" required>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control" name="password" placeholder="Enter your password" required>
        </div>
        <button type="submit" class="btn btn-primary w-100">Login</button>
        <p class="mt-3 text-center">
            Don't have an account? <a href="#" class="link-animation" data-bs-toggle="modal" data-bs-target="#registerModal">Click here</a>
        </p>
    </form>
</div>

<!-- Registration Modal -->
<div class="modal fade" id="registerModal" tabindex="-1" aria-labelledby="registerModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="registerModalLabel">Create Account</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="registrationForm">
                    <div class="mb-3">
                        <label for="first_name" class="form-label">First Name</label>
                        <input type="text" class="form-control" name="first_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="last_name" class="form-label">Last Name</label>
                        <input type="text" class="form-control" name="last_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="address" class="form-label">Address</label>
                        <input type="text" class="form-control" name="address" required>
                    </div>
                    <div class="mb-3">
                        <label for="birthdate" class="form-label">Birthdate</label>
                        <input type="date" class="form-control" name="birthdate" required>
                    </div>
                    <div class="mb-3">
                        <label for="gender" class="form-label">Gender</label>
                        <select class="form-select" name="gender" required>
                            <option value="" disabled selected></option>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" name="username" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" name="password" required>
                    </div>
                    <div class="mb-3">
                        <label for="role" class="form-label">Role</label>
                        <select class="form-select" name="role" required>
                            <option value="" disabled selected>Select Role</option>
                            <option value="customer">Customer</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Register</button>
                    <div id="registrationError" class="text-danger mt-3"></div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
$(document).ready(function() {
    $('#registrationForm').on('submit', function(e) {
        e.preventDefault(); // Prevent the default form submission

        $.ajax({
            type: 'POST',
            url: 'login.php',
            data: $(this).serialize() + '&register=1', // Add register flag
            dataType: 'json',
            success: function(response) {
                if (response.error) {
                    $('#registrationError').text(response.error);
                } else if (response.success) {
                    alert(response.success);
                    $('#registerModal').modal('hide');
                }
            },
            error: function() {
                $('#registrationError').text('An error occurred. Please try again.');
            }
        });
    });
});
</script>
</body>
</html>
