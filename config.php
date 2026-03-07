<?php

// Supabase PostgreSQL connection
$host = "aws-0-ap-south-1.pooler.supabase.com";
$dbname = "postgres";
$user = "postgres.xjhhfdkdqpyzarachzmv";
$password = getenv('SUPABASE_DB_PASSWORD') ?: "Ayyavu@2003";
$port = "6543";

// Create PostgreSQL connection
$conn = pg_connect("host=$host port=$port dbname=$dbname user=$user password=$password sslmode=require");

if(!$conn){
    die("Database connection failed: " . pg_last_error());
}

?>