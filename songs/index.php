<?php
require("../db.php");

function validateParam($param) {
    global $conn;

    if (empty($param)) {
		http_response_code(400);
		exit;
	}

	$id = $param;

	if (!is_numeric($id)) {
		header("Content-Type: application/json; charset=utf-8");
		http_response_code(400);
		echo json_encode(["message" => "ID is malformed"]);
		exit;
	}

	$id = intval($id, 10);

	$stmt = $conn->prepare("SELECT * FROM songs WHERE id = :id");
	$stmt->bindParam(":id", $id, PDO::PARAM_INT);
	$stmt->execute();
	$result = $stmt->fetch(PDO::FETCH_ASSOC);

	if (!is_array($result)) {
		http_response_code(404);
		exit;
	}

	return $id;
}

function checkAuth() {
    $headers = apache_request_headers();

    if (!isset($headers["X-Authorization"])) {
        http_response_code(401);
		exit;
    }

    if ($headers["X-Authorization"] !== "1234") {
        http_response_code(403);
		exit;
    }
}


# GET ALL SONGS
if($_SERVER["REQUEST_METHOD"] === "GET" && empty($_GET["id"]) && empty($_GET["genres"])) {
    $limit = isset($_GET["limit"]) ? intval($_GET["limit"]) : 10;
    $offset = isset($_GET["offset"]) ? intval($_GET["offset"]) : 0;

    $stmt = $conn->prepare("SELECT COUNT(id) FROM songs");
    $stmt->execute();
    $count = $stmt->fetch(PDO::FETCH_ASSOC);

    $stmt = $conn->prepare("SELECT * FROM songs LIMIT :limit OFFSET :offset");
    $stmt->bindParam(":limit", $limit, PDO::PARAM_INT);
    $stmt->bindParam(":offset", $offset, PDO::PARAM_INT);
    $stmt->execute();

    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $prevOffset = max(0, $offset - $limit);
    $nextOffset = $offset + $limit;

    $prev = "http://localhost:8888/php-music/songs?offset=$prevOffset&limit=$limit";
    $next = "http://localhost:8888/php-music/songs?offset=$nextOffset&limit=$limit";

    $output = [
        "count" => $count["COUNT(id)"],
        "prev" => ($prevOffset <= 0) ? null : $prev,
        "next" => ($nextOffset < $count["COUNT(id)"]) ? $next : null,
        "results" => [],
    ];

    for ($i = 0; $i < count($results); $i++) {
        $output["results"][] = ["title" => $results[$i]["title"], "url" => "http://localhost:8888/php-music/songs?id=" . $results[$i]["id"]];
        unset($results[$i]["id"]);
        unset($results[$i]["track_number"]);
        unset($results[$i]["release_id"]);
    }

    header("Content-Type: applicaition/json; charset=utf-8");
    echo json_encode($output);
}


# GET SONG FROM ID
if($_SERVER["REQUEST_METHOD"] === "GET" && !empty($_GET["id"])) {
    $id = validateParam($_GET["id"]);

    $stmt = $conn->prepare(
        "SELECT songs.id, songs.title, songs.track_number, songs.release_id,
        artists.id AS artist_id, artists.name AS artist_name, song_artists.featured_artist
        FROM songs
        LEFT JOIN song_artists ON song_artists.song_id = songs.id
        LEFT JOIN artists ON artists.id = song_artists.artist_id
        WHERE songs.id = :id"
    );
    $stmt->bindParam(":id", $id, PDO::PARAM_INT);
    $stmt->execute();

    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $output = [
        "id" => $results[0]["id"],
        "title" => $results[0]["title"],
        "track_number" => $results[0]["track_number"],
        "release_url" => "http://localhost:8888/php-music/releases?id=" . $results[0]["release_id"],
        "genres_url" => "http://localhost:8888/php-music/songs?genres=" . $results[0]["id"],
        "artists" => [],
    ];

    for ($i = 0; $i < count($results); $i++) {
		$output["artists"][] = ["name" => $results[$i]["artist_name"], "featured_artist" => $results[$i]["featured_artist"], "url" => "http://localhost:8888/php-music/artists?id=" . $results[$i]["artist_id"]];
	}

    header("Content-Type: applicaition/json; charset=utf-8");
    echo json_encode($output);
}


