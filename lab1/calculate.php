<?php
header('Content-Type: application/json');

if (isset($_POST['course'], $_POST['credits'], $_POST['grade'])) {
    $courses = $_POST['course'];
    $credits = $_POST['credits'];
    $grades = $_POST['grade'];

    $totalPoints = 0; $totalCredits = 0;
    // بناء جدول بتنسيق Bootstrap [cite: 823]
    $tableHtml = '<table class="table table-bordered mt-3"><thead class="thead-dark"><tr><th>Course</th><th>Credits</th><th>Grade</th><th>Grade Points</th></tr></thead><tbody>';

    for ($i = 0; $i < count($courses); $i++) {
        $cr = floatval($credits[$i]);
        $g = floatval($grades[$i]);
        if ($cr <= 0) continue;

        $pts = $cr * $g;
        $totalPoints += $pts;
        $totalCredits += $cr;
        $tableHtml .= "<tr><td>" . htmlspecialchars($courses[$i]) . "</td><td>$cr</td><td>$g</td><td>$pts</td></tr>";
    }
    $tableHtml .= '</tbody></table>';

    if ($totalCredits > 0) {
        $gpa = $totalPoints / $totalCredits;
        // تفسير قيمة المعدل [cite: 15]
        if ($gpa >= 3.7) $interpretation = "Distinction";
        elseif ($gpa >= 3.0) $interpretation = "Merit";
        elseif ($gpa >= 2.0) $interpretation = "Pass";
        else $interpretation = "Fail";

        echo json_encode([
            'success' => true,
            'gpa' => $gpa,
            'message' => "Your GPA is " . number_format($gpa, 2) . " ($interpretation).",
            'tableHtml' => $tableHtml
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'No valid courses entered.']);
    }
}
exit;
?>
