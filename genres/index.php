<?php
require("../db.php");

function validateID() {
    global $conn;

    if (empty($_GET["id"])) {
		http_response_code(400);
		exit;
	}

	$id = $_GET["id"];

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
if($_SERVER["REQUEST_METHOD"] === "GET" && empty($_GET["id"])) {
    $stmt = $conn->prepare("SELECT * FROM genres");
    $stmt->execute();

    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    for ($i = 0; $i < count($results); $i++) {
        $results[$i]["url"] = "http://localhost:8888/php-music/genres?id=" . $results[$i]["id"];
        unset($results[$i]["id"]);
    }

    header("Content-Type: applicaition/json; charset=utf-8");
    $output = ["results" => $results];
    echo json_encode($output);
}


# GET GENRE FROM ID
if($_SERVER["REQUEST_METHOD"] === "GET" && !empty($_GET["id"])) {
    $id = validateID();

    $stmt = $conn->prepare("SELECT * FROM genres WHERE id = :id");
    $stmt->bindParam(":id", $id, PDO::PARAM_INT);
    $stmt->execute();

    $results = $stmt->fetch(PDO::FETCH_ASSOC);

    header("Content-Type: applicaition/json; charset=utf-8");
    echo json_encode($results);
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
    $id = validateID();

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