$(document).ready(function () {
    'use strict';

    staffLoggedTimeChart('');
});

function staffLoggedTimeChart(element) {
    'use strict';

    var selectedFilter = $(element).data('dpfilter');

    if (element !== '') {
        if (selectedFilter !== '') {
            $('#StaffLoggedTimeFilter').text($(element).text());
        }
    }

    $.ajax({
        url: admin_url + 'datapulse/staff_logged_time_chart',
        type: 'GET',
        data: {selectedfilter: selectedFilter},
        dataType: 'json',
        success: function (data) {
            var staffNames = [];
            var loggedHours = [];

            $.each(data.labels, function (index, staffName) {
                staffNames.push(staffName);

                var parts = data.datasets[index].split(':');
                var hours = parseInt(parts[0]);
                var minutes = parseInt(parts[1]) / 60;
                loggedHours.push(hours + minutes);
            });

            var ctx = document.getElementById('staffLoggedTimeChart').getContext('2d');
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: staffNames,
                    datasets: [{
                        label: 'Logged Hours',
                        data: loggedHours,
                        backgroundColor: 'rgba(192,165,75,0.2)',
                        borderColor: 'rgb(208,183,24)',
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    tooltips: {
                        enabled: true,
                        mode: 'single',
                        callbacks: {
                            label: function (tooltipItems, data) {
                                return decimalToHM(tooltipItems.yLabel);
                            }
                        }
                    },
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero: true,
                                min: 0,
                                userCallback: function (label, index, labels) {
                                    return decimalToHM(label);
                                },
                            }
                        }]
                    }
                }
            });
        }
    });

}
