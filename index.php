<?php
session_start(); // Add session_start() for potential future use

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $FirstName = $_POST['firstName'];
    $LastName = $_POST['lastName'];
    $Age = (int)$_POST['age'];
    $Gender = $_POST['gender'];
    $Email = $_POST['email'];
    $Phone = $_POST['phone'];
    $New_Password = $_POST['newPassword'];
    $Confirm_Password = $_POST['confirmPassword'];

    if (empty($FirstName) || empty($LastName) || empty($Age) || empty($Gender) || empty($Email) || empty($Phone) || empty($New_Password) || empty($Confirm_Password)) {
        header("Location: Registration.html?error=empty_fields");
        exit();
    }

    if ($New_Password != $Confirm_Password) {
        header("Location: Registration.html?error=password_mismatch");
        exit();
    }

    $FirstName = htmlspecialchars($FirstName);
    $LastName = htmlspecialchars($LastName);
    $Age = htmlspecialchars($Age);
    $Gender = htmlspecialchars($Gender);
    $Email = htmlspecialchars($Email);
    $Phone = htmlspecialchars($Phone);

    $hashedPassword = password_hash($New_Password, PASSWORD_DEFAULT);

    $db_host = '127.0.0.1';
    $db_user = 'php_docker';
    $db_password = 'root';
    $db_name = 'php_docker';

    // Line 37: Connection attempt
    $conn = new mysqli(hostname: $db_host, username: $db_user, password: $db_password, database: $db_name);
    if ($conn->connect_error) {
        header("Location: Registration.html?error=Database connection failed: " . urlencode($conn->connect_error));
        exit();
    }

    try {
        $checkQuery = "SELECT * FROM student WHERE Email=?";
        $stmt = $conn->prepare($checkQuery);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }
        $stmt->bind_param("s", $Email);
        $stmt->execute();
        $checkResult = $stmt->get_result();
        $stmt->close();

        if ($checkResult->num_rows > 0) {
            throw new Exception("The Email entered already exists");
        }

        $checkQuery = "SELECT * FROM student WHERE Phone=?";
        $stmt = $conn->prepare($checkQuery);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }
        $stmt->bind_param("s", $Phone);
        $stmt->execute();
        $checkResult = $stmt->get_result();
        $stmt->close();

        if ($checkResult->num_rows > 0) {
            throw new Exception("The Phone Number entered already exists");
        }

        $in = "INSERT INTO student (FirstName, LastName, Age, Gender, Email, Phone, New_Password) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($in);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }
        $stmt->bind_param("ssissss", $FirstName, $LastName, $Age, $Gender, $Email, $Phone, $hashedPassword);

        if ($stmt->execute()) {
            $stmt->close();
            $conn->close();
            header("Location: index.html?success=registered");
            exit();
        } else {
            throw new Exception("Error registering details: " . $conn->error);
        }
    } catch (Exception $e) {
        if (isset($stmt) && $stmt) {
            $stmt->close();
        }
        $conn->close();
        header("Location: Registration.html?error=" . urlencode($e->getMessage()));
        exit();
    }
}
?>