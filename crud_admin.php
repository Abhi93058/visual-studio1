<?php
 include 'header.php';
include 'sidebar.php';
include('config/db_connect.php');
$sql = "SELECT * FROM test  Where role_id='1'";
$result = $conn->query($sql);
?>
<div class="container">
    <h2>Admin Details</h2>
    <a href="create_admin.php" class="btn btn-primary mb-3">+ Add Admin</a>

   <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-success">
            <?php
                echo $_SESSION['message'];
                unset($_SESSION['message']);
            ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger">
            <?php
                echo $_SESSION['error'];
                unset($_SESSION['error']);
            ?>
        </div>
    <?php endif; ?>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>id</th>
                <th>admin Name</th>
                <th>Email</th>
                <th>Password</th>
                <th>Edit</th>
                <th>Delete</th>
                <th>Image</th>

            </tr>
        </thead>
        <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
            <td><?php echo $row['id']; ?></td>
                <td><?php echo $row['name']; ?></td>
                <td><?php echo $row['email']; ?></td>
                <td><?php echo $row['password']; ?></td>
                <td><a href="edit.php?id=<?php echo $row['id']; ?>" class="btn btn-warning">Edit</a></td>
                <td>
                    <a href="delete.php?id=<?php echo $row['id']; ?>"class="btn btn-primary">delete</a>
                </td>
                <td>
    <img src="<?php echo $row['image_path']; ?>" alt="Image" style="width: 50px; height: 50px;">
</td>
              
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
       


<?php
include('footer.php');
?>
      