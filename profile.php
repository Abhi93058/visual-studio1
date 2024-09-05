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
$uploadError = '';
$updateMessage = '';

// Fetch current user details
$stmt = $conn->prepare("SELECT * FROM test WHERE name = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
} else {
    echo "User not found.";
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $newUsername = $_POST['username'];
    $email = $_POST['email'];
    $imageName = $user['image_path']; // Default to the existing image name
    
    // Handle image upload
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
        $image = $_FILES['profile_image'];
        $imageName = time() . '_' . basename($image['name']);
        $imagePath = 'uploads/' . $imageName;
        
        if (move_uploaded_file($image['tmp_name'], $imagePath)) {
            // Delete old image if exists
            if (!empty($user['image']) && file_exists('uploads/' . $user['image'])) {
                unlink('uploads/' . $user['image']);
            }
        } else {
            $uploadError = "Failed to upload image.";
        }
    }
    
    // Update user information in the database
    $stmt = $conn->prepare("UPDATE test SET name = ?, email = ?, image_path = ? WHERE id = ?");
    $stmt->bind_param("sssi", $newUsername, $email, $imageName, $user['id']);

    if ($stmt->execute()) {
        $_SESSION['username'] = $newUsername; // Update session username if changed
        $updateMessage = "Profile updated successfully!";
    } else {
        $updateMessage = "Failed to update profile.";
    }
    
    $stmt->close();
}

$conn->close();
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
        .profile-image {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
        }
        .profile-image img {
            border-radius: 50%;
            width: 150px;
            height: 150px;
            object-fit: cover;
        }
    </style>
</head>
<body>

<div class="container">
    <h2 class="text-center">Update Profile</h2>

    <?php if (!empty($updateMessage)): ?>
        <div class="alert alert-info text-center"><?php echo $updateMessage; ?></div>
    <?php endif; ?>

    <form action="profile.php" method="POST" enctype="multipart/form-data"> <!-- Corrected action -->
        <div class="form-group">
            <label for="username">Username:</label>
            <input type="text" name="username" id="username" class="form-control" value="<?php echo htmlspecialchars($user['name']); ?>" required>
        </div>
        
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" name="email" id="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" required>
        </div>

        <div class="form-group profile-image">
            <?php if (!empty($user['image'])): ?>
                <img src="uploads/<?php echo $user['image']; ?>" alt="Profile Image">
            <?php else: ?>
                <img src="path/to/default-avatar.png" alt="Default Profile Image">
            <?php endif; ?>
        </div>
        
        <div class="form-group">
            <label for="profile_image">Profile Image:</label>
            <input type="file" name="profile_image" id="profile_image" class="form-control-file">
        </div>
        
        <?php if (!empty($uploadError)): ?>
            <div class="alert alert-danger"><?php echo $uploadError; ?></div>
        <?php endif; ?>
        
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
