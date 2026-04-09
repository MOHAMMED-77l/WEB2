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
    
    // مصفوفة لتخزين بيانات الجدول للعرض
    $rows = [];

    for ($i = 0; $i < count($courses); $i++) {
        $cr = floatval($credits[$i]);
        $g = floatval($grades[$i]);
        if ($cr <= 0) continue;

        $totalPoints += $cr * $g;
        $totalCredits += $cr;
        
        // تجهيز بيانات الصف للجدول
        $rows[] = "<tr><td>".htmlspecialchars($courses[$i])."</td><td>$cr</td><td>$g</td></tr>";
    }

    if ($totalCredits > 0) {
        $gpa = $totalPoints / $totalCredits;

        // 1. حفظ في قاعدة البيانات
        $stmt = $conn->prepare("INSERT INTO results (student_name, semester, gpa) VALUES (?, ?, ?)");
        $stmt->bind_param("ssd", $student, $semester, $gpa);
        $stmt->execute();
        $result_id = $stmt->insert_id;

        for ($i = 0; $i < count($courses); $i++) {
            $stmt2 = $conn->prepare("INSERT INTO courses (result_id, course_name, credits, grade) VALUES (?, ?, ?, ?)");
            $stmt2->bind_param("isdd", $result_id, $courses[$i], $credits[$i], $grades[$i]);
            $stmt2->execute();
        }

        // 2. بناء الجدول الذي يطلبه ملف script.js ليظهره في الصفحة
        $tableHtml = '<table class="table table-bordered mt-3"><thead><tr><th>Course</th><th>Credits</th><th>Grade</th></tr></thead><tbody>';
        $tableHtml .= implode('', $rows);
        $tableHtml .= '</tbody></table>';

        // 3. إرسال الرد النهائي
        echo json_encode([
            'success' => true,
            'gpa' => $gpa,
            'message' => "Saved! GPA = " . number_format($gpa, 2),
            'tableHtml' => $tableHtml // هذا السطر هو الذي كان ينقصك لتظهر النتيجة
        ]);

    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid data']);
    }
}
?>
