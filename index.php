  <?php
  session_start();
  ?>

  <!DOCTYPE html>
  <html lang="en">

  <head>
    <meta charset="UTF-8">
    <title>Reading Hub</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <link rel="icon" type="image/x-icon" href="/images/logo.png" />
    <style>
      * {
        margin: 0;
        padding: 0;
        font-family: sans-serif;

      }

      .banner {
        width: 100%;
        height: 100vh;
        background-image: linear-gradient(rgba(0, 0, 0, 0.75), rgba(0, 0, 0, 0.75)), url(https://images5.alphacoders.com/133/thumb-1920-1338186.png);
        background-size: cover;
        background-position: center;
      }

      .navbar {
        width: 85%;
        margin: auto;
        padding: 35px 0;
        display: flex;
        align-items: center;
        justify-content: space-between;
      }

      .logo {
        width: 120px;
        cursor: pointer;

      }

      .navbar ul li {
        list-style: none;
        display: inline-block;
        margin: 0 20px;
        position: relative;
      }

      .navbar ul li a {
        text-decoration: none;
        color: #fff;
        text-transform: uppercase;

      }

      .navbar ul li::after {
        content: '';
        height: 3px;
        width: 0;
        background: #009688;
        position: absolute;
        left: 0;
        bottom: -10px;
        transition: 0.5s;
      }

      .navbar ul li:hover:after {
        width: 100%;
      }

      .content {
        width: 100%;
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        text-align: center;
        color: #fff;

      }

      .content h1 {
        font-size: 70px;
        margin-top: 80px;

      }

      .content p {
        margin: 20px auto;
        font-weight: 100;
        line-height: 25px;

      }

      span {
        background: #009688;
        height: 100%;
        width: 100%;
        border-radius: 25px;
        position: absolute;
        left: 0;
        bottom: 0;
        z-index: -1;
        transition: 0.5s;
      }
    </style>
  </head>

  <body>
    <div class="banner">
      <div class="navbar">
        <a href="/"><img src="/images/logo.png" class="logo" title="Reading Hub"></a>
        <ul>
          <?php if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) { ?>
            <li><a href="/">Home</a></li>
            <li><a href="/register.php">Register</a></li>
            <li><a href="/login.php">Login</a></li>
          <?php } else { ?>
            <li><a href="/hub.php">Hub</a></li>
            <li><a href="/logout.php">Logout</a></li>
          <?php } ?>
        </ul>
      </div>
      <div class="content">
        <h1>WELCOME TO THE READING HUB</h1>
        <p>Share your readings!</p>
      </div>
    </div>

  </body>

  </html>