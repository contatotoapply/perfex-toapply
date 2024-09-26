$(document).ready(function(){
    'use strict';

    dpEstimateAssignedAgentsChart();
});

function dpEstimateAssignedAgentsChart(element) {
    'use strict';

    var selectedYear = $(element).data('year');

    if (selectedYear !== '') {
        $('#dpEstimateAssignedAgentsChartYear').text(selectedYear);
    }

    $.ajax({
        url: admin_url + 'datapulse/estimate_assigned_agents_chart',
        type: "GET",
        data: { year: selectedYear },
        dataType: "json",
        success: function(data) {
            var projectNames = [];
            var staffCounts = [];

            $.each(data, function(index, item) {
                projectNames.push(item.agent_name);
                staffCounts.push(item.estimate_count);
            });

            var ctx = document.getElementById('dpEstimateAssignedAgentsChart').getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: projectNames,
                    datasets: [{
                        label: 'Estimates',
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
