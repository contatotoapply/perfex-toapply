$(document).ready(function () {
    'use strict';

    dpStaffAssignedToLeads();
});

function dpStaffAssignedToLeads(element) {
    'use strict';

    var selectedYear = $(element).data('year');

    if (selectedYear !== '') {
        $('#dpStaffAssignedToLeadsYear').text(selectedYear);
    }

    $.ajax({
        url: admin_url + 'datapulse/staff_assigned_to_leads/',
        method: "GET",
        data: { year: selectedYear },
        dataType: "json",
        success: function(data) {
            var chartData = {
                labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
                datasets: []
            };

            var staffDatasets = {};

            data.stats.forEach(function(stat) {
                if (!staffDatasets.hasOwnProperty(stat.staff_name)) {
                    staffDatasets[stat.staff_name] = {
                        label: stat.staff_name + ' (' + stat.total_leads + ')',
                        data: [],
                        borderColor: 'rgba(' + Math.floor(Math.random() * 256) + ',' + Math.floor(Math.random() * 256) + ',' + Math.floor(Math.random() * 256) + ', 1)',
                        borderWidth: 2,
                        fill: false
                    };

                    for (var i = 0; i < 12; i++) {
                        staffDatasets[stat.staff_name].data.push(0);
                    }
                }

                staffDatasets[stat.staff_name].data[stat.month - 1] += parseInt(stat.leads_count);
            });

            for (var staffName in staffDatasets) {
                if (staffDatasets.hasOwnProperty(staffName)) {
                    chartData.datasets.push(staffDatasets[staffName]);
                }
            }

            var ctx = document.getElementById('lineChart').getContext('2d');
            var lineChart = new Chart(ctx, {
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
