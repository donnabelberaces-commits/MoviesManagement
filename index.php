
<?php
$conn = new mysqli("localhost", "root", "", "movie_db");

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}


if (isset($_POST['add'])) {
    $title = trim($_POST['title']);
    $genre = trim($_POST['genre']);
    $year = (int) $_POST['year'];
    $rating = (float) $_POST['rating'];

    $stmt = $conn->prepare(
        "INSERT INTO movies (title, genre, release_year, rating)
         VALUES (?, ?, ?, ?)"
    );

    $stmt->bind_param("ssid", $title, $genre, $year, $rating);
    $stmt->execute();
    $stmt->close();

    header("Location: index.php");
    exit;
}


if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];

    $stmt = $conn->prepare("DELETE FROM movies WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();

    header("Location: index.php");
    exit;
}


$search = trim($_GET['search'] ?? '');

$stmt = $conn->prepare(
    "SELECT * FROM movies
     WHERE title LIKE ?
     ORDER BY id DESC"
);

$keyword = "%$search%";
$stmt->bind_param("s", $keyword);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Movie Management System</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #171717;
            color: white;
            margin: 40px;
        }

        h1 {
            color: #ff4747;
        }

        input, button {
            padding: 10px;
            margin: 5px 0;
        }

        input {
            border-radius: 5px;
            border: none;
        }

        button {
            background: #ff4747;
            color: white;
            border: none;
            cursor: pointer;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: #262626;
        }

        th, td {
            padding: 12px;
            border: 1px solid #444;
            text-align: left;
        }

        th {
            background: #ff4747;
        }

        a {
            color: #ff7777;
        }

        form {
            margin-bottom: 15px;
        }
    </style>
</head>

<body>

<h1>🎬 Movie Management System</h1>
<p>Add and manage your favorite movies.</p>

<h3>Add New Movie</h3>

<form method="POST">
    <input type="text" name="title"
           placeholder="Movie title" required>

    <input type="text" name="genre"
           placeholder="Genre" required>

    <input type="number" name="year"
           placeholder="Release year"
           min="1888" max="2100" required>

    <input type="number" name="rating"
           placeholder="Rating (0-10)"
           min="0" max="10" step="0.1" required>

    <button type="submit" name="add">Add Movie</button>
</form>

<h3>Movie List</h3>

<form method="GET">
    <input type="text" name="search"
           placeholder="Search movie title..."
           value="<?= htmlspecialchars($search) ?>">

    <button type="submit">Search</button>
    <a href="index.php">Show All</a>
</form>


<table>
    <tr>
        <th>Poster</th>
        <th>Title</th>
        <th>Genre</th>
        <th>Year</th>
        <th>Rating</th>
        <th>Action</th>
    </tr>

    <?php while ($movie = $result->fetch_assoc()): ?>
    <tr>
        <td>
            <?php if (!empty($movie['poster'])): ?>
                <img
                    src="<?= htmlspecialchars($movie['poster']) ?>"
                    alt="Movie poster"
                    width="100"
                    style="height:140px; object-fit:cover;"
                >
            <?php else: ?>
                No poster
            <?php endif; ?>
        </td>

        <td><?= htmlspecialchars($movie['title']) ?></td>
        <td><?= htmlspecialchars($movie['genre']) ?></td>
        <td><?= $movie['release_year'] ?></td>
        <td><?= $movie['rating'] ?>/10</td>

        <td>
            <a href="index.php?delete=<?= $movie['id'] ?>"
               onclick="return confirm('Delete this movie?')">
                Delete
            </a>
        </td>
    </tr>
    <?php endwhile; ?>
</table>
