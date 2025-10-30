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

	$stmt = $conn->prepare("SELECT * FROM artists WHERE id = :id");
	$stmt->bindParam(":id", $id, PDO::PARAM_INT);
	$stmt->execute();
	$result = $stmt->fetch(PDO::FETCH_ASSOC);

	if (!is_array($result)) {
		http_response_code(404);
		exit;
	}

	return $id;
}


# GET ALL ARTISTS
if($_SERVER["REQUEST_METHOD"] === "GET" && empty($_GET["id"]) && empty($_GET["discography"])) {
    $limit = isset($_GET["limit"]) ? intval($_GET["limit"]) : 10;
    $offset = isset($_GET["offset"]) ? intval($_GET["offset"]) : 0;

    $stmt = $conn->prepare("SELECT COUNT(id) FROM artists");
    $stmt->execute();
    $count = $stmt->fetch(PDO::FETCH_ASSOC);

    $stmt = $conn->prepare("SELECT * FROM artists LIMIT :limit OFFSET :offset");
    $stmt->bindParam(":limit", $limit, PDO::PARAM_INT);
    $stmt->bindParam(":offset", $offset, PDO::PARAM_INT);
    $stmt->execute();

    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $prevOffset = max(0, $offset - $limit);
    $nextOffset = $offset + $limit;

    $prev = "http://localhost:8888/php-music/artists?offset=$prevOffset&limit=$limit";
    $next = "http://localhost:8888/php-music/artists?offset=$nextOffset&limit=$limit";

    $output = [
        "count" => $count["COUNT(id)"],
        "prev" => ($prevOffset <= 0) ? null : $prev,
        "next" => ($nextOffset < $count["COUNT(id)"]) ? $next : null,
        "results" => [],
    ];

    for ($i = 0; $i < count($results); $i++) {
        $output["results"][] = ["name" => $results[$i]["name"], "url" => "http://localhost:8888/php-music/artists?id=" . $results[$i]["id"]];
        unset($results[$i]["id"]);
    }

    header("Content-Type: applicaition/json; charset=utf-8");
    echo json_encode($output);
}


# GET ARTIST FROM ID
if($_SERVER["REQUEST_METHOD"] === "GET" && !empty($_GET["id"])) {
    $id = validateParam($_GET["id"]);

    $stmt = $conn->prepare(
        "SELECT artists.id, artists.name,
        releases.id AS release_id, releases.title AS release_title
        FROM artists
        LEFT JOIN release_artists ON release_artists.artist_id = :id
        LEFT JOIN releases ON releases.id = release_artists.release_id
        WHERE artists.id = :id"
    );
    $stmt->bindParam(":id", $id, PDO::PARAM_INT);
    $stmt->execute();

    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $output = [
        "id" => $results[0]["id"],
        "name" => $results[0]["name"],
        "discography_url" => "http://localhost:8888/php-music/artists?discography=" . $id,
    ];

    header("Content-Type: applicaition/json; charset=utf-8");
    echo json_encode($output);
}


# GET RELEASES FROM ARTIST
if($_SERVER["REQUEST_METHOD"] === "GET" && !empty($_GET["discography"])) {
    $discography = validateParam($_GET["discography"]);

    $stmt = $conn->prepare(
        "SELECT releases.id, releases.title, releases.release_year, releases.release_type, releases.artwork_url, artists.name
        FROM release_artists
        LEFT JOIN releases ON releases.id = release_artists.release_id
        LEFT JOIN artists ON artists.id = release_artists.artist_id
        WHERE release_artists.artist_id = :artist
        ORDER BY releases.release_year ASC"
    );
    $stmt->bindParam(":artist", $discography, PDO::PARAM_INT);
    $stmt->execute();

    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $output = [
        "artist" => $results[0]["name"],
        "artist_url" => "http://localhost:8888/php-music/artists?id=" . $discography,
        "results" => [],
    ];

    for ($i = 0; $i < count($results); $i++) {
        $output["results"][] = ["title" => $results[$i]["title"], "url" => "http://localhost:8888/php-music/releases?id=" . $results[$i]["id"]];
        unset($results[$i]["id"]);
        unset($results[$i]["release_year"]);
        unset($results[$i]["release_type"]);
        unset($results[$i]["artwork_url"]);
    }

    header("Content-Type: applicaition/json; charset=utf-8");
    echo json_encode($output);
}


# CREATE NEW ARTIST
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (empty($_POST["name"])) {
        http_response_code(403);
        echo json_encode("name not included");
        exit;
    }

    $name = $_POST["name"];

    $stmt = $conn->prepare("INSERT INTO artists (`name`)
                            VALUES (:name)");

    $stmt->bindParam(":name", $name);

    $stmt->execute();
    http_response_code(201);
}


# EDIT EXISTING ARTIST
if ($_SERVER["REQUEST_METHOD"] === "PUT") {
    $id = validateParam($_GET["id"]);

    parse_str(file_get_contents("php://input"), $body);

    if (empty($body["name"])) {
        http_response_code(403);
        echo json_encode("name not included");
        exit;
    }

    $stmt = $conn->prepare("UPDATE artists 
                            SET name = :name
                            WHERE id = :id");

    $stmt->bindParam(":id", $id, PDO::PARAM_INT);
    $stmt->bindParam(":name", $body["name"]);

    $stmt->execute();

    $stmt = $conn->prepare("SELECT * FROM artists WHERE id = :id");
    $stmt->bindParam(":id", $id, PDO::PARAM_INT);
    $stmt->execute();

    header("Content-Type: applicaition/json; charset=utf-8");
    http_response_code(200);
    echo json_encode($stmt->fetch(PDO::FETCH_ASSOC));
}


# DELETE ARTIST
if ($_SERVER["REQUEST_METHOD"] === "DELETE") {
    if (empty($_GET["id"])) {
        http_response_code(400);
        exit;
    }
    $id = $_GET["id"];

    $stmt = $conn->prepare("DELETE FROM artists WHERE id = :id");
    $stmt->bindParam(":id", $id, PDO::PARAM_INT);

    $stmt->execute();
    http_response_code(204);
}