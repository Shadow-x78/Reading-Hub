<?php
require_once "config.php";

$username = $password = $confirm_password = "";
$register_err = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);
    $confirm_password = trim($_POST["confirm_password"]);

    if (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
        $register_err = "Username can only contain letters, numbers, and underscores.";
    } else {
        $sql = "SELECT id FROM users_db WHERE username = ?";
        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "s", $param_username);
            $param_username = $username;
            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_store_result($stmt);
                if (mysqli_stmt_num_rows($stmt) == 1) {
                    $register_err = "This username is already taken.";
                }
            } else {
                $register_err = "Oops! Something went wrong. Please try again later.";
            }
            mysqli_stmt_close($stmt);
        }
    }

    if (strlen($password) < 6) {
        $register_err = "Password must have at least 6 characters.";
    }

    if ($password != $confirm_password) {
        $register_err = "Password did not match.";
    }

    if (empty($register_err)) {
        $sql = "INSERT INTO users_db (username, password) VALUES (?, ?)";
        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "ss", $param_username, $param_password);
            $param_username = $username;
            $param_password = password_hash($password, PASSWORD_DEFAULT);
            if (mysqli_stmt_execute($stmt)) {
                header("location: login.php");
                exit();
            } else {
                $register_err = "Oops! Something went wrong. Please try again later.";
            }
            mysqli_stmt_close($stmt);
        }
    }
    mysqli_close($link);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Reading Hub - Register</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
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
            width: 90%;
            max-width: 420px;
            background: transparent;
            border: 2px solid rgba(255, 255, 255, .2);
            backdrop-filter: blur(9px);
            color: #fff;
            border-radius: 12px;
            padding: 30px 40px;
        }

        .wrapper h1 {
            font-size: 36px;
            text-align: center;
        }

        .wrapper .input-box {
            position: relative;
            width: 100%;
            margin: 30px 0;
        }

        .input-box input {
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

        .input-box input::placeholder {
            color: #fff;
        }

        .input-box i {
            position: absolute;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 20px;
        }

        .wrapper .btn {
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

        .wrapper .login-link {
            font-size: 14.5px;
            text-align: center;
            margin: 20px 0 15px;
        }

        .login-link p a {
            color: #fff;
            text-decoration: none;
            font-weight: 600;
        }

        .login-link p a:hover {
            text-decoration: underline;
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
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <a href="/"><img src="/images/logo.png" class="logo" title="Reading Hub"></a>
            <h1>Register</h1>
            <?php if (!empty($register_err)) { ?>
                <br>
                <div class="alert"><?php echo htmlspecialchars($register_err); ?></div>
            <?php } ?>
            <div class="input-box">
                <input type="text" name="username" placeholder="Username" value="<?php echo htmlspecialchars($username); ?>" required>
                <i class='bx bxs-user'></i>
            </div>
            <div class="input-box">
                <input type="password" name="password" placeholder="Password" required>
                <i class='bx bxs-lock-alt'></i>
            </div>
            <div class="input-box">
                <input type="password" name="confirm_password" placeholder="Confirm Password" required>
                <i class='bx bxs-lock-alt'></i>
            </div>
            <button type="submit" class="btn">Register</button>
            <div class="login-link">
                <p>Already have an account? <a href="/login.php">Login</a></p>
            </div>
        </form>
    </div>
</body>

</html>