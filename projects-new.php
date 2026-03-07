<?php
include('config.php');

// Handle project deletion
if(isset($_GET['delete']) && isset($_GET['admin'])) {
    $id = $_GET['delete'];
    
    // Get cover image
    $get = pg_query($conn, "SELECT image FROM projects WHERE id=$id");
    $data = pg_fetch_assoc($get);

    if($data) {
        $image = $data['image'];
        if(file_exists("admin/uploads/".$image)) {
            unlink("admin/uploads/".$image);
        }
    }

    // Delete gallery images
    $imgs = pg_query($conn, "SELECT image FROM project_images WHERE project_id=$id");
    while($row = pg_fetch_assoc($imgs)) {
        if(file_exists("admin/uploads/".$row['image'])) {
            unlink("admin/uploads/".$row['image']);
        }
    }

    // Delete DB records
    pg_query($conn, "DELETE FROM project_images WHERE project_id=$id");
    pg_query($conn, "DELETE FROM projects WHERE id=$id");
    
    header("Location: projects-new.php?admin=1");
    exit();
}

// Pagination
$limit = 6;
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$start = ($page-1) * $limit;

// Filter
$filter = "";
if(isset($_GET['status']) && $_GET['status'] != "ALL PROJECTS") {
    $status = $_GET['status'];
    $filter = "WHERE status='$status'";
}

// Total count
$total = pg_query($conn, "SELECT COUNT(*) as total FROM projects $filter");
$totalRow = pg_fetch_assoc($total);
$totalPages = ceil($totalRow['total'] / $limit);

// Get projects
$result = pg_query($conn, "SELECT * FROM projects $filter ORDER BY id DESC OFFSET $start LIMIT $limit");

