<?php

$conn = new mysqli("localhost","admin_man","66010914015","cinemadb");

$sql="SELECT sensor_name,data_value
FROM telemetry_data
ORDER BY id DESC
LIMIT 50";

$result=$conn->query($sql);

$data=[];

while($row=$result->fetch_assoc()){
$data[$row['sensor_name']]=$row['data_value'];
}

$gas=$data['Gas']??0;
$temp=$data['Temperature']??0;
$hum=$data['humidity']??0;

?>

<!DOCTYPE html>
<html>

<head>

<title>Smart Cinema Dashboard</title>

<link rel="stylesheet" href="style.css">

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script src="https://unpkg.com/mqtt/dist/mqtt.min.js"></script>

</head>

<body>

<h1>🎬 SMART CINEMA CONTROL</h1>


<div class="grid">

<div class="card">

<h2>Gas</h2>

<div class="value"><?php echo $gas ?></div>

</div>

<div class="card">

<h2>Temperature</h2>

<div class="value"><?php echo $temp ?></div>

</div>

<div class="card">

<h2>Humidity</h2>

<div class="value"><?php echo $hum ?></div>

</div>

</div>


<div class="card">

<canvas id="chart"></canvas>

</div>


<div class="card">

<h2>Light Status</h2>

Stair Light UC :
<span id="stair1">OFF</span>

<br><br>

Stair Light IR :
<span id="stair2">OFF</span>

</div>


<div class="card">

<h2>Light Control</h2>

<button onclick="send('cinema/stair1','ON')">Stair UC ON</button>

<button onclick="send('cinema/stair1','OFF')">Stair UC OFF</button>

<button onclick="send('cinema/stair2','ON')">Stair IR ON</button>

<button onclick="send('cinema/stair2','OFF')">Stair IR OFF</button>

<button onclick="send('cinema/walllight','ON')">Wall ON</button>

<button onclick="send('cinema/walllight','OFF')">Wall OFF</button>

</div>


<div class="card">

<h2>Movie Control</h2>

<button onclick="send('cinema/command','Input.Left')">LEFT</button>

<button onclick="send('cinema/command','Input.Right')">RIGHT</button>

<button onclick="send('cinema/command','Input.Up')">UP</button>

<button onclick="send('cinema/command','Input.Down')">DOWN</button>

<button onclick="send('cinema/command','playpause')">PLAY / PAUSE</button>

<button onclick="send('cinema/command','Input.Select')">SELECT</button>

<button onclick="send('cinema/command','Input.Back')">BACK</button>

<button onclick="send('cinema/command','Input.Home')">HOME</button>

</div>



<script src="mqtt.js"></script>

<script>

const gas=<?php echo $gas ?>;
const temp=<?php echo $temp ?>;
const hum=<?php echo $hum ?>;

new Chart(document.getElementById("chart"),{

type:'bar',

data:{
labels:['Gas','Temp','Humidity'],
datasets:[{
label:'Sensor',
data:[gas,temp,hum]
}]
}

});

</script>

</body>
</html>