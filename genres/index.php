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

	$stmt = $conn->prepare("SELECT * FROM genres WHERE id = :id");
	$stmt->bindParam(":id", $id, PDO::PARAM_INT);
	$stmt->execute();
	$result = $stmt->fetch(PDO::FETCH_ASSOC);

	if (!is_array($result)) {
		http_response_code(404);
		exit;
	}

	return $id;
}


# GET ALL GENRES
if($_SERVER["REQUEST_METHOD"] === "GET" && empty($_GET["id"]) && empty($_GET["songs"])) {
    $limit = isset($_GET["limit"]) ? intval($_GET["limit"]) : 10;
    $offset = isset($_GET["offset"]) ? intval($_GET["offset"]) : 0;

    $stmt = $conn->prepare("SELECT COUNT(id) FROM genres");
    $stmt->execute();
    $count = $stmt->fetch(PDO::FETCH_ASSOC);

    $stmt = $conn->prepare("SELECT * FROM genres LIMIT :limit OFFSET :offset");
    $stmt->bindParam(":limit", $limit, PDO::PARAM_INT);
    $stmt->bindParam(":offset", $offset, PDO::PARAM_INT);
    $stmt->execute();

    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $prevOffset = max(0, $offset - $limit);
    $nextOffset = $offset + $limit;

    $prev = "http://localhost:8888/php-music/genres?offset=$prevOffset&limit=$limit";
    $next = "http://localhost:8888/php-music/genres?offset=$nextOffset&limit=$limit";

    $output = [
        "count" => $count["COUNT(id)"],
        "prev" => ($prevOffset <= 0) ? null : $prev,
        "next" => ($nextOffset < $count["COUNT(id)"]) ? $next : null,
        "results" => [],
    ];

    for ($i = 0; $i < count($results); $i++) {
        $output["results"][] = ["name" => $results[$i]["name"], "url" => "http://localhost:8888/php-music/genres?id=" . $results[$i]["id"]];
        unset($results[$i]["id"]);
    }

    header("Content-Type: applicaition/json; charset=utf-8");
    echo json_encode($output);
}


# GET GENRE FROM ID
if($_SERVER["REQUEST_METHOD"] === "GET" && !empty($_GET["id"])) {
    $id = validateParam($_GET["id"]);

    $stmt = $conn->prepare("SELECT * FROM genres WHERE id = :id");
    $stmt->bindParam(":id", $id, PDO::PARAM_INT);
    $stmt->execute();

    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $output = [
        "id" => $results[0]["id"],
        "name" => $results[0]["name"],
        "songs_url" => "http://localhost:8888/php-music/genres?genre=" . $id,
    ];

    header("Content-Type: applicaition/json; charset=utf-8");
    echo json_encode($output);
}


# GET SONGS FROM GENRE
if($_SERVER["REQUEST_METHOD"] === "GET" && !empty($_GET["songs"])) {
    $songs = validateParam($_GET["songs"]);

    $stmt = $conn->prepare(
        "SELECT songs.id, songs.title, genres.name
        FROM song_genres
        LEFT JOIN genres ON genres.id = :genre
        LEFT JOIN songs ON songs.id = song_genres.song_id
        WHERE song_genres.genre_id = :genre"
    );
    $stmt->bindParam(":genre", $songs, PDO::PARAM_INT);
    $stmt->execute();

    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $output = [
        "genre" => $results[0]["name"],
        "genre_url" => "http://localhost:8888/php-music/genres?id=" . $songs,
        "results" => [],
    ];

    for ($i = 0; $i < count($results); $i++) {
        $output["results"][] = ["title" => $results[$i]["title"], "url" => "http://localhost:8888/php-music/artists?id=" . $results[$i]["id"]];
        unset($results[$i]["id"]);
        unset($results[$i]["name"]);
    }

    header("Content-Type: applicaition/json; charset=utf-8");
    echo json_encode($output);
}


# CREATE NEW GENRE
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (empty($_POST["name"])) {
        http_response_code(403);
        echo json_encode("name not included");
        exit;
    }

    $name = $_POST["name"];

    $stmt = $conn->prepare("INSERT INTO genres (`name`)
                            VALUES (:name)");

    $stmt->bindParam(":name", $name);

    $stmt->execute();
    http_response_code(201);
}


# EDIT EXISTING GENRE
if ($_SERVER["REQUEST_METHOD"] === "PUT") {
    $id = validateParam($_GET["id"]);

    parse_str(file_get_contents("php://input"), $body);

    if (empty($body["name"])) {
        http_response_code(403);
        echo json_encode("name not included");
        exit;
    }

    $stmt = $conn->prepare("UPDATE genres 
                            SET name = :name
                            WHERE id = :id");

    $stmt->bindParam(":id", $id, PDO::PARAM_INT);
    $stmt->bindParam(":name", $body["name"]);

    $stmt->execute();

    $stmt = $conn->prepare("SELECT * FROM genres WHERE id = :id");
    $stmt->bindParam(":id", $id, PDO::PARAM_INT);
    $stmt->execute();

    header("Content-Type: applicaition/json; charset=utf-8");
    http_response_code(200);
    echo json_encode($stmt->fetch(PDO::FETCH_ASSOC));
}


# DELETE GENRE
if ($_SERVER["REQUEST_METHOD"] === "DELETE") {
    if (empty($_GET["id"])) {
        http_response_code(400);
        exit;
    }
    $id = $_GET["id"];

    $stmt = $conn->prepare("DELETE FROM genres WHERE id = :id");
    $stmt->bindParam(":id", $id, PDO::PARAM_INT);

    $stmt->execute();
    http_response_code(204);
}