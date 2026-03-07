<?php
include "config.php";

$id = $_GET['id'];

// 1. Get image names from database
$sql = "SELECT image1, image2, image3, image4, image5 FROM projects WHERE id=$id";
$result = mysqli_query($conn, $sql);
$data = mysqli_fetch_assoc($result);

// 2. Delete images from uploads folder
$images = [
    $data['image1'],
    $data['image2'],
    $data['image3'],
    $data['image4'],
    $data['image5']
];

foreach ($images as $img) {
    if (!empty($img) && file_exists("uploads/" . $img)) {
        unlink("uploads/" . $img);
    }
}

// 3. Delete record from database
$delete = "DELETE FROM projects WHERE id=$id";
mysqli_query($conn, $delete);

header("Location: admin_projects.php");
?>