# GET GENRES FROM SONG
if($_SERVER["REQUEST_METHOD"] === "GET" && !empty($_GET["genres"])) {
    $genres = validateParam($_GET["genres"]);

    $stmt = $conn->prepare(
        "SELECT genres.id, genres.name, songs.title
        FROM song_genres
        LEFT JOIN genres ON genres.id = song_genres.genre_id
        LEFT JOIN songs ON songs.id = song_genres.song_id
        WHERE song_genres.song_id = :song"
    );
    $stmt->bindParam(":song", $genres, PDO::PARAM_INT);
    $stmt->execute();

    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $output = [
        "song" => $results[0]["title"],
        "song_url" => "http://localhost:8888/php-music/songs?id=" . $genres,
        "results" => [],
    ];

    for ($i = 0; $i < count($results); $i++) {
        $output["results"][] = ["name" => $results[$i]["name"], "url" => "http://localhost:8888/php-music/genres?id=" . $results[$i]["id"]];
        unset($results[$i]["id"]);
        unset($results[$i]["name"]);
    }

    header("Content-Type: applicaition/json; charset=utf-8");
    echo json_encode($output);
}



# CREATE NEW SONG
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    checkAuth();

    if (empty($_POST["title"])) {
        http_response_code(403);
        echo json_encode("title not included");
        exit;
    }
    if (empty($_POST["track_number"])) {
        http_response_code(403);
        echo json_encode("track_number not included");
        exit;
    }
    if (empty($_POST["release_id"])) {
        http_response_code(403);
        echo json_encode("release_id not included");
        exit;
    }

    $title = $_POST["title"];
    $track_number = $_POST["track_number"];
    $release_id = $_POST["release_id"];

    $stmt = $conn->prepare("INSERT INTO songs (`title`, `track_number`, `release_id`)
                            VALUES (:title, :track_number, :release_id)");

    $stmt->bindParam(":title", $title);
    $stmt->bindParam(":track_number", $track_number);
    $stmt->bindParam(":release_id", $release_id);

    $stmt->execute();
    http_response_code(201);
}


# EDIT EXISTING SONG
if ($_SERVER["REQUEST_METHOD"] === "PUT") {
    checkAuth();
    $id = validateParam($_GET["id"]);

    parse_str(file_get_contents("php://input"), $body);

    if (empty($body["title"])) {
        http_response_code(403);
        echo json_encode("title not included");
        exit;
    }
    if (empty($body["track_number"])) {
        http_response_code(403);
        echo json_encode("title not included");
        exit;
    }
    if (empty($body["release_id"])) {
        http_response_code(403);
        echo json_encode("title not included");
        exit;
    }

    $stmt = $conn->prepare("UPDATE songs 
                            SET title = :title, track_number = :track_number, release_id = :release_id
                            WHERE id = :id");

    $stmt->bindParam(":id", $id, PDO::PARAM_INT);
    $stmt->bindParam(":title", $body["title"]);
    $stmt->bindParam(":track_number", $body["track_number"]);
    $stmt->bindParam(":release_id", $body["release_id"]);

    $stmt->execute();

    $stmt = $conn->prepare("SELECT * FROM songs WHERE id = :id");
    $stmt->bindParam(":id", $id, PDO::PARAM_INT);
    $stmt->execute();

    header("Content-Type: applicaition/json; charset=utf-8");
    http_response_code(200);
    echo json_encode($stmt->fetch(PDO::FETCH_ASSOC));
}


# DELETE SONG
if ($_SERVER["REQUEST_METHOD"] === "DELETE") {
    checkAuth();
    $id = validateParam($_GET["id"]);

    $stmt = $conn->prepare("DELETE FROM songs WHERE id = :id");
    $stmt->bindParam(":id", $id, PDO::PARAM_INT);

    $stmt->execute();
    http_response_code(204);
}