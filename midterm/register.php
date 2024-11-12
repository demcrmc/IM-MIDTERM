<?php
session_start();
include 'connection.php'; // Include your database connection

// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$error = "";
$success = "";

try {
    $connection = new Connection();
    $pdo = $connection->OpenConnection();
} catch (PDOException $e) {
    die("Database connection error: " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve form inputs and sanitize them
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $address = trim($_POST['address']);
    $birthdate = $_POST['birthdate'];
    $gender = $_POST['gender'];
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $role = $_POST['rolede'];

    // Validate that all required fields are filled in
    if (empty($first_name) || empty($last_name) || empty($address) || empty($birthdate) || empty($gender) || empty($username) || empty($password) || empty($role)) {
        $error = "Please fill in all fields.";
    } else {
        // Hash the password for security
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        // Prepare SQL query to insert user into database
        $sql = "INSERT INTO users (first_name, last_name, address, birthdate, gender, username, password, role) VALUES (:first_name, :last_name, :address, :birthdate, :gender, :username, :password, :role)";
        $stmt = $pdo->prepare($sql);

        // Bind parameters and execute query
        try {
            $stmt->execute([
                ':first_name' => $first_name,
                ':last_name' => $last_name,
                ':address' => $address,
                ':birthdate' => $birthdate,
                ':gender' => $gender,
                ':username' => $username,
                ':password' => $hashed_password,
                ':role' => $role
            ]);

            $success = "User registered successfully!";
        } catch (PDOException $e) {
            // Display more specific error messages for debugging
            if ($e->getCode() == 23000) {
                $error = "Username already exists. Please choose another.";
            } else {
                $error = "Database error: " . $e->getMessage(); // Detailed error for debugging
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container">
    <div class="row justify-content-center mt-5">
        <div class="col-md-4">
            <h3 class="text-center">Register</h3>
            <?php if ($error) : ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            <?php if ($success) : ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            <form method="post" action="register.php">
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
                        <option value="" disabled selected>Select Gender</option>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                        <option value="other">Other</option>
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
                    <label for="rolede" class="form-label">Role</label>
                    <select class="form-select" name="rolede" required>
                        <option value="" disabled selected>Select Role</option>
                        <option value="customer">Customer</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Register</button>
            </form>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
