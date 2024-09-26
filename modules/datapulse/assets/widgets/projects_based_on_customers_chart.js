$(document).ready(function(){
    'use strict';

    dpProjectsBasedOnCustomersChart();
});

function dpProjectsBasedOnCustomersChart(element) {
    'use strict';

    var selectedYear = $(element).data('year');

    if (selectedYear !== '') {
        $('#projectsBasedOnCustomersChartYear').text(selectedYear);
    }

    $.ajax({
        url: admin_url + 'datapulse/projects_based_on_customers_chart',
        type: "GET",
        data: { year: selectedYear },
        dataType: "json",
        success: function(data) {
            var clientNames = [];
            var projectCounts = [];

            $.each(data, function(index, item) {
                clientNames.push(item.client_name);
                projectCounts.push(item.project_count);
            });

            var ctx = document.getElementById('projectsBasedOnCustomersChart').getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: clientNames,
                    datasets: [{
                        label: 'Projects',
                        data: projectCounts,
                        backgroundColor: 'rgba(75,192,171,0.2)',
                        borderColor: 'rgb(75,147,192)',
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
