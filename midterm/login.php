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
            background-color: #e3f2fd; /* Light blue background */
        }
        .container {
            margin-top: 100px; /* Space from the top */
            background-color: #ffffff; /* White background for the form */
            padding: 30px; /* Inner spacing */
            border-radius: 10px; /* Rounded corners */
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1); /* Shadow effect */
        }
        h3 {
            color: #007bff; 
            font-weight: 600; /* Bold header */
        }
        .btn-primary {
            background-color: #1e88e5; /* Primary button color */
            border: none; /* Remove border */
            transition: background-color 0.3s; /* Smooth transition */
        }
        .btn-primary:hover {
            background-color: #1565c0; /* Darker blue on hover */
        }
        .form-label {
            color: #333;
        }
        .alert {
            margin-top: 20px; /* Space above alerts */
            font-weight: 400; /* Normal font weight for alerts */
        }
        #registrationError {
            margin-top: 10px; /* Space for registration error */
        }
        .modal-header {
            background-color: #e3f2fd; /* Light blue modal header */
        }
        .modal-title {
            color: #007bff; /* Title color */
        }
    </style>
</head>
<body>
<div class="container">
    <div class="row justify-content-center mt-5">
        <div class="col-md-4">
            <h3 class="text-center">LOGIN</h3>
            <?php if ($error) : ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            <form method="post" action="login.php">
                <input type="hidden" name="login" value="1">
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" class="form-control" name="username" placeholder="Username" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" name="password" placeholder="Password" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Login</button>
                <p class="mt-3 text-center">
                    Wa kay account mala ka?<a href="#" data-bs-toggle="modal" data-bs-target="#registerModal">pag himo oy animal</a>
                </p>
            </form>
        </div>
    </div>
</div>

<!-- Registration Modal -->
<div class="modal fade" id="registerModal" tabindex="-1" aria-labelledby="registerModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="registerModalLabel">PAGHIMO DIRI NIMALA KA</h5>
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
                            <option value="" disabled selected>UNSAY COLOR NIMO</option>
                            <option value="male">YOYOT</option>
                            <option value="female">MAGTUTUDLO</option>
                            <option value="female">KIKI</option>
                            <option value="female">EBAY</option>
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
                        <label for="role" class="form-label">Role</label>
                        <select class="form-select" name="role" required>
                            <option value="" disabled selected>Select Role</option>
                            <option value="customer">Customer</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">SGE DAYUN </button>
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