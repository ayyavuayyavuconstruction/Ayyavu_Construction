<?php
include("../config.php");

$id = $_GET['id'];
$project = pg_query($conn, "SELECT * FROM projects WHERE id=$id");
$data = pg_fetch_assoc($project);

if($_POST) {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $details = $_POST['details'];
    $status = $_POST['status'];
    $location = $_POST['location'];

    // Handle image upload if new image is provided
    if(!empty($_FILES['image']['name'])) {
        // Delete old image
        if(file_exists("uploads/" . $data['image'])) {
            unlink("uploads/" . $data['image']);
        }

        $image = $_FILES['image']['name'];
        $newName = time() . "_" . $image;
        move_uploaded_file($_FILES['image']['tmp_name'], "uploads/" . $newName);

        $updateQuery = "UPDATE projects SET title='$title', description='$description', details='$details', status='$status', location='$location', image='$newName' WHERE id=$id";
    } else {
        $updateQuery = "UPDATE projects SET title='$title', description='$description', details='$details', status='$status', location='$location' WHERE id=$id";
    }

    pg_query($conn, $updateQuery);
    header("Location: ../projects-new.php?admin=1");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Project - Ayyavu Construction</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #f8fafc;
            padding: 2rem;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 1rem;
            padding: 2rem;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        }

        .header {
            text-align: center;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #e2e8f0;
        }

        .header h1 {
            color: #1a202c;
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }

        .header p {
            color: #718096;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #374151;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
            font-size: 1rem;
            transition: border-color 0.3s;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        .form-group textarea {
            min-height: 120px;
            resize: vertical;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        .current-image {
            max-width: 200px;
            border-radius: 0.5rem;
            margin-top: 0.5rem;
        }

        .button-group {
            display: flex;
            gap: 1rem;
            justify-content: center;
            margin-top: 2rem;
        }

        .btn {
            padding: 0.75rem 2rem;
            border: none;
            border-radius: 0.5rem;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            text-align: center;
            transition: all 0.3s;
        }

        .btn-primary {
            background: #2563eb;
            color: white;
        }

        .btn-primary:hover {
            background: #1d4ed8;
        }

        .btn-secondary {
            background: #6b7280;
            color: white;
        }

        .btn-secondary:hover {
            background: #4b5563;
        }

        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
            }
            
            .button-group {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Edit Project</h1>
            <p>Update project information and details</p>
        </div>

        <form method="POST" enctype="multipart/form-data">
            <div class="form-row">
                <div class="form-group">
                    <label>Project Title</label>
                    <input type="text" name="title" value="<?php echo htmlspecialchars($data['title']); ?>" required>
                </div>

                <div class="form-group">
                    <label>Location</label>
                    <input type="text" name="location" value="<?php echo htmlspecialchars($data['location'] ?? ''); ?>" placeholder="Chennai, Tamil Nadu">
                </div>
            </div>

            <div class="form-group">
                <label>Project Description</label>
                <textarea name="description" required><?php echo htmlspecialchars($data['description']); ?></textarea>
            </div>

            <div class="form-group">
                <label>Project Details</label>
                <textarea name="details"><?php echo htmlspecialchars($data['details']); ?></textarea>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Project Status</label>
                    <select name="status" required>
                        <option value="COMPLETED" <?php echo $data['status'] == 'COMPLETED' ? 'selected' : ''; ?>>Completed</option>
                        <option value="ONGOING" <?php echo $data['status'] == 'ONGOING' ? 'selected' : ''; ?>>Ongoing</option>
                        <option value="UPCOMING" <?php echo $data['status'] == 'UPCOMING' ? 'selected' : ''; ?>>Upcoming</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Update Cover Image (Optional)</label>
                    <input type="file" name="image" accept="image/*">
                </div>
            </div>

            <div class="form-group">
                <label>Current Cover Image</label>
                <img src="uploads/<?php echo $data['image']; ?>" alt="Current Image" class="current-image">
            </div>

            <div class="button-group">
                <button type="submit" class="btn btn-primary">Update Project</button>
                <a href="../projects-new.php?admin=1" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</body>
</html>