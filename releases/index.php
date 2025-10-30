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

	$stmt = $conn->prepare("SELECT * FROM releases WHERE id = :id");
	$stmt->bindParam(":id", $id, PDO::PARAM_INT);
	$stmt->execute();
	$result = $stmt->fetch(PDO::FETCH_ASSOC);

	if (!is_array($result)) {
		http_response_code(404);
		exit;
	}

	return $id;
}


# GET ALL RELEASES
if($_SERVER["REQUEST_METHOD"] === "GET" && empty($_GET["id"]) && empty($_GET["tracklist"])) {
    $limit = isset($_GET["limit"]) ? intval($_GET["limit"]) : 10;
    $offset = isset($_GET["offset"]) ? intval($_GET["offset"]) : 0;

    $stmt = $conn->prepare("SELECT COUNT(id) FROM releases");
    $stmt->execute();
    $count = $stmt->fetch(PDO::FETCH_ASSOC);

    $stmt = $conn->prepare("SELECT * FROM releases LIMIT :limit OFFSET :offset");
    $stmt->bindParam(":limit", $limit, PDO::PARAM_INT);
    $stmt->bindParam(":offset", $offset, PDO::PARAM_INT);
    $stmt->execute();

    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $prevOffset = max(0, $offset - $limit);
    $nextOffset = $offset + $limit;

    $prev = "http://localhost:8888/php-music/releases?offset=$prevOffset&limit=$limit";
    $next = "http://localhost:8888/php-music/releases?offset=$nextOffset&limit=$limit";

    $output = [
        "count" => $count["COUNT(id)"],
        "prev" => ($prevOffset <= 0) ? null : $prev,
        "next" => ($nextOffset < $count["COUNT(id)"]) ? $next : null,
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


# GET RELEASE FROM ID
if($_SERVER["REQUEST_METHOD"] === "GET" && !empty($_GET["id"])) {
    $id = validateParam($_GET["id"]);

    $stmt = $conn->prepare(
        "SELECT releases.id, releases.title, releases.release_year, releases.release_type, releases.artwork_url,
        artists.id AS artist_id, artists.name AS artist_name
        FROM releases
        LEFT JOIN release_artists ON release_artists.release_id = :id
        LEFT JOIN artists ON artists.id = release_artists.artist_id
        WHERE releases.id = :id"
    );
    $stmt->bindParam(":id", $id, PDO::PARAM_INT);
    $stmt->execute();

    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $output = [
        "id" => $results[0]["id"],
        "title" => $results[0]["title"],
        "release_year" => $results[0]["release_year"],
        "release_type" => $results[0]["release_type"],
        "artwork_url" => $results[0]["artwork_url"],
        "artists" => [],
        "tracklist_url" => "http://localhost:8888/php-music/releases?tracklist=" . $id,
    ];

    for ($i = 0; $i < count($results); $i++) {
		$output["artists"][] = ["name" => $results[$i]["artist_name"], "url" => "http://localhost:8888/php-music/artists?id=" . $results[$i]["artist_id"]];
	}

    header("Content-Type: applicaition/json; charset=utf-8");
    echo json_encode($output);
}


# GET SONGS FROM RELEASE
if($_SERVER["REQUEST_METHOD"] === "GET" && !empty($_GET["tracklist"])) {
    $tracklist = validateParam($_GET["tracklist"]);

    $stmt = $conn->prepare(
        "SELECT songs.id, songs.title, songs.track_number, releases.title AS release_title
        FROM songs
        LEFT JOIN releases ON releases.id = songs.release_id
        WHERE songs.release_id = :release
        ORDER BY songs.track_number ASC"
    );
    $stmt->bindParam(":release", $tracklist, PDO::PARAM_INT);
    $stmt->execute();

    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $output = [
        "release" => $results[0]["release_title"],
        "release_url" => "http://localhost:8888/php-music/releases?id=" . $tracklist,
        "results" => [],
    ];

    for ($i = 0; $i < count($results); $i++) {
        $output["results"][] = ["title" => $results[$i]["title"], "url" => "http://localhost:8888/php-music/songs?id=" . $results[$i]["id"]];
        unset($results[$i]["id"]);
        unset($results[$i]["name"]);
    }

    header("Content-Type: applicaition/json; charset=utf-8");
    echo json_encode($output);
}




# CREATE NEW RELEASE
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (empty($_POST["title"])) {
        http_response_code(403);
        echo json_encode("title not included");
        exit;
    }
    if (empty($_POST["release_year"])) {
        http_response_code(403);
        echo json_encode("title not included");
        exit;
    }
    if (empty($_POST["release_type"])) {
        http_response_code(403);
        echo json_encode("title not included");
        exit;
    }
    if (empty($_POST["artwork_url"])) {
        http_response_code(403);
        echo json_encode("title not included");
        exit;
    }

    $title = $_POST["title"];
    $release_year = $_POST["release_year"];
    $release_type = $_POST["release_type"];
    $artwork_url = $_POST["artwork_url"];

    $stmt = $conn->prepare("INSERT INTO releases (`title`, `release_year`, `release_type`, `artwork_url`)
                            VALUES (:title, :release_year, :release_type, :artwork_url)");

    $stmt->bindParam(":title", $title);
    $stmt->bindParam(":release_year", $release_year);
    $stmt->bindParam(":release_type", $release_type);
    $stmt->bindParam(":artwork_url", $artwork_url);

    $stmt->execute();
    http_response_code(201);
}


# EDIT EXISTING RELEASE
if ($_SERVER["REQUEST_METHOD"] === "PUT") {
    $id = validateParam($_GET["id"]);

    parse_str(file_get_contents("php://input"), $body);

    if (empty($body["title"])) {
        http_response_code(403);
        echo json_encode("title not included");
        exit;
    }
    if (empty($body["release_year"])) {
        http_response_code(403);
        echo json_encode("release_year not included");
        exit;
    }
    if (empty($body["release_type"])) {
        http_response_code(403);
        echo json_encode("release_type not included");
        exit;
    }
    if (empty($body["artwork_url"])) {
        http_response_code(403);
        echo json_encode("artwork_url not included");
        exit;
    }

    $stmt = $conn->prepare("UPDATE releases 
                            SET title = :title, release_year = :release_year, release_type = :release_type, artwork_url = :artwork_url
                            WHERE id = :id");

    $stmt->bindParam(":id", $id, PDO::PARAM_INT);
    $stmt->bindParam(":title", $body["title"]);
    $stmt->bindParam(":release_year", $body["release_year"]);
    $stmt->bindParam(":release_type", $body["release_type"]);
    $stmt->bindParam(":artwork_url", $body["artwork_url"]);

    $stmt->execute();

    $stmt = $conn->prepare("SELECT * FROM releases WHERE id = :id");
    $stmt->bindParam(":id", $id, PDO::PARAM_INT);
    $stmt->execute();

    header("Content-Type: applicaition/json; charset=utf-8");
    http_response_code(200);
    echo json_encode($stmt->fetch(PDO::FETCH_ASSOC));
}


# DELETE RELEASE
if ($_SERVER["REQUEST_METHOD"] === "DELETE") {
    if (empty($_GET["id"])) {
        http_response_code(400);
        exit;
    }
    $id = $_GET["id"];

    $stmt = $conn->prepare("DELETE FROM releases WHERE id = :id");
    $stmt->bindParam(":id", $id, PDO::PARAM_INT);

    $stmt->execute();
    http_response_code(204);
}