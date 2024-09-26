$(document).ready(function(){
    'use strict';

    $.ajax({
        url: admin_url + 'datapulse/item_groups_chart',
        type: "GET",
        dataType: "json",
        success: function(data) {
            var categories = {};
            $.each(data, function(index, item) {
                var category_name = item.group_name || "No Category";
                if (categories[category_name]) {
                    categories[category_name]++;
                } else {
                    categories[category_name] = 1;
                }
            });

            var chartData = {
                labels: Object.keys(categories),
                datasets: [{
                    label: 'Items',
                    data: Object.values(categories),
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            };

            var ctx = document.getElementById('dpItemGroupChart').getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: chartData,
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
