<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['email'])) {
    // Redirect to login page if not logged in
    header("Location: login.php");
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "my_portfolio";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch user's email from session
$email = $_SESSION['email'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $uploadDir = 'images/';
    $uploadFile = $uploadDir . basename($_FILES['photo']['name']);
    $imageFileType = strtolower(pathinfo($uploadFile, PATHINFO_EXTENSION));
    $check = getimagesize($_FILES['photo']['tmp_name']);

    // Check if image file is an actual image or fake image
    if ($check !== false) {
        // Allow certain file formats
        if ($imageFileType == "jpg" || $imageFileType == "png" || $imageFileType == "jpeg" || $imageFileType == "gif") {
            // Check if file already exists
            if (!file_exists($uploadFile)) {
                // Check file size (limit to 5MB)
                if ($_FILES['photo']['size'] <= 5000000) {
                    if (move_uploaded_file($_FILES['photo']['tmp_name'], $uploadFile)) {
                        // File is uploaded, now save the file path to the database
                        $stmt = $conn->prepare("UPDATE users SET photo_path=? WHERE email=?");
                        $stmt->bind_param("ss", $uploadFile, $email);
                        if ($stmt->execute()) {
                            // Return the file path as JSON response
                            echo json_encode(['photo_path' => $uploadFile]);
                            exit();
                        } else {
                            echo json_encode(['error' => "Error updating database: " . $stmt->error]);
                            exit();
                        }
                        $stmt->close();
                    } else {
                        echo json_encode(['error' => "Sorry, there was an error uploading your file."]);
                        exit();
                    }
                } else {
                    echo json_encode(['error' => "Sorry, your file is too large."]);
                    exit();
                }
            } else {
                echo json_encode(['error' => "Sorry, file already exists."]);
                exit();
            }
        } else {
            echo json_encode(['error' => "Sorry, only JPG, JPEG, PNG & GIF files are allowed."]);
            exit();
        }
    } else {
        echo json_encode(['error' => "File is not an image."]);
        exit();
    }
}

$conn->close();
?>
