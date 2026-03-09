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

<h3>Gas Sensor</h3>

<canvas id="gasGauge"></canvas>

</div>

<div class="card">

<h3>Temperature</h3>

<canvas id="tempGauge"></canvas>

</div>

<div class="card">

<h3>Humidity</h3>

<canvas id="humGauge"></canvas>

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

<h2>Wall Light</h2>

<label class="switch">
  <input type="checkbox" id="wallSwitch">
  <span class="slider"></span>
</label>

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

const client = mqtt.connect("ws://103.114.201.199:9001");

client.on("connect", function(){

console.log("MQTT Connected");

client.subscribe("cinema/stair1/state");
client.subscribe("cinema/stair2/state");
client.subscribe("cinema/walllight/state");

});

client.on("message", function(topic, message){

let value = message.toString();

if(topic=="cinema/stair1/state"){
document.getElementById("stair1").innerHTML=value;
}

if(topic=="cinema/stair2/state"){
document.getElementById("stair2").innerHTML=value;
}

if(topic=="cinema/walllight/state"){

let sw=document.getElementById("wallSwitch");

sw.checked = value=="ON";

}

});

document.getElementById("wallSwitch").addEventListener("change",function(){

let value=this.checked?"ON":"OFF";

client.publish("cinema/walllight",value);

});

function createGauge(id,value,max,color){

const ctx = document.getElementById(id);

new Chart(ctx,{

type:'doughnut',

data:{
datasets:[{
data:[value, max-value],
backgroundColor:[color,"#333"],
borderWidth:0
}]
},

options:{


responsive:true,
maintainAspectRatio:false,

rotation:270,
circumference:180,

cutout:'70%',

plugins:{
legend:{display:false},
tooltip:{enabled:false}
}

}

});

}

createGauge("gasGauge", gas, 500, "#7CFC00")

createGauge("tempGauge", temp, 100, "#ff4444")

createGauge("humGauge", hum, 100, "#e6ff00")

</script>

</body>
</html>