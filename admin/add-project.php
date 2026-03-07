<!DOCTYPE html>
<html>
<head>
    <title>Add Project</title>
</head>
<body>

<h2>Add New Project</h2>

<form action="save-project.php" method="POST" enctype="multipart/form-data">

    <input type="text" name="title" placeholder="Project Title" required><br><br>

    <textarea name="description" placeholder="Project Description"></textarea><br><br>

    <input type="file" name="images[]" multiple required><br><br>

    <button type="submit">Add Project</button>
    <br><br>
   <label>Project Details</label><br>
    <textarea name="details" rows="5" placeholder="Full Project Details"></textarea>

    <br><br>
    <label>Project Status</label><br>
    <select name="status" required>
        <option value="">Select Status</option>
        <option value="Ongoing">Ongoing</option>
        <option value="Completed">Completed</option>
        <option value="Upcoming">Upcoming</option>
</select>


</form>

</body>
</html>