$isAdmin = isset($_GET['admin']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our Projects - Ayyavu Construction</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: #f8fafc;
            color: #1a202c;
            line-height: 1.6;
        }

        /* Header */
        .header {
            background: white;
            padding: 1rem 0;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .nav-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 700;
            font-size: 1.25rem;
            color: #2563eb;
        }

        .logo::before {
            content: "🏗️";
            font-size: 1.5rem;
        }

        .nav-links {
            display: flex;
            gap: 2rem;
            list-style: none;
        }

        .nav-links a {
            text-decoration: none;
            color: #4a5568;
            font-weight: 500;
            transition: color 0.3s;
        }

        .nav-links a:hover,
        .nav-links a.active {
            color: #2563eb;
        }

        .cta-button {
            background: #2563eb;
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 0.5rem;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
        }

        .cta-button:hover {
            background: #1d4ed8;
            transform: translateY(-1px);
        }

        /* Main Content */
        .main-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 3rem 2rem;
        }

        .page-header {
            text-align: center;
            margin-bottom: 3rem;
        }

        .page-title {
            font-size: 2.5rem;
            font-weight: 800;
            color: #1a202c;
            margin-bottom: 1rem;
        }

        .page-subtitle {
            font-size: 1.125rem;
            color: #718096;
            max-width: 600px;
            margin: 0 auto;
        }

        /* Filter Tabs */
        .filter-tabs {
            display: flex;
            justify-content: center;
            gap: 0;
            margin-bottom: 3rem;
            background: white;
            border-radius: 0.75rem;
            padding: 0.5rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            flex-wrap: wrap;
        }

        .filter-tab {
            padding: 0.75rem 1.5rem;
            background: transparent;
            border: none;
            border-radius: 0.5rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            color: #718096;
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .filter-tab.active,
        .filter-tab:hover {
            background: #2563eb;
            color: white;
        }

        /* Admin Controls */
        .admin-controls {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            padding: 1rem;
            background: #fef3c7;
            border-radius: 0.5rem;
            border-left: 4px solid #f59e0b;
        }

        .admin-badge {
            background: #f59e0b;
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 1rem;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .add-project-btn {
            background: #10b981;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
        }

        .add-project-btn:hover {
            background: #059669;
        }

        /* Projects Grid */
        .projects-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 2rem;
            margin-bottom: 3rem;
        }

        .project-card {
            background: white;
            border-radius: 1rem;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            transition: all 0.3s;
            position: relative;
        }

        .project-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 25px rgba(0,0,0,0.1);
        }

        .project-image {
            width: 100%;
            height: 240px;
            object-fit: cover;
            background: #e2e8f0;
        }

        .project-status {
            position: absolute;
            top: 1rem;
            left: 1rem;
            padding: 0.25rem 0.75rem;
            border-radius: 1rem;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .status-completed {
            background: #10b981;
            color: white;
        }

        .status-ongoing {
            background: #f59e0b;
            color: white;
        }

        .status-upcoming {
            background: #3b82f6;
            color: white;
        }

        .project-content {
            padding: 1.5rem;
        }

        .project-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: #1a202c;
            margin-bottom: 0.5rem;
        }

        .project-location {
            display: flex;
            align-items: center;
            gap: 0.25rem;
            color: #718096;
            font-size: 0.875rem;
            margin-bottom: 1rem;
        }

        .project-location::before {
            content: "📍";
        }

        .project-description {
            color: #4a5568;
            font-size: 0.875rem;
            line-height: 1.6;
            margin-bottom: 1rem;
        }

        .project-actions {
            display: flex;
            gap: 0.5rem;
            justify-content: space-between;
            align-items: center;
        }

        .view-details {
            color: #2563eb;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.875rem;
            transition: color 0.3s;
        }

        .view-details:hover {
            color: #1d4ed8;
        }

        .admin-actions {
            display: flex;
            gap: 0.5rem;
        }

        .edit-btn, .delete-btn {
            padding: 0.25rem 0.5rem;
            border-radius: 0.25rem;
            text-decoration: none;
            font-size: 0.75rem;
            font-weight: 600;
            transition: all 0.3s;
        }

        .edit-btn {
            background: #3b82f6;
            color: white;
        }

        .edit-btn:hover {
            background: #2563eb;
        }

        .delete-btn {
            background: #ef4444;
            color: white;
        }

        .delete-btn:hover {
            background: #dc2626;
        }

        /* Load More */
        .load-more {
            text-align: center;
            margin-top: 2rem;
        }

        .load-more-btn {
            background: transparent;
            color: #2563eb;
            border: 2px solid #2563eb;
            padding: 0.75rem 2rem;
            border-radius: 0.5rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }

        .load-more-btn:hover {
            background: #2563eb;
            color: white;
        }

        /* Pagination */
        .pagination {
            display: flex;
            justify-content: center;
            gap: 0.5rem;
            margin-top: 3rem;
        }

        .pagination a {
            padding: 0.5rem 1rem;
            background: white;
            color: #4a5568;
            text-decoration: none;
            border-radius: 0.375rem;
            border: 1px solid #e2e8f0;
            transition: all 0.3s;
        }

        .pagination a:hover,
        .pagination a.active {
            background: #2563eb;
            color: white;
            border-color: #2563eb;
        }

        /* Footer */
        .footer {
            background: #1a202c;
            color: #a0aec0;
            padding: 3rem 0 2rem;
            margin-top: 4rem;
        }

        .footer-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr;
            gap: 2rem;
        }

        .footer-brand {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 700;
            font-size: 1.25rem;
            color: #2563eb;
            margin-bottom: 1rem;
        }

        .footer-brand::before {
            content: "🏗️";
            font-size: 1.5rem;
        }

        .footer-section h4 {
            color: white;
            font-weight: 600;
            margin-bottom: 1rem;
            text-transform: uppercase;
            font-size: 0.875rem;
            letter-spacing: 0.05em;
        }

        .footer-section ul {
            list-style: none;
        }

        .footer-section ul li {
            margin-bottom: 0.5rem;
        }

        .footer-section ul li a {
            color: #a0aec0;
            text-decoration: none;
            transition: color 0.3s;
        }

        .footer-section ul li a:hover {
            color: white;
        }

        .footer-bottom {
            border-top: 1px solid #2d3748;
            margin-top: 2rem;
            padding-top: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.875rem;
        }

        .social-links {
            display: flex;
            gap: 1rem;
        }

        .social-links a {
            color: #a0aec0;
            font-size: 1.25rem;
            transition: color 0.3s;
        }

        .social-links a:hover {
            color: #2563eb;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .nav-container {
                padding: 0 1rem;
                flex-direction: column;
                gap: 1rem;
            }

            .nav-links {
                gap: 1rem;
            }

            .main-container {
                padding: 2rem 1rem;
            }

            .page-title {
                font-size: 2rem;
            }

            .filter-tabs {
                flex-direction: column;
                gap: 0.5rem;
            }

            .projects-grid {
                grid-template-columns: 1fr;
                gap: 1.5rem;
            }

            .footer-container {
                grid-template-columns: 1fr;
                gap: 2rem;
            }

            .footer-bottom {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }

            .admin-controls {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            color: #718096;
        }

        .empty-state-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
        }

        .empty-state h3 {
            font-size: 1.5rem;
            color: #4a5568;
            margin-bottom: 0.5rem;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="nav-container">
            <div class="logo">AYYAVU CONSTRUCTION</div>
            <nav>
                <ul class="nav-links">
                    <li><a href="index.php">Home</a></li>
                    <li><a href="about.html">About</a></li>
                    <li><a href="projects-new.php" class="active">Projects</a></li>
                    <li><a href="#">Services</a></li>
                    <li><a href="contact.html">Contact</a></li>
                </ul>
            </nav>
            <a href="#" class="cta-button">Get a Quote</a>
        </div>
    </header>

    <!-- Main Content -->
    <main class="main-container">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-title">Our Projects</h1>
            <p class="page-subtitle">
                Building a legacy of excellence, one structure at a time. From luxury 
                residential complexes to industrial hubs, discover our portfolio of quality 
                craftsmanship.
            </p>
        </div>

        <!-- Admin Controls -->
        <?php if($isAdmin): ?>
        <div class="admin-controls">
            <div>
                <span class="admin-badge">ADMIN MODE</span>
                <span style="margin-left: 1rem;">You can edit and delete projects</span>
            </div>
            <a href="admin/add-project.php" class="add-project-btn">+ Add New Project</a>
        </div>
        <?php endif; ?>

        <!-- Filter Tabs -->
        <div class="filter-tabs">
            <button class="filter-tab <?php echo !isset($_GET['status']) || $_GET['status'] == 'ALL PROJECTS' ? 'active' : ''; ?>" 
                    onclick="filterProjects('ALL PROJECTS')">
                ALL PROJECTS
            </button>
            <button class="filter-tab <?php echo isset($_GET['status']) && $_GET['status'] == 'COMPLETED' ? 'active' : ''; ?>" 
                    onclick="filterProjects('COMPLETED')">
                COMPLETED
            </button>
            <button class="filter-tab <?php echo isset($_GET['status']) && $_GET['status'] == 'ONGOING' ? 'active' : ''; ?>" 
                    onclick="filterProjects('ONGOING')">
                ONGOING
            </button>
            <button class="filter-tab <?php echo isset($_GET['status']) && $_GET['status'] == 'UPCOMING' ? 'active' : ''; ?>" 
                    onclick="filterProjects('UPCOMING')">
                UPCOMING
            </button>
        </div>

        <!-- Projects Grid -->
        <?php if(pg_num_rows($result) > 0): ?>
        <div class="projects-grid">
            <?php while($project = pg_fetch_assoc($result)): ?>
            <div class="project-card">
                <img src="admin/uploads/<?php echo $project['image']; ?>" 
                     alt="<?php echo htmlspecialchars($project['title']); ?>" 
                     class="project-image">
                
                <div class="project-status status-<?php echo strtolower($project['status']); ?>">
                    <?php echo strtoupper($project['status']); ?>
                </div>

                <div class="project-content">
                    <h3 class="project-title"><?php echo htmlspecialchars($project['title']); ?></h3>
                    
                    <div class="project-location">
                        <?php echo htmlspecialchars($project['location'] ?? 'Chennai, Tamil Nadu'); ?>
                    </div>

                    <p class="project-description">
                        <?php echo htmlspecialchars(substr($project['description'], 0, 120)) . '...'; ?>
                    </p>

                    <div class="project-actions">
                        <a href="project-details.php?id=<?php echo $project['id']; ?>" class="view-details">
                            View Details →
                        </a>
                        
                        <?php if($isAdmin): ?>
                        <div class="admin-actions">
                            <a href="admin/edit-project.php?id=<?php echo $project['id']; ?>" class="edit-btn">Edit</a>
                            <a href="?delete=<?php echo $project['id']; ?>&admin=1" 
                               class="delete-btn" 
                               onclick="return confirm('Are you sure you want to delete this project?')">Delete</a>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>

        <!-- Pagination -->
        <?php if($totalPages > 1): ?>
        <div class="pagination">
            <?php for($i = 1; $i <= $totalPages; $i++): ?>
                <a href="?page=<?php echo $i; ?><?php echo isset($_GET['status']) ? '&status='.$_GET['status'] : ''; ?><?php echo $isAdmin ? '&admin=1' : ''; ?>" 
                   class="<?php echo $page == $i ? 'active' : ''; ?>">
                    <?php echo $i; ?>
                </a>
            <?php endfor; ?>
        </div>
        <?php endif; ?>

        <!-- Load More -->
        <div class="load-more">
            <p style="color: #718096; margin-bottom: 1rem;">
                Showing <?php echo min($limit * $page, $totalRow['total']); ?> of <?php echo $totalRow['total']; ?> projects
            </p>
        </div>

        <?php else: ?>
        <!-- Empty State -->
        <div class="empty-state">
            <div class="empty-state-icon">🏗️</div>
            <h3>No Projects Found</h3>
            <p>No projects match your current filter. Try selecting a different category.</p>
            <?php if($isAdmin): ?>
            <a href="admin/add-project.php" class="add-project-btn" style="margin-top: 1rem; display: inline-block;">
                Add Your First Project
            </a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-container">
            <div>
                <div class="footer-brand">AYYAVU CONSTRUCTION</div>
                <p>Leading construction firm delivering innovative solutions and superior structures across Tamil Nadu.</p>
            </div>
            
            <div class="footer-section">
                <h4>Navigation</h4>
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="about.html">About</a></li>
                    <li><a href="projects-new.php">Projects</a></li>
                    <li><a href="contact.html">Contact</a></li>
                </ul>
            </div>
            
            <div class="footer-section">
                <h4>Social</h4>
                <ul>
                    <li><a href="#">LinkedIn</a></li>
                    <li><a href="#">Instagram</a></li>
                    <li><a href="#">Facebook</a></li>
                </ul>
            </div>
            
            <div class="footer-section">
                <h4>Legal</h4>
                <ul>
                    <li><a href="#">Privacy Policy</a></li>
                    <li><a href="#">Terms of Use</a></li>
                </ul>
            </div>
        </div>
        
        <div class="footer-bottom">
            <p>© 2024 Ayyavu Construction. All rights reserved.</p>
            <div class="social-links">
                <a href="#">📘</a>
                <a href="#">📷</a>
                <a href="#">🔗</a>
            </div>
        </div>
    </footer>

    <script>
        function filterProjects(status) {
            const currentUrl = new URL(window.location);
            const isAdmin = currentUrl.searchParams.get('admin');
            
            if (status === 'ALL PROJECTS') {
                currentUrl.searchParams.delete('status');
            } else {
                currentUrl.searchParams.set('status', status);
            }
            
            currentUrl.searchParams.delete('page'); // Reset to first page
            
            if (isAdmin) {
                currentUrl.searchParams.set('admin', '1');
            }
            
            window.location.href = currentUrl.toString();
        }

        // Add smooth scrolling for better UX
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });
    </script>
</body>
</html>