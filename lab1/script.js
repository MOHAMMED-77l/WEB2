$(document).ready(function () {

$('#addCourse').click(function () {
    let row = $('.course-row').first().clone();
    row.find('input').val('');

    row.append('<div class="col-auto"><button type="button" class="btn btn-danger remove-row">X</button></div>');
    $('#courses').append(row);
});

$(document).on('click', '.remove-row', function () {
    if ($('.course-row').length > 1) {
        $(this).closest('.course-row').remove();
    }
});

$('#gpaForm').submit(function (e) {
    e.preventDefault();

    $.ajax({
        url: 'calculate.php',
        type: 'POST',
        data: $(this).serialize(),
        dataType: 'json',

        success: function (res) {

            if (res.success) {

                let percent = (res.gpa / 4) * 100;

                let color = "bg-danger";
                if (res.gpa >= 3.7) color = "bg-success";
                else if (res.gpa >= 3.0) color = "bg-info";
                else if (res.gpa >= 2.0) color = "bg-warning";

                $('#result').html(`
                    <div class="alert alert-success">${res.message}</div>

                    <div class="progress mt-2">
                        <div class="progress-bar ${color}" style="width:${percent}%">
                            ${res.gpa.toFixed(2)}
                        </div>
                    </div>
                `);

            } else {
                $('#result').html(`<div class="alert alert-danger">${res.message}</div>`);
            }
        }
    });

});

});
