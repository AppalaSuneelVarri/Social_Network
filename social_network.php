<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$dbHost = "localhost";
$dbName = "SocialNetwork";
$dbUser = "root";
$dbPass = "";

try {
    $dbh = new PDO("mysql:host=$dbHost;dbname=$dbName", $dbUser, $dbPass);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $username = $_SESSION['username'];

    if (isset($_GET['add'])) {
        $friendToAdd = $_GET['add'];
        $stmt = $dbh->prepare("INSERT INTO friends (user, friend) VALUES (:user, :friend)");
        $stmt->bindParam(':user', $username);
        $stmt->bindParam(':friend', $friendToAdd);
        $stmt->execute();
    } elseif (isset($_GET['remove'])) {
        $friendToRemove = $_GET['remove'];
        $stmt = $dbh->prepare("DELETE FROM friends WHERE user = :user AND friend = :friend");
        $stmt->bindParam(':user', $username);
        $stmt->bindParam(':friend', $friendToRemove);
        $stmt->execute();
    }

    $stmt = $dbh->prepare("SELECT username, fullname, email FROM users WHERE username = :username");
    $stmt->bindParam(':username', $username);
    $stmt->execute();
    $user = $stmt->fetch();

    $stmt = $dbh->prepare("SELECT f.friend, u.fullname, u.email FROM friends f JOIN users u ON f.friend = u.username WHERE f.user = :username");
    $stmt->bindParam(':username', $username);
    $stmt->execute();
    $friends = $stmt->fetchAll();

    $stmt = $dbh->prepare("SELECT username, fullname, email FROM users WHERE username != :username AND username NOT IN (SELECT friend FROM friends WHERE user = :username)");
    $stmt->bindParam(':username', $username);
    $stmt->execute();
    $nonFriends = $stmt->fetchAll();
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Social Network</title>
    <style>
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .user-info {
            display: flex;
            align-items: center;
        }

        .email {
            margin-left: 10px;
        }

        .logout-button {
            margin-left: 10px; /* Adjusted margin */
        }

        .friend-list {
            list-style: none;
        }

        .action-button {
            margin-left: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>WELCOME <?php echo $user['fullname']; ?> (<?php echo $user['username']; ?>)</h1>
        <div class="user-info">
            <p class="email">Email: <?php echo $user['email']; ?></p>
            <a href="login.php" class="logout-button">Logout</a>
        </div>
    </div>

    <h2>Friends</h2>
    <ul class="friend-list">
        <?php foreach ($friends as $friend): ?>
            <li>
                <?php echo $friend['fullname']; ?> (<?php echo $friend['friend']; ?>) : <?php echo $friend['email']; ?>
                <form method="get" action="social_network.php" class="action-button">
                    <button type="submit" name="remove" value="<?php echo $friend['friend']; ?>">Remove</button><br>
                </form>
            </li>
        <?php endforeach; ?>
    </ul>

    <h2>Others</h2>
    <ul class="friend-list">
        <?php foreach ($nonFriends as $nonFriend): ?>
            <li>
                <?php echo $nonFriend['fullname']; ?> (<?php echo $nonFriend['username']; ?>) : <?php echo $nonFriend['email']; ?>
                <form method="get" action="social_network.php" class="action-button">
                    <button type="submit" name="add" value="<?php echo $nonFriend['username']; ?>">Add</button><br>
                </form>
            </li>
        <?php endforeach; ?>
    </ul>
</body>
</html>
