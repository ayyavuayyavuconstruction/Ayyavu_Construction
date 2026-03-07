<?php
include("config.php");

if($conn) {
    echo "Database Connected Successfully to Supabase PostgreSQL<br>";

    // Test query
    $result = pg_query($conn, "SELECT COUNT(*) as total FROM projects");
    if($result) {
        $row = pg_fetch_assoc($result);
        echo "Total projects: " . $row['total'];
    }
} else {
    echo "Database Connection Failed: " . pg_last_error();
}
?>
