<?php
include('config.php');

/* DELETE PROJECT */
if(isset($_GET['delete'])){

$id = $_GET['delete'];

/* GET COVER IMAGE */
$get = mysqli_query($conn,"SELECT image FROM projects WHERE id=$id");
$data = mysqli_fetch_assoc($get);

if($data){
$image = $data['image'];

if(file_exists("admin/uploads/".$image)){
unlink("admin/uploads/".$image);
}
}

/* DELETE GALLERY IMAGES */
$imgs = mysqli_query($conn,"SELECT image FROM project_images WHERE project_id=$id");

while($row = mysqli_fetch_assoc($imgs)){
if(file_exists("admin/uploads/".$row['image'])){
unlink("admin/uploads/".$row['image']);
}
}

/* DELETE DB RECORDS */

mysqli_query($conn,"DELETE FROM project_images WHERE project_id=$id");
mysqli_query($conn,"DELETE FROM projects WHERE id=$id");

exit("deleted");
}


/* PAGINATION */

$limit = 3;
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$start = ($page-1)*$limit;


/* FILTER */

$filter = "";

if(isset($_GET['status']) && $_GET['status'] != "All"){

$status = $_GET['status'];
$filter = "WHERE status='$status'";

}


/* TOTAL */

$total = mysqli_query($conn,"SELECT COUNT(*) as total FROM projects $filter");
$totalRow = mysqli_fetch_assoc($total);
$totalPages = ceil($totalRow['total']/$limit);


/* GET PROJECTS */

$result = mysqli_query($conn,"SELECT * FROM projects $filter ORDER BY id DESC LIMIT $start,$limit");

?>

<!DOCTYPE html>
<html>

<head>

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Ayyavu Construction - Projects</title>

<style>

body{
margin:0;
font-family:Poppins;
background:#f5f5f5;
}

/* HEADER */

.header{
background:#111;
padding:18px 0;
}

.container{
width:90%;
margin:auto;
}

.nav{
display:flex;
justify-content:space-between;
align-items:center;
}

.logo{
color:#fff;
font-weight:bold;
font-size:20px;
}

.nav-links a{
color:#fff;
margin-left:25px;
text-decoration:none;
font-size:15px;
}

.nav-links a:hover{
color:#f39c12;
}

.cta-btn{
background:#f39c12;
padding:8px 15px;
border-radius:4px;
}

/* PROJECT SECTION */

.project-section{
padding:70px 0;
}

.project-title{
text-align:center;
font-size:34px;
margin-bottom:40px;
}

/* FILTER */

.filter-btns{
text-align:center;
margin-bottom:40px;
}

.filter-btns button{
padding:10px 20px;
border:none;
background:#eee;
border-radius:25px;
margin:5px;
cursor:pointer;
font-weight:500;
}

.filter-btns button:hover{
background:#2d6cdf;
color:#fff;
}

/* PROJECT CARD */

.project-card{

background:#fff;
margin-bottom:40px;
border-radius:12px;
overflow:hidden;
box-shadow:0 8px 25px rgba(0,0,0,0.1);

}

.project-images img{

width:100%;
height:260px;
object-fit:cover;
cursor:pointer;

}

.project-content{

padding:25px;

}

.project-content h3{
margin-top:0;
}

.projects-grid{
display:grid;
grid-template-columns:repeat(auto-fit,minmax(300px,1fr));
gap:30px;
margin-top:40px;
}

.project-card{
background:#fff;
border-radius:12px;
overflow:hidden;
box-shadow:0 5px 20px rgba(0,0,0,0.08);
transition:0.3s;
}

.project-card:hover{
transform:translateY(-5px);
}

.project-img{
width:100%;
height:220px;
object-fit:cover;
}

.project-info{
padding:15px;
}

.project-info h3{
margin:0;
font-size:18px;
}

.project-location{
font-size:14px;
color:#777;
margin-top:5px;
}

/* STATUS */

.status-badge{

padding:5px 12px;
border-radius:20px;
color:#fff;
font-size:13px;

}

.ongoing{background:#f39c12}
.completed{background:#27ae60}
.upcoming{background:#3498db}

/* DELETE */

.delete-btn{

margin-top:12px;
background:red;
color:#fff;
padding:6px 12px;
border:none;
cursor:pointer;

}

/* PAGINATION */

.pagination{
text-align:center;
margin-top:40px;
}

.pagination a{

padding:8px 14px;
margin:3px;
background:#ddd;
text-decoration:none;

}

.pagination a:hover{

background:#f39c12;
color:#fff;

}

/* LIGHTBOX */

#lightbox{

position:fixed;
top:0;
left:0;
width:100%;
height:100%;
background:rgba(0,0,0,0.8);
display:none;
justify-content:center;
align-items:center;

}

#lightbox img{

width:70%;
border-radius:10px;

}

/* FOOTER */

.footer{

background:#111;
color:#fff;
padding:40px 0;
margin-top:50px;

}

.footer-container{

width:90%;
margin:auto;
text-align:center;

}

</style>

</head>

<body>

<header class="header">

<div class="container">

<div class="nav">

<div class="logo">Ayyavu Construction</div>

<div class="nav-links">

<a href="index.php">Home</a>
<a href="projects.php">Projects</a>
<a href="contact.php" class="cta-btn">Consultation</a>

</div>

</div>

</div>

</header>



<section class="project-section">

<div class="container">

<h1 style="text-align:center;font-size:40px;margin-top:40px;">
Our Projects
</h1>

<p style="text-align:center;color:#777;width:60%;margin:auto;">
Building a legacy of excellence, one structure at a time. 
From luxury residential complexes to industrial hubs.
</p>


<div class="filter-btns">

<button onclick="filterStatus('All')">All</button>
<button onclick="filterStatus('Ongoing')">Ongoing</button>
<button onclick="filterStatus('Completed')">Completed</button>
<button onclick="filterStatus('Upcoming')">Upcoming</button>

</div>



<div class="projects-grid">

<?php while($row = mysqli_fetch_assoc($result)) { ?>

<div class="project-card">

<img class="project-img" src="admin/uploads/<?php echo $row['image']; ?>">

<div class="project-info">

<h3><?php echo $row['title']; ?></h3>

<div class="project-location">
<?php echo $row['location']; ?>
</div>

</div>

</div>

<?php } ?>

</div>


<div class="pagination">

<?php for($i=1;$i<=$totalPages;$i++){ ?>

<a href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>

<?php } ?>

</div>

</div>

</section>


<div id="lightbox" onclick="closeLightbox()">

<img id="lightbox-img">

</div>


<footer class="footer">

<div class="footer-container">

© <?php echo date("Y"); ?> Ayyavu Construction. All Rights Reserved.

</div>

</footer>



<script>

function filterStatus(status){

if(status=="All"){

window.location="projects.php";

}else{

window.location="projects.php?status="+status;

}

}


function openLightbox(src){

document.getElementById("lightbox").style.display="flex";
document.getElementById("lightbox-img").src=src;

}


function closeLightbox(){

document.getElementById("lightbox").style.display="none";

}


function deleteProject(id){

if(confirm("Delete this project?")){

fetch("projects.php?delete="+id)
.then(res=>res.text())
.then(data=>{

location.reload();

});

}

}

</script>

</body>
</html>