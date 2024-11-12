<?php
// Database Connection
$servername = "localhost";
$username = "root";
$password = "";
$database = "EmployeeDB";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize variables for the form
$id = $name = $position = $department = $salary = "";
$editMode = false;

// Handle form submission for Add and Edit actions
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $position = $_POST['position'];
    $department = $_POST['department'];
    $salary = $_POST['salary'];

    if (isset($_POST['id']) && $_POST['id'] != "") { // Edit action
        $id = $_POST['id'];
        $sql = "UPDATE employees SET name='$name', position='$position', department='$department', salary='$salary' WHERE id=$id";
    } else { // Add action
        $sql = "INSERT INTO employees (name, position, department, salary) VALUES ('$name', '$position', '$department', '$salary')";
    }

    if ($conn->query($sql) === TRUE) {
        header("Location: index.php"); // Refresh page to clear form
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Handle delete action
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $sql = "DELETE FROM employees WHERE id = $id";
    if ($conn->query($sql) === TRUE) {
        header("Location: index.php"); // Refresh page
    } else {
        echo "Error deleting record: " . $conn->error;
    }
}

// Handle edit action
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $result = $conn->query("SELECT * FROM employees WHERE id = $id");
    if ($result->num_rows > 0) {
        $employee = $result->fetch_assoc();
        $name = $employee['name'];
        $position = $employee['position'];
        $department = $employee['department'];
        $salary = $employee['salary'];
        $editMode = true;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Employee Data Book</title>
    <link rel="stylesheet" href="body.css">
</head>
<body>
    <h1>Employee Data Book</h1>

    <!-- Form for Adding and Editing -->
    <form action="index.php" method="POST">
        <input type="hidden" name="id" value="<?php echo $id; ?>">
        <input type="text" name="name" placeholder="Name" value="<?php echo $name; ?>" required>
        <input type="text" name="position" placeholder="Position" value="<?php echo $position; ?>" required>
        <input type="text" name="department" placeholder="Department" value="<?php echo $department; ?>" required>
        <input type="number" step="0.01" name="salary" placeholder="Salary" value="<?php echo $salary; ?>" required>
        <button type="submit"><?php echo $editMode ? "Update Employee" : "Add Employee"; ?></button>
    </form>

    <!-- Display Employees Table -->
    <table>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Position</th>
            <th>Department</th>
            <th>Salary</th>
            <th>Actions</th>
        </tr>
        <?php
        $result = $conn->query("SELECT * FROM employees");
        while ($row = $result->fetch_assoc()):
        ?>
        <tr>
            <td><?php echo $row['id']; ?></td>
            <td><?php echo $row['name']; ?></td>
            <td><?php echo $row['position']; ?></td>
            <td><?php echo $row['department']; ?></td>
            <td><?php echo $row['salary']; ?></td>
            <td>
                <a href="index.php?edit=<?php echo $row['id']; ?>">Edit</a> |
                <a href="index.php?delete=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure you want to delete this record?');">Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>
