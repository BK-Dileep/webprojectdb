<?php
$hostname = '127.0.0.1';  // MySQL runs locally
$username = 'php_docker'; // Matches start.sh user
$password = 'root';       // Matches start.sh password
$database = 'php_docker'; // Matches start.sh database

$conn = new mysqli(hostname: $hostname, username: $username, password: $password, database: $database);

if ($conn->connect_error) {
    die("Connection failed: " . mysqli_connect_error());
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $Email = $_POST['Email'];
    $NewPassword = $_POST['newpass'];
    $ConfirmPassword = $_POST['confirmpass'];

    if (empty($Email) || empty($NewPassword) || empty($ConfirmPassword)) {
        header("Location: forgot.html?error=Please fill all the fields");
        $conn->close();
        exit();
    }

    if ($NewPassword !== $ConfirmPassword) {
        header("Location: forgot.html?error=New password and Confirm password do not match");
        $conn->close();
        exit();
    }

    $checkQuery = "SELECT * FROM student WHERE Email = ?";
    $checkStmt = $conn->prepare($checkQuery);
    $checkStmt->bind_param("s", $Email);
    $checkStmt->execute();
    $result = $checkStmt->get_result();
    if ($result->num_rows == 0) {
        $checkStmt->close();
        header("Location: forgot.html?error=Email not found");
        $conn->close();
        exit();
    }
    $checkStmt->close();

    // Hash the new password before updating
    $hashedPassword = password_hash($NewPassword, PASSWORD_DEFAULT);

    $updateQuery = "UPDATE student SET New_Password = ?, ModifiedAt = CURRENT_TIMESTAMP WHERE Email = ?";
    $stmt = $conn->prepare($updateQuery);
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("ss", $hashedPassword, $Email);

    if ($stmt->execute()) {
        session_start();
        $_SESSION['user_email'] = $Email;
        $stmt->close();
        $conn->close();
        header("Location: index.html?success=Congratulations, you have logged in successfully!");
        exit();
    } else {
        $error = "Error updating password: " . $conn->error;
        $stmt->close();
        $conn->close();
        header("Location: forgot.html?error=" . urlencode($error));
        exit();
    }
}

$conn->close();
?>