<?php
/* ===============================
   DATABASE CONNECTION
================================ */

$host = "103.114.201.199";
$user = "admin_man";
$pass = "";
$db   = "cinemadb";

$conn = new mysqli($host,$user,$pass,$db);

if ($conn->connect_error) {
    die("Database connection failed");
}




/* ===============================
   GET SENSOR DATA
================================ */

$sql = "SELECT sensor_name,data_value
        FROM telemetry_data
        ORDER BY id DESC
        LIMIT 50";

$result = $conn->query($sql);

$data = [];

while($row = $result->fetch_assoc()){
    $data[$row['sensor_name']] = $row['data_value'];
}

$gas  = $data['Gas'] ?? 0;
$temp = $data['Temperature'] ?? 0;
$hum  = $data['humidity'] ?? 0;

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<title>Smart Cinema Dashboard</title>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>

/* ===============================
   GLOBAL STYLE
================================ */

body{
    margin:0;
    font-family:Segoe UI;
    background: radial-gradient(circle,#2b0000,#000000);
    color:white;
}

/* ===============================
   HEADER
================================ */

.header{
    text-align:center;
    padding:20px;
    font-size:30px;
    font-weight:bold;

    background:linear-gradient(90deg,#7a0000,#ff0000);

    box-shadow:0 5px 20px rgba(255,0,0,0.5);
}

/* ===============================
   DASHBOARD CARDS
================================ */

.dashboard{
    display:flex;
    justify-content:center;
    gap:30px;
    margin-top:40px;
}

.card{

    width:250px;
    padding:25px;

    background:#111;

    border-radius:15px;

    text-align:center;

    box-shadow:0 0 25px rgba(255,0,0,0.3);

}

.card h2{
    color:#ff2a2a;
}

.value{
    font-size:40px;
    font-weight:bold;
}

/* ===============================
   CHART BOX
================================ */

.chart-box{

    width:80%;
    margin:auto;
    margin-top:50px;

    background:#111;

    padding:20px;

    border-radius:20px;

    box-shadow:0 0 25px rgba(255,0,0,0.3);

}

</style>

</head>

<body>

<!-- ===============================
     HEADER
================================ -->

<div class="header">
🎬 SMART CINEMA DASHBOARD
</div>


<!-- ===============================
     SENSOR CARDS
================================ -->

<div class="dashboard">

<div class="card">

<h2>🔥 Gas</h2>

<div class="value">
<?php echo $gas ?> ppm
</div>

</div>


<div class="card">

<h2>🌡 Temperature</h2>

<div class="value">
<?php echo $temp ?> °C
</div>

</div>


<div class="card">

<h2>💧 Humidity</h2>

<div class="value">
<?php echo $hum ?> %
</div>

</div>

</div>


<!-- ===============================
     CHART
================================ -->

<div class="chart-box">

<canvas id="sensorChart"></canvas>

</div>



<script>

/* ===============================
   CHART DATA
================================ */

const gas  = <?php echo $gas ?>;
const temp = <?php echo $temp ?>;
const hum  = <?php echo $hum ?>;


/* ===============================
   CREATE CHART
================================ */

const ctx = document.getElementById('sensorChart');

new Chart(ctx,{

type:'bar',

data:{

labels:['Gas','Temperature','Humidity'],

datasets:[{

label:'Sensor Value',

data:[gas,temp,hum],

borderWidth:1

}]

},

options:{

plugins:{
legend:{
labels:{color:'white'}
}
},

scales:{
y:{
ticks:{color:'white'}
},
x:{
ticks:{color:'white'}
}
}

}

});


/* ===============================
   AUTO REFRESH
================================ */

setTimeout(function(){

location.reload();

},5000);

</script>

</body>
</html>