<?php
session_start();

include 'header.php';
include 'sidebar.php';
include('config/db_connect.php');

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

$username = $_SESSION['username'];
$updateMessage = '';

// Fetch current user details including the password
$query = "SELECT id, name, email, password FROM test WHERE name = '$username'";
$result = mysqli_query($conn, $query);

if ($result && mysqli_num_rows($result) > 0) {
    $user = mysqli_fetch_assoc($result);
} else {
    echo "User not found.";
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $newUsername = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    // Retain the existing password if a new one is not provided
    $hashedPassword = !empty($password) ? $password : $user['password']; 

    $updateQuery = "UPDATE test SET name = '$newUsername', email = '$email', password = '$hashedPassword' WHERE id = ".$user['id'];
    
    if (mysqli_query($conn, $updateQuery)) {
        $_SESSION['username'] = $newUsername; 
        $updateMessage = "Profile updated successfully!";
    } else {
        $updateMessage = "Failed to update profile.";
    }
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Profile</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .container {
            max-width: 600px;
            margin-top: 50px;
        }
    </style>
</head>
<body>

<div class="container">
    <h2 class="text-center">Update Profile</h2>

    <?php if (!empty($updateMessage)): ?>
        <div class="alert alert-info text-center"><?php echo $updateMessage; ?></div>
    <?php endif; ?>

    <form action="profile.php" method="POST">
        <div class="form-group">
            <label for="username">Username:</label>
            <input type="text" name="username" id="username" class="form-control" value="<?php echo htmlspecialchars($user['name']); ?>" required>
        </div>
        
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" name="email" id="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" required>
        </div>
        
        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" name="password" id="password" class="form-control" placeholder="Enter new password (leave empty if not changing)">
        </div>

        <button type="submit" class="btn btn-primary btn-block">Update Profile</button>
    </form>
</div>

<!-- Bootstrap JS and dependencies -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.6.0/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
<?php
include 'footer.php';
?>
