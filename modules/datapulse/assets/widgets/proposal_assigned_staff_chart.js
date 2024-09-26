$(document).ready(function(){
    'use strict';

    dpProposalAssignedStaffChart();
});

function dpProposalAssignedStaffChart(element) {
    'use strict';

    var selectedYear = $(element).data('year');

    if (selectedYear !== '') {
        $('#dpProposalAssignedStaffChartYear').text(selectedYear);
    }

    $.ajax({
        url: admin_url + 'datapulse/proposal_assigned_staff_chart',
        type: "GET",
        data: { year: selectedYear },
        dataType: "json",
        success: function(data) {
            var projectNames = [];
            var staffCounts = [];

            $.each(data, function(index, item) {
                projectNames.push(item.staff_name);
                staffCounts.push(item.proposal_count);
            });

            var ctx = document.getElementById('dpProposalAssignedStaffChart').getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: projectNames,
                    datasets: [{
                        label: 'Proposals',
                        data: staffCounts,
                        backgroundColor: 'rgba(139,75,192,0.2)',
                        borderColor: 'rgb(100,75,192)',
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
