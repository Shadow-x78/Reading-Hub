<?php
require_once "config.php";

session_start();

$title = $author = $summary = $image_url = $by_user = "";
$upload_err = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = htmlspecialchars(trim($_POST["title"]));
    $author = htmlspecialchars(trim($_POST["author"]));
    $summary = htmlspecialchars(trim($_POST["summary"]));
    $image_url = htmlspecialchars(trim($_POST["image_url"]));
    $by_user = $_SESSION["username"];

    if (!empty($title) && !empty($author) && !empty($summary) && !empty($image_url)) {
        $sql = "INSERT INTO books_db (title, author, summary, image_url, by_user) VALUES (?, ?, ?, ?, ?)";
        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "sssss", $title, $author, $summary, $image_url, $by_user);
            if (mysqli_stmt_execute($stmt)) {
                header("Location: hub.php");
                exit();
            } else {
                $upload_err = "Oops! Something went wrong. Please try again later.";
            }
            mysqli_stmt_close($stmt);
        } else {
            $upload_err = "Oops! Something went wrong. Please try again later.";
        }
    } else {
        $upload_err = "Please fill out all fields.";
    }
    mysqli_close($link);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Reading Hub - Upload</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="icon" type="image/x-icon" href="/images/logo.png" />
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Poppins", sans-serif;
        }

        .logo {
            display: block;
            width: 120px;
            cursor: pointer;
            margin-left: auto;
            margin-right: auto;
        }

        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background: url(https://images5.alphacoders.com/133/thumb-1920-1338186.png) no-repeat;
            background-size: cover;
            background-position: center;
        }

        .wrapper {
            width: 420px;
            background: transparent;
            border: 2px solid rgba(255, 255, 255, .2);
            backdrop-filter: blur(9px);
            color: #fff;
            border-radius: 12px;
            padding: 30px 40px;
            position: relative;
        }

        .wrapper h1 {
            font-size: 36px;
            text-align: center;
        }

        .input-box {
            margin-bottom: 20px;
        }

        .input-box input,
        .input-box textarea {
            width: 100%;
            background: transparent;
            border: none;
            outline: none;
            border: 2px solid rgba(255, 255, 255, .2);
            border-radius: 40px;
            font-size: 16px;
            color: #fff;
            padding: 20px 25px;
        }

        .input-box textarea {
            min-height: 120px;
            resize: vertical;
        }

        .btn {
            width: 100%;
            height: 45px;
            background: #fff;
            border: none;
            outline: none;
            border-radius: 40px;
            box-shadow: 0 0 10px rgba(0, 0, 0, .1);
            cursor: pointer;
            font-size: 16px;
            color: #333;
            font-weight: 600;
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
            cursor: pointer;
        }

        .alert {
            background-color: #f44336;
            color: white;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            text-align: center;
        }

        @media only screen and (max-width: 600px) {
            .wrapper {
                width: 90%;
                max-width: none;
            }
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <a href="#" onclick="goBack()" class="back-btn">
            <button><i class="fas fa-arrow-left"></i> Back</button>
        </a>
        <br>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <h1>Upload</h1>
            <?php if (!empty($upload_err)) { ?>
                <br>
                <div class="alert"><?php echo htmlspecialchars($upload_err); ?></div>
            <?php } ?>
            <br>
            <div class="input-box">
                <input type="text" placeholder="Title of the book" id="title" name="title" required>
            </div>
            <div class="input-box">
                <input type="text" placeholder="Author" id="author" name="author" required>
            </div>
            <div class="input-box">
                <textarea placeholder="About the book (max 5000 words)" id="summary" name="summary" rows="10" cols="50" maxlength="5000" required></textarea>
            </div>
            <div class="input-box">
                <input type="text" placeholder="Image URL" id="image_url" name="image_url" required>
            </div>

            <button type="submit" class="btn">Upload</button>
        </form>
    </div>

    <script>
        function goBack() {
            if (confirm("Warning: Are you sure you want to go back to the hub? Any unsaved changes will be lost.")) {
                window.location.href = "hub.php";
            }
        }
    </script>

</body>

</html>