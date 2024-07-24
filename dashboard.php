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

// Fetch user's email and photo path from database
$email = $_SESSION['email'];
$sql = $conn->prepare("SELECT email, photo_path FROM users WHERE email=?");
$sql->bind_param("s", $email);
$sql->execute();
$result = $sql->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $user_email = $row['email'];
    $photo_path = $row['photo_path'];
} else {
    echo "Error fetching user's email";
    $conn->close();
    exit();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: greenyellow;
        }
        .container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            width: 400px;
            text-align: center;
            position: relative;
        }
        .profile-pic {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            position: absolute;
            top: -50px;
            left: 50%;
            transform: translateX(-50%);
            border: 3px solid #fff;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h2 {
            margin-top: 60px;
            margin-bottom: 20px;
        }
        .welcome {
            margin-bottom: 20px;
        }
        .logout-btn {
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-top: 20px;
        }
        .logout-btn:hover {
            background-color: #0056b3;
        }
        input[type="email"] {
            width: calc(100% - 20px);
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        input[type="file"] {
            display: none;
        }
        .upload-btn {
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .upload-btn:hover {
            background-color: #0056b3;
        }
        .file-name {
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <img src="<?php echo $photo_path; ?>" alt="Profile Picture" class="profile-pic" id="profile-pic">
        <h2>Welcome to the Dashboard</h2>
        <div class="welcome">
            Welcome, <?php echo $_SESSION['email']; ?>!
        </div>
        <form enctype="multipart/form-data" id="upload-form">
            <input type="email" name="email" placeholder="Your Email" value="<?php echo $user_email; ?>" required>
            <input type="file" id="file-upload" name="photo" accept="image/*" required>
            <label for="file-upload" class="upload-btn">Upload Photo</label>
            <span class="file-name"></span>
            <button type="button" id="save-btn" class="logout-btn">Save</button>
        </form>
        <form id="logout-form" action="logout.php" method="POST">
            <button type="submit" class="logout-btn">Logout</button>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var profilePic = document.getElementById('profile-pic');
            var photoPath = "<?php echo $photo_path; ?>";
            if (photoPath) {
                profilePic.src = photoPath;
            }
        });

        document.getElementById('file-upload').addEventListener('change', function() {
            var fileName = this.files[0].name;
            document.querySelector('.file-name').textContent = fileName;
        });

        document.getElementById('save-btn').addEventListener('click', function() {
            var formData = new FormData(document.getElementById('upload-form'));
            fetch('upload_photo.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.photo_path) {
                    document.getElementById('profile-pic').src = data.photo_path;
                } else if (data.error) {
                    alert(data.error);
                }
            })
            .catch(error => console.error('Error:', error));
        });

        document.getElementById('logout-form').addEventListener('submit', function(event) {
            event.preventDefault(); // Prevent the default form submission
            fetch('logout.php', {
                method: 'POST',
                body: new FormData(this)
            }).then(response => {
                if (response.ok) {
                    window.location.href = 'radika.html'; // Redirect after successful logout
                }
            }).catch(error => {
                console.error('Error:', error);
            });
        });
    </script>
</body>
</html>
