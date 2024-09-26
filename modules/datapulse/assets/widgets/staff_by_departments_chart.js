$(document).ready(function(){
    'use strict';

    $.ajax({
        url: admin_url + 'datapulse/staff_by_departments_chart',
        type: "GET",
        dataType: "json",
        success: function(data) {
            var departmentNames = [];
            var staffCounts = [];

            $.each(data, function(index, item) {
                departmentNames.push(item.department_name);
                staffCounts.push(item.total_active_staff);
            });

            var ctx = document.getElementById('staffByDepartmentsChart').getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: departmentNames,
                    datasets: [{
                        label: 'Total Active Staff',
                        data: staffCounts,
                        backgroundColor: 'rgba(192,75,110,0.2)',
                        borderColor: 'rgb(192,75,159)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero: true,
                            }
                        }]
                    },
                }
            });
        }
    });
});
