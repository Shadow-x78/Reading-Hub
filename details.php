<?php
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

require_once "config.php";

$book = [];
$details_err = "";
$comment_err = "";

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["id"])) {
    $book_id = $_GET["id"];

    $sql = "SELECT id, title, author, summary, image_url, by_user FROM books_db WHERE id = ?";
    $stmt = mysqli_prepare($link, $sql);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $book_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) > 0) {
            $book = mysqli_fetch_assoc($result);
        } else {
            $details_err = "No book found with the specified ID.";
        }

        mysqli_stmt_close($stmt);
    } else {
        $details_err = "Failed to prepare the SQL statement.";
    }
} else {
    $details_err = "Invalid request.";
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["comment"])) {
        if (empty(trim($_POST["comment"]))) {
            $comment_err = "Please enter a comment.";
        } else {
            $comment = trim($_POST["comment"]);
            $username = $_SESSION["username"];
            $book_id = $_POST["book_id"];

            $sql = "INSERT INTO comments_db (username, comment, created_at, book_id) VALUES (?, ?, NOW(), ?)";
            $stmt = mysqli_prepare($link, $sql);

            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "ssi", $username, $comment, $book_id);
                if (mysqli_stmt_execute($stmt)) {
                    header("location: {$_SERVER['PHP_SELF']}?id={$book_id}");
                    exit;
                } else {
                    $comment_err = "Failed to submit the comment.";
                }

                mysqli_stmt_close($stmt);
            } else {
                $comment_err = "Failed to prepare the SQL statement.";
            }
        }
    } elseif (isset($_POST["delete_comment"])) {
        $delete_comment_id = $_POST["delete_comment_id"];
        $book_id = $_POST["book_id"];

        $sql = "DELETE FROM comments_db WHERE id = ?";
        $stmt = mysqli_prepare($link, $sql);

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "i", $delete_comment_id);
            if (mysqli_stmt_execute($stmt)) {
                header("location: {$_SERVER['PHP_SELF']}?id={$book_id}");
                exit;
            } else {
                echo "Failed to delete the comment.";
            }

            mysqli_stmt_close($stmt);
        } else {
            echo "Failed to prepare the SQL statement.";
        }
    }
}

$comments = [];
if (!empty($book_id)) {
    $sql = "SELECT id, username, comment, created_at FROM comments_db WHERE book_id = ?";
    $stmt = mysqli_prepare($link, $sql);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $book_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        while ($row = mysqli_fetch_assoc($result)) {
            $comments[] = $row;
        }

        mysqli_stmt_close($stmt);
    }
}

