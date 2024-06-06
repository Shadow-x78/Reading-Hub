<?php
session_start();

require_once "config.php";

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
  header("location: login.php");
  exit;
}

if (isset($_GET["delete_id"]) && !empty(trim($_GET["delete_id"]))) {
  $id = htmlspecialchars($_GET["delete_id"]);
  $sql = "DELETE FROM books_db WHERE id = ?";
  if ($stmt = mysqli_prepare($link, $sql)) {
    mysqli_stmt_bind_param($stmt, "i", $id);
    if (mysqli_stmt_execute($stmt)) {
      header("location: hub.php");
      exit();
    } else {
      echo "Oops! Something went wrong. Please try again later.";
    }
  }
  mysqli_stmt_close($stmt);
}

$sql = "SELECT id, title, image_url, by_user FROM books_db";
$stmt = mysqli_prepare($link, $sql);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$books = [];
if (mysqli_num_rows($result) > 0) {
  while ($row = mysqli_fetch_assoc($result)) {
    $books[] = $row;
  }
} else {
  $no_books_found = true;
}

mysqli_stmt_close($stmt);

mysqli_close($link);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Reading Hub - EHTP</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <link rel="icon" type="image/x-icon" href="/images/logo.png" />
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: sans-serif;
    }

    .banner {
      width: 100%;
      min-height: 100vh;
      background-image: linear-gradient(rgba(0, 0, 0, 0.75), rgba(0, 0, 0, 0.75)),
        url(https://images.alphacoders.com/132/thumb-1920-1326370.png);
      background-size: cover;
      background-position: center;
      position: relative;
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

    .navbar ul {
      list-style-type: none;
      display: flex;
    }

    .navbar ul li {
      margin: 0 20px;
    }

    .navbar ul li a {
      text-decoration: none;
      color: #fff;
      text-transform: uppercase;
      font-size: 18px;
    }

    .title {
      color: #fff;
      text-align: center;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      font-size: 36px;
      text-transform: capitalize;
    }

    .content {
      width: 85%;
      margin: auto;
      text-align: center;
      color: #fff;
      padding-bottom: 50px;
    }

    .book-container {
      display: flex;
      flex-wrap: wrap;
      justify-content: center;
      gap: 20px;
    }

    .book-container a {
      text-decoration: none;
    }

    .book-card {
      background-color: rgba(255, 255, 255, 0.2);
      border-radius: 10px;
      padding: 20px;
      width: calc(50% - 20px);
      max-width: 300px;
      display: flex;
      flex-direction: column;
      align-items: center;
      text-align: center;
    }

    .book-card img {
      max-width: 100%;
      height: 200px;
      border-radius: 5px;
    }

    .book-info {
      margin-top: 10px;
    }

    .book-info h2 {
      font-size: 16px;
      margin-bottom: 5px;
    }

    .book-info p {
      font-size: 14px;
      color: #888;
      margin-bottom: 10px;
    }

    .book-info button {
      background-color: #007bff;
      color: #fff;
      border: none;
      padding: 5px 10px;
      border-radius: 5px;
      cursor: pointer;
    }

    .book-info button:hover {
      background-color: #0056b3;
    }

    @media (max-width: 768px) {
      .book-card {
        width: calc(100% - 20px);
      }
    }
  </style>
</head>

<body>
  <div class="banner">
    <div class="navbar">
      <a href="/"><img src="/images/logo.png" class="logo" alt="Reading Hub"></a>
      <ul>
        <li><a href="/upload.php">Upload</a></li>
        <li><a href="/logout.php">Logout</a></li>
      </ul>
    </div>
    <h2 class="title">Shared Books</h2>
    <div class="content">
      <div class="book-container">
        <?php if (isset($no_books_found) && $no_books_found) : ?>
          <h3><b>No Books Available</b></h3>
        <?php else : ?>
          <?php foreach ($books as $book) : ?>
            <div class="book-card">
              <a href="details.php?id=<?= htmlspecialchars($book['id']); ?>">
                <?php if ($book['image_url']) : ?>
                  <img src="<?= htmlspecialchars($book['image_url']); ?>" alt="<?= htmlspecialchars($book['title']); ?>">
                <?php else : ?>
                  <img src="No Image Available" alt="No Image Available">
                <?php endif; ?>
              </a>
              <div class="book-info">
                <h2><b><?= htmlspecialchars($book['title']); ?></b></h2>
                <p><b>Added by: <?= htmlspecialchars($book['by_user']); ?></b></p>
                <?php if ($_SESSION["username"] === "administrator0" || $_SESSION["username"] === "SHADOW_x7") : ?>
                  <button onclick="confirmDelete(<?= htmlspecialchars($book['id']); ?>, '<?= htmlspecialchars($book['title']); ?>', event)" class="btn btn-primary"><i class="fas fa-trash"></i> Delete</button>
                <?php endif; ?>
              </div>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <script>
    function confirmDelete(id, title, event) {
      event.preventDefault();
      if (confirm("Are you sure you want to delete " + title + " ?")) {
        window.location.href = "hub.php?delete_id=" + id;
      } else {
        event.preventDefault();
      }
    }
  </script>
</body>

</html>