<?php
include('../includes/db.php'); // Include DB connection

// Fetch users from the database
$query = "SELECT * FROM users";
$result = mysqli_query($conn, $query);

// Check if the admin requested to delete a user
if (isset($_GET['delete_user'])) {
    $user_id = $_GET['delete_user'];
    $delete_query = "DELETE FROM users WHERE id = $user_id";
    mysqli_query($conn, $delete_query);
    header("Location: manage_users.php"); // Redirect after deletion
}
?>

<h2>Manage Users</h2>

<table>
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Email</th>
        <th>Phone</th>
        <th>Action</th>
    </tr>

    <?php while ($user = mysqli_fetch_assoc($result)) : ?>
        <tr>
            <td><?php echo $user['id']; ?></td>
            <td><?php echo $user['name']; ?></td>
            <td><?php echo $user['email']; ?></td>
            <td><?php echo $user['phone']; ?></td>
            <td>
                <a href="edit_user.php?id=<?php echo $user['id']; ?>">Edit</a> | 
                <a href="manage_users.php?delete_user=<?php echo $user['id']; ?>" onclick="return confirm('Are you sure?')">Delete</a>
            </td>
        </tr>
    <?php endwhile; ?>
</table>
