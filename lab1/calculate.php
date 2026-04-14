<?php
// منع ظهور أي أخطاء بصيغة HTML لإبقاء الـ JSON نظيفاً
error_reporting(0); 
header('Content-Type: application/json');

include 'db.php';

// التأكد من وجود اتصال
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => "خطأ في الاتصال بقاعدة البيانات: " . $conn->connect_error]);
    exit;
}

if (isset($_POST['course'])) {
    try {
        $student = $_POST['student_name'] ?? 'Unknown';
        $semester = $_POST['semester'] ?? 'N/A';
        $courses = $_POST['course'];
        $credits = $_POST['credits'];
        $grades = $_POST['grade'];

        $totalPoints = 0;
        $totalCredits = 0;
        $rows = [];

        for ($i = 0; $i < count($courses); $i++) {
            $cr = floatval($credits[$i]);
            $g = floatval($grades[$i]);
            if ($cr <= 0) continue;

            $totalPoints += $cr * $g;
            $totalCredits += $cr;
            
            $rows[] = "<tr><td>".htmlspecialchars($courses[$i])."</td><td>$cr</td><td>$g</td></tr>";
        }

        if ($totalCredits > 0) {
            $gpa = $totalPoints / $totalCredits;

            // حفظ في قاعدة البيانات
            $stmt = $conn->prepare("INSERT INTO results (student_name, semester, gpa) VALUES (?, ?, ?)");
            $stmt->bind_param("ssd", $student, $semester, $gpa);
            $stmt->execute();
            $result_id = $stmt->insert_id;

            for ($i = 0; $i < count($courses); $i++) {
                $stmt2 = $conn->prepare("INSERT INTO courses (result_id, course_name, credits, grade) VALUES (?, ?, ?, ?)");
                $stmt2->bind_param("isdd", $result_id, $courses[$i], $credits[$i], $grades[$i]);
                $stmt2->execute();
            }

            $tableHtml = '<table class="table table-bordered mt-3"><thead><tr><th>Course</th><th>Credits</th><th>Grade</th></tr></thead><tbody>';
            $tableHtml .= implode('', $rows);
            $tableHtml .= '</tbody></table>';

            echo json_encode([
                'success' => true,
                'gpa' => $gpa,
                'message' => "تم الحساب والحفظ بنجاح!",
                'tableHtml' => $tableHtml
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => "يرجى إدخال ساعات صحيحة للمواد."]);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => "حدث خطأ داخلي: " . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => "لم يتم استلام أي بيانات."]);
}
?>
