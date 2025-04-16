<?php

$dbhandle = mysqli_connect('localhost', 'root', 'syslib', 'b2busers')
 or die('unable to connect to main DB - server_status');

$query = "SELECT * FROM companies";
$res = $dbhandle->query($query);



?>




<html>

  <head>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
      google.charts.load('current', {'packages':['corechart']});
      google.charts.setOnLoadCallback(drawChart);

      function drawChart() {

        var data = google.visualization.arrayToDataTable([
          ['description', 'Sales Today'],

<?php

while($row=$res->fetch_assoc())
{
    echo "['".$row['description']."',".$row['salestoday']."],";
}


?>

]);

        var options = {
          title: 'B2B Companies Todays Sales',
	  is3D: true,
          pieSliceText: 'label',
          slices: {  10: {offset: 0.2},
},
        };

        var chart = new google.visualization.PieChart(document.getElementById('piechart'));

        chart.draw(data, options);
      }
    </script>
  </head>
  <body>
    <div id="piechart" style="width: 1350px; height: 750px;"></div>
  </body>
</html>

