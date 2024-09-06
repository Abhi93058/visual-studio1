<?php
session_start();

include 'header.php';
include 'sidebar.php';
include('config/db_connect.php');

// Fetch current admin details
$username = $_SESSION['username'];
$query = "SELECT * FROM test WHERE name = '$username'";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_FILES['image_path']) && $_FILES['image_path']['error'] == UPLOAD_ERR_OK) {
        $image = $_FILES['image_path'];
        $imageName = time() . '_' . basename($image['name']);
        $imagePath = 'uploads/' . $imageName;

        // Move the uploaded image to the uploads directory
        if (move_uploaded_file($image['tmp_name'], $imagePath)) {
            // Delete old image if it exists
            if (!empty($user['image_path']) && file_exists('uploads/' . $user['image_path'])) {
                unlink('uploads/' . $user['image_path']);
            }

            // Update the database with the new image path
            $updateQuery = "UPDATE test SET image_path = '$imageName' WHERE name = '$username'";
            mysqli_query($conn, $updateQuery);

            // Update session data
            $_SESSION['image_path'] = $imageName;

            // Redirect to settings page with success message
            echo "<script>
            window.location.href = 'setting.php?status=success';
          </script>";

                exit;
        } else {
            echo "Failed to upload image.";
        }
    } else {
        echo "No image was uploaded.";
    }
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            padding-top: 50px;
            background-color: #f8f9fa;
        }
        .container {
            max-width: 600px;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .alert {
            margin-bottom: 20px;
        }
        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }
        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #004085;
        }
        .form-group label {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Change Admin Image</h2>
        <?php if (isset($_GET['status']) && $_GET['status'] == 'success'): ?>
            <div class="alert alert-success">Image updated successfully!</div>
        <?php endif; ?>

        <form action="setting.php" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="image_path">Upload New Image:</label>
                <input type="file" name="image_path" id="image_path" class="form-control">
            </div>
            <button type="submit" class="btn btn-primary">Update Image</button>
        </form>
    </div>
</body>
</html>
<?php
include 'footer.php';
?>
