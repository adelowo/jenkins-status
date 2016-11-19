<?php

$url = rtrim(getopt("u:")["u"], '/');

if ($data = file_get_contents($url . "/api/json")) {

    $data = json_decode($data, true)["jobs"];

    $connection = new PDO("sqlite:data/status.sqlite", null, null);
    $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $statement = $connection->prepare(
        "INSERT INTO jenkins(job_name, uri, status,time_checked) VALUES (:job_name,:uri,:status,:time_checked)"
    );

    foreach ($data as $item) {
        $statement->bindValue(":job_name", $item['name']);
        $statement->bindValue(":uri", $item['url']);
        $statement->bindValue(":status", ($item['color'] === "blue" ? "successful" : "failure"));
        $statement->bindValue(":time_checked", (new DateTime())->format("Y-m-d H:i:s"));
        $statement->execute();
    }

    echo "All done";
} else {

    throw new Exception(
        "Couldn't authenticate. It should be something like http://lanre:mytoken_mytoken0@localhost:8080"
    );
}
