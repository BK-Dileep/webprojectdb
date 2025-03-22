<<<<<<< HEAD
<?php
// Start session BEFORE any output
session_start();

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
    $Password = $_POST['pass'];

    if (empty($Email) || empty($Password)) {
        header("Location: index.html?error=Please fill all the fields");
        $conn->close();
        exit();
    }

    $checkQuery = "SELECT * FROM student WHERE Email = ?";
    $stmt = $conn->prepare($checkQuery);
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("s", $Email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $storedPassword = $row['New_Password'];
        if (password_verify($Password, $storedPassword)) {
            $_SESSION['user_email'] = $Email;
            $stmt->close();
            $conn->close();
            header("Location: done.html"); // Redirect to done.html on success
            exit();
        } else {
            $stmt->close();
            $conn->close();
            header("Location: index.html?error=Invalid password");
            exit();
        }
    } else {
        $stmt->close();
        $conn->close();
        header("Location: index.html?error=Invalid email");
        exit();
    }
}

$conn->close();
=======
<?php
// Start session BEFORE any output
session_start();

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
    $Password = $_POST['pass'];

    if (empty($Email) || empty($Password)) {
        header("Location: index.html?error=Please fill all the fields");
        $conn->close();
        exit();
    }

    $checkQuery = "SELECT * FROM student WHERE Email = ?";
    $stmt = $conn->prepare($checkQuery);
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("s", $Email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $storedPassword = $row['New_Password'];
        if (password_verify($Password, $storedPassword)) {
            $_SESSION['user_email'] = $Email;
            $stmt->close();
            $conn->close();
            header("Location: done.html"); // Redirect to done.html on success
            exit();
        } else {
            $stmt->close();
            $conn->close();
            header("Location: index.html?error=Invalid password");
            exit();
        }
    } else {
        $stmt->close();
        $conn->close();
        header("Location: index.html?error=Invalid email");
        exit();
    }
}

$conn->close();
>>>>>>> 6c877fc (Second commit)
?>