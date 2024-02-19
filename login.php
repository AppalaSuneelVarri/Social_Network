<?php
session_start();

$dbHost = "localhost";
$dbName = "SocialNetwork";
$dbUser = "root";
$dbPass = "";

try {
    $dbh = new PDO("mysql:host=$dbHost;dbname=$dbName", $dbUser, $dbPass);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if (isset($_POST['username']) && isset($_POST['password'])) {
        $username = $_POST['username'];
        $password = md5($_POST['password']); 

        $stmt = $dbh->prepare("SELECT * FROM users WHERE username = :username AND password = :password");
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password', $password);
        $stmt->execute();

        if ($stmt->rowCount() == 1) {
            $_SESSION['username'] = $username;
            header("Location: social_network.php");
        } else {
            $loginError = "Invalid username or password. Please try again.";
        }
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Social Network Login</title>
    <style>
        h1 {
            color: navy; 
        }
        form {
            display: flex;
            flex-direction: column;
            width: 300px; 
        }
        label {
            margin-bottom: 10px; 
        }
        input {
            margin-bottom: 10px; 
        }
        .login-button {
            align-self: flex-end; 
        }
    </style>
</head>
<body>
    <h1>Social Network Login</h1>
    <form method="post" action="login.php">
        <label for="username">Username:</label>
        <input type="text" name="username" id="username"><br>
        <label for="password">Password:</label>
        <input type="password" name="password" id="password"><br>
        <input type="submit" value="Login" class="login-button">
    </form>
    <?php if (isset($loginError)) { echo "<p>$loginError</p>"; } ?>
</body>
</html>
