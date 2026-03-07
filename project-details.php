<?php
include("config.php");

$id = $_GET['id'];

$project = pg_query($conn,"SELECT * FROM projects WHERE id=$id");
$data = pg_fetch_assoc($project);

$images = pg_query($conn,"SELECT * FROM project_images WHERE project_id=$id");
?>

<!DOCTYPE html>
<html>
<head>
<title><?php echo $data['title']; ?></title>

<style>

body{
font-family:Poppins;
margin:0;
background:#f5f5f5;
}

.container{
width:90%;
margin:auto;
padding:40px 0;
}

.project-title{
font-size:32px;
margin-bottom:10px;
}

.status{
display:inline-block;
padding:6px 12px;
border-radius:20px;
color:#fff;
font-size:13px;
}

.ongoing{background:orange;}
.completed{background:green;}
.upcoming{background:blue;}

.gallery{
display:grid;
grid-template-columns:repeat(auto-fit,minmax(250px,1fr));
gap:15px;
margin-top:30px;
}

.gallery img{
width:100%;
height:200px;
object-fit:cover;
border-radius:8px;
cursor:pointer;
}

.details{
margin-top:30px;
line-height:1.7;
}

</style>
</head>

<body>

<div class="container">

<h1 class="project-title"><?php echo $data['title']; ?></h1>

<span class="status <?php echo strtolower($data['status']); ?>">
<?php echo $data['status']; ?>
</span>

<div class="gallery">

<?php while($img = pg_fetch_assoc($images)) { ?>

<img src="admin/uploads/<?php echo $img['image']; ?>">

<?php } ?>

</div>

<div class="details">

<h3>Project Details</h3>

<p><?php echo $data['details']; ?></p>

</div>

</div>

</body>
</html>