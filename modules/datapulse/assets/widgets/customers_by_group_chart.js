$(document).ready(function(){
    'use strict';

    $.ajax({
        url: admin_url + 'datapulse/customers_by_group_chart',
        type: "GET",
        dataType: "json",
        success: function(data) {
            var departmentNames = [];
            var staffCounts = [];

            $.each(data, function(index, item) {
                departmentNames.push(item.group_name);
                staffCounts.push(item.customer_count);
            });

            var ctx = document.getElementById('customersByGroupChart').getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: departmentNames,
                    datasets: [{
                        label: 'Customers',
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
