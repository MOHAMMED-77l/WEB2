<?php
include 'db.php';

$res = $conn->query("SELECT * FROM results ORDER BY created_at DESC");

echo "<h2>History</h2>";
echo "<table border='1'>";
echo "<tr><th>Name</th><th>Semester</th><th>GPA</th><th>Date</th></tr>";

while ($row = $res->fetch_assoc()) {
    echo "<tr>
    <td>{$row['student_name']}</td>
    <td>{$row['semester']}</td>
    <td>{$row['gpa']}</td>
    <td>{$row['created_at']}</td>
    </tr>";
}
echo "</table>";
?>
