$(document).ready(function () {
    // 1. إضافة صف مادة جديد عند الضغط على الزر
    $('#addCourse').click(function () {
        var row = $('.course-row').first().clone(); // نسخ الصف الأول [cite: 637, 638]
        row.find('input').val(''); // تفريغ القيم في الصف الجديد [cite: 639]
        row.append('<div class="col-auto"><button type="button" class="btn btn-danger remove-row">X</button></div>'); [cite: 641, 650, 652]
        $('#courses').append(row); // إضافة الصف للمجموعة [cite: 658]
    });

    // 2. حذف صف المادة
    $(document).on('click', '.remove-row', function () {
        if ($('.course-row').length > 1) { // التأكد من عدم حذف كل الصفوف [cite: 664, 665]
            $(this).closest('.course-row').remove(); [cite: 667]
        }
    });

    // 3. إرسال البيانات عبر AJAX
    $('#gpaForm').submit(function (e) {
        e.preventDefault(); // منع الصفحة من التحديث [cite: 685]
        
        $.ajax({
            url: 'calculate.php',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json', [cite: 715, 717, 719, 721, 723]
            success: function (response) {
                if (response.success) { [cite: 726]
                    // تحديد لون التنبيه بناءً على المعدل
                    var alertClass = 'alert-info';
                    if (response.gpa >= 3.7) alertClass = 'alert-success'; // Distinction [cite: 734, 735, 16]
                    else if (response.gpa >= 3.0) alertClass = 'alert-info'; // Merit [cite: 736, 17]
                    else if (response.gpa >= 2.0) alertClass = 'alert-warning'; // Pass [cite: 738, 739, 18]
                    else alertClass = 'alert-danger'; // Fail [cite: 741, 744, 19]

                    // عرض النتيجة والجدول
                    $('#result').html('<div class="alert ' + alertClass + '">' + response.message + '</div>' + response.tableHtml); [cite: 748, 750, 751, 764, 766]
                }
            }
        });
    });
});
