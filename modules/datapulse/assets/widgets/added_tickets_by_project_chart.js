$(document).ready(function () {
    dpAddedTicketsByProject();
});

function dpAddedTicketsByProject(element) {
    'use strict';

    var selectedYear = $(element).data('year');

    if (selectedYear !== '') {
        $('#dpAddedTicketsByProjectYear').text(selectedYear);
    }

    $.ajax({
        url: admin_url + 'datapulse/added_tickets_by_project_chart',
        method: "GET",
        data: {year: selectedYear},
        dataType: "json",
        success: function (data) {
            var chartData = {
                labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
                datasets: []
            };

            var projectDatasets = {};

            data.forEach(function (item) {
                var projectName = item.project_name + ' - Tickets';
                if (!projectDatasets.hasOwnProperty(projectName)) {
                    projectDatasets[projectName] = {
                        label: projectName,
                        data: [],
                        borderColor: 'rgba(' + Math.floor(Math.random() * 256) + ',' + Math.floor(Math.random() * 256) + ',' + Math.floor(Math.random() * 256) + ', 1)',
                        borderWidth: 2,
                        fill: false
                    };

                    for (var i = 0; i < 12; i++) {
                        projectDatasets[projectName].data.push(0);
                    }
                }

                projectDatasets[projectName].data[item.month - 1] = parseInt(item.ticket_count);
            });

            for (var projectName in projectDatasets) {
                if (projectDatasets.hasOwnProperty(projectName)) {
                    chartData.datasets.push(projectDatasets[projectName]);
                }
            }

            var ctx = document.getElementById('addedTicketsByProjectChart').getContext('2d');
            new Chart(ctx, {
                type: 'line',
                data: chartData,
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
