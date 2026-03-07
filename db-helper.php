<?php

// Database helper functions for PostgreSQL compatibility

function db_query($conn, $query) {
    return pg_query($conn, $query);
}

function db_fetch_assoc($result) {
    return pg_fetch_assoc($result);
}

function db_num_rows($result) {
    return pg_num_rows($result);
}

function db_insert_id($conn) {
    $result = pg_query($conn, "SELECT lastval()");
    $row = pg_fetch_row($result);
    return $row[0];
}

function db_escape_string($conn, $string) {
    return pg_escape_string($conn, $string);
}

function db_error($conn) {
    return pg_last_error($conn);
}

?>
