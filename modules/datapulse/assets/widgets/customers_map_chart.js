$(document).ready(function () {
    'use strict';

    google.charts.load('current', {
        'packages':['geochart'],
    });
    google.charts.setOnLoadCallback(drawRegionsMap);
});

function drawRegionsMap() {
    'use strict';

    $.ajax({
        url: admin_url + 'datapulse/customers_map_chart/',
        dataType: "json",
        success: function(data) {

            var chart_data = [];

            chart_data.push(['Country', 'Total Customers']);
            chart_data = chart_data.concat(data);

            var data = google.visualization.arrayToDataTable(chart_data);
            var options = {};
            var chart = new google.visualization.GeoChart(document.getElementById('regions_div'));

            chart.draw(data, options);
        }
    });
}
