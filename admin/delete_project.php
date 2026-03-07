<?php
include "../config.php";

$id = $_GET['id'];

// 1. Get cover image from database
$sql = "SELECT image FROM projects WHERE id=$id";
$result = pg_query($conn, $sql);
$data = pg_fetch_assoc($result);

// 2. Delete cover image from uploads folder
if ($data && !empty($data['image']) && file_exists("uploads/" . $data['image'])) {
    unlink("uploads/" . $data['image']);
}

// 3. Get and delete project images
$imgs = pg_query($conn, "SELECT image FROM project_images WHERE project_id=$id");
while($row = pg_fetch_assoc($imgs)){
    if(file_exists("uploads/" . $row['image'])){
        unlink("uploads/" . $row['image']);
    }
}

// 4. Delete records from database
pg_query($conn, "DELETE FROM project_images WHERE project_id=$id");
pg_query($conn, "DELETE FROM projects WHERE id=$id");

header("Location: ../projects-new.php?admin=1");
?>
