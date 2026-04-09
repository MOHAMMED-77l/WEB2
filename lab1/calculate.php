<?php
header('Content-Type: application/json');
include 'db.php';

if (isset($_POST['course'])) {

$student = $_POST['student_name'];
$semester = $_POST['semester'];

$courses = $_POST['course'];
$credits = $_POST['credits'];
$grades = $_POST['grade'];

$totalPoints = 0;
$totalCredits = 0;

for ($i = 0; $i < count($courses); $i++) {
    $cr = floatval($credits[$i]);
    $g = floatval($grades[$i]);

    if ($cr <= 0) continue;

    $totalPoints += $cr * $g;
    $totalCredits += $cr;
}

if ($totalCredits > 0) {

    $gpa = $totalPoints / $totalCredits;

    $stmt = $conn->prepare("INSERT INTO results (student_name, semester, gpa) VALUES (?, ?, ?)");
    $stmt->bind_param("ssd", $student, $semester, $gpa);
    $stmt->execute();

    $result_id = $stmt->insert_id;

    for ($i = 0; $i < count($courses); $i++) {
        $stmt2 = $conn->prepare("INSERT INTO courses (result_id, course_name, credits, grade) VALUES (?, ?, ?, ?)");
        $stmt2->bind_param("isdd", $result_id, $courses[$i], $credits[$i], $grades[$i]);
        $stmt2->execute();
    }
// بناء جدول لعرض المواد في النتيجة (الجزء الثالث والرابع)
    $tableHtml = '<table class="table table-sm mt-3"><thead><tr><th>Course</th><th>Credits</th><th>Grade</th></tr></thead><tbody>';
    for ($i = 0; $i < count($courses); $i++) {
        $tableHtml .= "<tr><td>{$courses[$i]}</td><td>{$credits[$i]}</td><td>{$grades[$i]}</td></tr>";
    }
    $tableHtml .= '</tbody></table>';

    echo json_encode([
        'success' => true,
        'gpa' => $gpa,
        'message' => "Saved! GPA = " . number_format($gpa, 2),
        'tableHtml' => $tableHtml // أضف هذا السطر لكي يظهر الجدول في الصفحة
    ]);
    echo json_encode([
        'success' => true,
        'gpa' => $gpa,
        'message' => "Saved! GPA = " . number_format($gpa, 2)
    ]);

} else {
    echo json_encode(['success' => false, 'message' => 'Invalid data']);
}

}
?>
