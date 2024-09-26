$(document).ready(function(){
    'use strict';

    dpStaffAssignedToProjects();
});

function dpStaffAssignedToProjects(element) {
    'use strict';

    var selectedYear = $(element).data('year');

    if (selectedYear !== '') {
        $('#dpStaffAssignedToProjectsYear').text(selectedYear);
    }

    $.ajax({
        url: admin_url + 'datapulse/staff_assigned_projects_chart',
        type: "GET",
        data: { year: selectedYear },
        dataType: "json",
        success: function(data) {
            var projectNames = [];
            var staffCounts = [];

            $.each(data, function(index, item) {
                projectNames.push(item.project_name);
                staffCounts.push(item.total_staff_members);
            });

            var ctx = document.getElementById('staffAssignedProjectsChart').getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: projectNames,
                    datasets: [{
                        label: 'Staff Members',
                        data: staffCounts,
                        backgroundColor: 'rgba(192,145,75,0.2)',
                        borderColor: 'rgb(192,141,75)',
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
}