mysqli_close($link);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Reading Hub - Details</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="icon" type="image/x-icon" href="/images/logo.png">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: sans-serif;
        }

        body {
            background: linear-gradient(rgba(0, 0, 0, 0.75), rgba(0, 0, 0, 0.75)), url("https://images.alphacoders.com/132/thumb-1920-1326370.png");
            background-size: cover;
            background-position: center;
            color: #ccc;
        }

        .container {
            max-width: 1200px;
            margin: auto;
            padding: 50px 20px;
        }

        .navbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .back-btn {
            text-decoration: none;
            display: inline-block;
            padding: 10px 20px;
            background-color: #009688;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .back-btn:hover {
            background-color: #00796b;
        }

        .back-btn button {
            border: none;
            background: none;
            color: inherit;
            font-size: inherit;
            cursor: pointer;
        }

        .nav-links ul {
            list-style-type: none;
            display: flex;
        }

        .nav-links ul li {
            margin: 0 20px;
        }

        .nav-links ul li a {
            text-decoration: none;
            color: #fff;
            text-transform: uppercase;
            font-size: 18px;
        }

        .book-card {
            display: flex;
            background-color: rgba(255, 255, 255, 0.2);
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .book-cover {
            width: 150px;
            height: auto;
            margin-right: 20px;
            border-radius: 10px;
        }

        .details-section {
            flex-grow: 1;
        }

        .details-section h3 {
            font-size: 18px;
            margin-bottom: 5px;
        }

        .about-book-scroll {
            max-height: 150px;
            overflow-y: auto;
            margin-bottom: 10px;
            padding-right: 10px;
            border-right: 1px solid #ccc;
        }

        .comment-section {
            margin-top: 30px;
        }

        .add-comment textarea {
            width: 100%;
            min-height: 100px;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
            margin-bottom: 10px;
        }

        .add-comment button {
            width: 100%;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            background-color: #009688;
            color: #fff;
            cursor: pointer;
            margin-top: 10px;
        }

        .add-comment button:hover {
            background-color: #00796b;
        }

        .comment {
            background-color: rgba(255, 255, 255, 0.2);
            border-radius: 10px;
            padding: 10px;
            margin-bottom: 10px;
        }

        .comment p {
            color: #ccc;
            font-size: 16px;
        }

        .delete-btn {
            background-color: #ff5252;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
        }

        .delete-btn:hover {
            background-color: #ff0000;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="navbar">
            <a href="/hub.php" class="back-btn">
                <button><i class="fas fa-arrow-left"></i> Back</button>
            </a>
            <div class="nav-links">
                <ul>
                    <li><a href="/upload.php">Upload</a></li>
                    <li><a href="/logout.php">Logout</a></li>
                </ul>
            </div>
        </div>
        <div class="book-container">
            <?php if (!empty($book)) : ?>
                <div class="book-card">
                    <img src="<?= htmlspecialchars($book['image_url']); ?>" alt="<?= htmlspecialchars($book['title']); ?>" class="book-cover">
                    <div class="details-section">
                        <h3>Title</h3>
                        <p><?= htmlspecialchars($book['title']); ?></p>
                        <br>
                        <h3>Author</h3>
                        <p><?= htmlspecialchars($book['author']); ?></p>
                        <br>
                        <h3>About the Book</h3>
                        <div class="about-book-scroll">
                            <p><?= htmlspecialchars($book['summary']); ?></p>
                        </div>
                        <br>
                        <h3>Uploaded by</h3>
                        <p><?= htmlspecialchars($book['by_user']); ?></p>
                    </div>
                </div>
                <div class="comment-section">
                    <div class="add-comment">
                        <form action="<?= htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                            <input type="hidden" name="book_id" value="<?= isset($book['id']) ? htmlspecialchars($book['id']) : '' ?>">
                            <textarea name="comment" placeholder="Add your comment..." rows="4" required></textarea>
                            <button type="submit">Submit</button>
                            <span class="help-block"><?= $comment_err; ?></span>
                        </form>
                    </div>
                    <br>
                    <div class="comment-list">
                        <?php
                        usort($comments, function ($a, $b) {
                            return strtotime($b['created_at']) - strtotime($a['created_at']);
                        });

                        if (!empty($comments)) : ?>
                            <?php foreach ($comments as $comment) : ?>
                                <div class='comment'>
                                    <p><b><?= htmlspecialchars($comment['comment']); ?></b></p>
                                    <br>
                                    <span>By <?= htmlspecialchars($comment['username']); ?> on <?= htmlspecialchars($comment['created_at']); ?></span>
                                    <?php if ($_SESSION["username"] === "administrator0" || $_SESSION["username"] === "SHADOW_x7") : ?>
                                        <br>
                                        <br>
                                        <form action="<?= htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                                            <input type="hidden" name="delete_comment_id" value="<?= $comment['id']; ?>">
                                            <input type="hidden" name="book_id" value="<?= $book_id; ?>">
                                            <button type="submit" name="delete_comment" class="delete-btn"><i class="fas fa-trash"></i> Delete</button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <p>No comments available for this book.</p>
                        <?php endif; ?>
                    </div>
                </div>
            <?php else : ?>
                <p><?= $details_err ?></p>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>