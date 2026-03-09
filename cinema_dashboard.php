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

<title>Smart Cinema Control</title>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>

body{
background:radial-gradient(circle,#2b0000,#000);
color:white;
font-family:Segoe UI;
}

.grid{

display:grid;
grid-template-columns:repeat(3,1fr);
gap:20px;
padding:20px;

}

.card{

background:#111;
padding:20px;
border-radius:15px;

}

button{

background:#e00000;
border:none;
color:white;
padding:12px;
margin:6px;
border-radius:8px;
font-weight:bold;
cursor:pointer;

}

button:hover{
background:#ff4444;
}

</style>

</head>

<body>

<h1>🎬 SMART CINEMA CONTROL</h1>

<div class="grid">

<div class="card">

<h2>Gas</h2>

<h1><?php echo $gas ?></h1>

</div>

<div class="card">

<h2>Temperature</h2>

<h1><?php echo $temp ?></h1>

</div>

<div class="card">

<h2>Humidity</h2>

<h1><?php echo $hum ?></h1>

</div>

</div>


<div class="card">

<canvas id="chart"></canvas>

</div>


<div class="card">

<h2>💡 Light Control</h2>

<button onclick="send('cinema/stair1','ON')">Stair UC ON</button>
<button onclick="send('cinema/stair1','OFF')">Stair UC OFF</button>

<button onclick="send('cinema/stair2','ON')">Stair IR ON</button>
<button onclick="send('cinema/stair2','OFF')">Stair IR OFF</button>

<button onclick="send('cinema/walllight','ON')">Wall ON</button>
<button onclick="send('cinema/walllight','OFF')">Wall OFF</button>

</div>


<div class="card">

<h2>🎬 Cinema Control</h2>

<button onclick="send('cinema/command','Input.Left')">LEFT</button>
<button onclick="send('cinema/command','Input.Right')">RIGHT</button>

<button onclick="send('cinema/command','Input.Up')">UP</button>
<button onclick="send('cinema/command','Input.Down')">DOWN</button>

<button onclick="send('cinema/command','playpause')">PLAY / PAUSE</button>

<button onclick="send('cinema/command','Input.Select')">SELECT</button>
<button onclick="send('cinema/command','Input.Back')">BACK</button>
<button onclick="send('cinema/command','Input.Home')">HOME</button>

</div>


<script>

const gas=<?php echo $gas ?>;
const temp=<?php echo $temp ?>;
const hum=<?php echo $hum ?>;

new Chart(document.getElementById("chart"),{

type:'line',

data:{
labels:['Gas','Temp','Humidity'],
datasets:[{
label:'Sensor',
data:[gas,temp,hum]
}]
}

});

function send(topic,value){

fetch("mqtt.php",{

method:"POST",

headers:{
"Content-Type":"application/x-www-form-urlencoded"
},

body:"topic="+topic+"&value="+value

});

}

setTimeout(()=>location.reload(),5000);

</script>

</body>
</html>