<?php
include 'db.php';

header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="gpa_results.csv"');

$output = fopen("php://output", "w");

fputcsv($output, ['Name', 'Semester', 'GPA', 'Date']);

$res = $conn->query("SELECT * FROM results");

while ($row = $res->fetch_assoc()) {
    fputcsv($output, $row);
}

fclose($output);
?>
