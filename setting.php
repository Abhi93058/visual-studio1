<?php
//session_start();
include 'header.php';
include 'sidebar.php';
include 'config/db_connect.php';

// Assume the admin is logged in and we have the admin's ID in the session
//$id = $_SESSION['id'];
// Initialize variables
$name = "";
$image_path = "";
$error = "";

// Fetch current admin data
$query = "SELECT name, image_path FROM test WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->bind_result($name, $image_path);
$stmt->fetch();
$stmt->close();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and validate inputs
    $name = htmlspecialchars(trim($_POST['name']));

    // Check if a new image is uploaded
    if (!empty($_FILES['image_path']['name'])) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["image_path"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Check if the file is an actual image
        $check = getimagesize($_FILES["image_path"]["tmp_name"]);
        if ($check === false) {
            $error = "File is not an image.";
        }

        // Check file size (limit to 2MB)
        if ($_FILES["image_path"]["size"] > 2097152) {
            $error = "Sorry, your file is too large.";
        }

        // Allow certain file formats
        if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg") {
            $error = "Sorry, only JPG, JPEG, & PNG files are allowed.";
        }

        // Check if there were no errors
        if (empty($error)) {
            // Upload the file
            if (move_uploaded_file($_FILES["image_path"]["tmp_name"], $target_file)) {
                // If the upload was successful, update the profile_image path
                $image_path = $target_file;
            } else {
                $error = "Sorry, there was an error uploading your file.";
            }
        }
    }

    if (empty($error)) {
        // Update the admin profile in the database
        $query = "UPDATE test SET name = ?, image_path = ? WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssi", $name, $image_path, $id);
        if ($stmt->execute()) {
            echo "<script>alert('Profile updated successfully!');</script>";
        } else {
            $error = "Error updating profile: " . $stmt->error;
        }
        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Profile</title>
</head>
<body>

<h2>Update Profile</h2>

<?php if (!empty($error)): ?>
    <div style="color: red;"><?php echo $error; ?></div>
<?php endif; ?>

<form action="" method="POST" enctype="multipart/form-data">
    <div>
        <label for="name">Name:</label>
        <input type="text" name="name" value="<?php echo htmlspecialchars($name); ?>" required>
    </div>
    <div>
        <label for="image_path">Profile Image:</label>
        <input type="file" name="image_path">
        <?php if (!empty($image_path)): ?>
            <img src="<?php echo htmlspecialchars($image_path); ?>" alt="Profile Image" style="width: 100px; height: 100px; display: block; margin-top: 10px;">
        <?php endif; ?>
    </div>
    <div>
        <button type="submit">Update Profile</button>
    </div>
</form>

</body>
</html>
<?php
include('footer.php');
?>