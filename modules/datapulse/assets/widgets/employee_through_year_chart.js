$(document).ready(function(){
    'use strict';

    dpEmployeeThroughYear();
});

function dpEmployeeThroughYear(element) {
    'use strict';

    var selectedYear = $(element).data('year');

    if (selectedYear !== '') {
        $('#employeeThroughYearChartYear').text(selectedYear);
    }

    var months = [];
    var staffCounts = Array.from({length: 12}, () => 0);

    $.ajax({
        url: admin_url + 'datapulse/employee_through_year_chart',
        type: "GET",
        data: { year: selectedYear },
        dataType: "json",
        success: function(data) {

            $.each(data, function(index, item) {
                staffCounts[item.month - 1] = item.total_staff;
            });

            var monthNames = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
            months = monthNames;

            var ctx = document.getElementById('employeeThroughYearChart').getContext('2d');
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: months,
                    datasets: [{
                        label: 'New Staff',
                        data: staffCounts,
                        backgroundColor: 'rgba(192,79,75,0.2)',
                        borderColor: 'rgb(192,85,75)',
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }
    });
}
