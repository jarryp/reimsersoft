<?php
require_once "procedimientos.php";
if($conn2=conectarpg()){
$sql="select distinct extract('year' from fecha) as agno
 from recibos
 where est='A' and fecha<=now()
 order by agno desc limit 4";
$result=pg_query($sql);
while($row=pg_fetch_array($result)){
    $datay[]=$row['agno'];
}
?>
<!doctype html>
<html>

<head>
    <title>Line Chart</title>
    <script src="../js/Chart.bundle.js"></script>
    <script src="http://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
    <style>
        canvas {
            -moz-user-select: none;
            -webkit-user-select: none;
            -ms-user-select: none;
        }
    </style>
</head>

<body>
    <div style="width:35%;">
        <canvas id="canvas2"></canvas>
    </div>
    <br>
    <br>
    <script>
        //var MONTHS = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
        var randomScalingFactor = function() {
            return Math.round(Math.random() * 100 * (Math.random() > 0.5 ? -1 : 1));
        };
        var randomColorFactor = function() {
            return Math.round(Math.random() * 255);
        };
        var randomColor = function(opacity) {
            return 'rgba(' + randomColorFactor() + ',' + randomColorFactor() + ',' + randomColorFactor() + ',' + (opacity || '.3') + ')';
        };

        var config = {
            type: 'line',
            data: {
                labels: ["Ene","Feb","Mar","Abl","May","Jun","Jul","Ago","Sep","Oct","Nov","Dic"],
                datasets: [{
                    label: '<?php echo $datay[3];?>',
                    data: [
                        <?php
                            $sql=" select extract('month' from fecha) as mes , sum(neto) as monto
                                    from recibos 
                                    where est='A' and fecha<=now() and extract('year' from fecha)=".$datay[3]."
                                    group by extract('month' from fecha)
                                    order by extract('month' from fecha)";
                           $result=pg_query($sql);
                            while($row=pg_fetch_array($result)){
                                ?>
                                '<?php echo $row["monto"]?>',
                            <?php
                            }
                        ?>
                    ],
                    fill: false,
                    borderDash: [5, 5],
                }, {
                    label: '<?php echo $datay[2];?>',
                    data: [
                        <?php
                            $sql=" select extract('month' from fecha) as mes , sum(neto) as monto
                                    from recibos 
                                    where est='A' and fecha<=now() and extract('year' from fecha)=".$datay[2]."
                                    group by extract('month' from fecha)
                                    order by extract('month' from fecha)";
                           $result=pg_query($sql);
                            while($row=pg_fetch_array($result)){
                                ?>
                                '<?php echo $row["monto"]?>',
                            <?php
                            }
                        ?>
                    ],
                    fill: false,
                    borderDash: [5, 5],
                }, {
                    label: '<?php echo $datay[1];?>',
                    data: [<?php
                            $sql=" select extract('month' from fecha) as mes , sum(neto) as monto
                                    from recibos 
                                    where est='A' and fecha<=now() and extract('year' from fecha)=".$datay[1]."
                                    group by extract('month' from fecha)
                                    order by extract('month' from fecha)";
                           $result=pg_query($sql);
                            while($row=pg_fetch_array($result)){
                                ?>
                                '<?php echo $row["monto"]?>',
                            <?php
                            }
                        ?>],
                    lineTension: 0,
                    fill: false,
                }, {
                    label: '<?php echo $datay[0];?>',
                    data: [<?php
                            $sql=" select extract('month' from fecha) as mes , sum(neto) as monto
                                    from recibos 
                                    where est='A' and fecha<=now() and extract('year' from fecha)=".$datay[0]."
                                    group by extract('month' from fecha)
                                    order by extract('month' from fecha)";
                           $result=pg_query($sql);
                            while($row=pg_fetch_array($result)){
                                ?>
                                '<?php echo $row["monto"]?>',
                            <?php
                            }
                        ?>],
                    fill: false,
                }]
            },
            options: {
                responsive: true,
                legend: {
                    position: 'bottom',
                },
                hover: {
                    mode: 'label'
                },
                scales: {
                    xAxes: [{
                        display: true,
                        scaleLabel: {
                            display: true,
                            labelString: 'Month'
                        }
                    }],
                    yAxes: [{
                        display: true,
                        scaleLabel: {
                            display: true,
                            labelString: 'Value'
                        }
                    }]
                },
                title: {
                    display: true,
                    text: 'Chart.js Line Chart - Legend'
                }
            }
        };

        $.each(config.data.datasets, function(i, dataset) {
            var background = randomColor(0.5);
            dataset.borderColor = background;
            dataset.backgroundColor = background;
            dataset.pointBorderColor = background;
            dataset.pointBackgroundColor = background;
            dataset.pointBorderWidth = 1;
        });

        window.onload = function() {
            var ctx = document.getElementById("canvas2").getContext("2d");
            window.myLine = new Chart(ctx, config);
        };
        

    </script>
</body>

</html>
<?php
    }
    ?>