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
<div class="value" id="gasValue">0</div>

</div>

<div class="card">

<h3>Temperature</h3>

<canvas id="tempGauge"></canvas>
<div class="value" id="tempValue">0</div>

</div>

<div class="card">

<h3>Humidity</h3>

<canvas id="humGauge"></canvas>
<div class="value" id="humValue">0</div>

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

<button onclick="playpause()">PLAY / PAUSE</button>

<button onclick="send('cinema/command','Input.Select')">SELECT</button>

<button onclick="send('cinema/command','Input.Back')">BACK</button>

<button onclick="send('cinema/command','Input.Home')">HOME</button>

</div>





<script>

const gas=<?php echo $gas ?>;
const temp=<?php echo $temp ?>;
const hum=<?php echo $hum ?>;

const sensorChart = new Chart(document.getElementById("chart"),{

type:'bar',

data:{
labels:['Gas','Temp','Humidity'],

datasets:[{
label:'Sensor Value',

data:[gas,temp,hum],

backgroundColor:[
"#00ff9c",
"#ff3b3b",
"#ffd500"
],

borderRadius:10

}]

},

options:{
responsive:true,
maintainAspectRatio:false,
plugins:{
legend:{
labels:{
color:"#ffffff",
font:{size:16}
}
}
},

scales:{
x:{
ticks:{color:"#ffffff"},
grid:{color:"rgba(255,255,255,0.1)"}
},
y:{
beginAtZero:true,
ticks:{color:"#ffffff"},
grid:{color:"rgba(255,255,255,0.1)"}
}
}
}

});

const client = mqtt.connect("ws://103.114.201.199:9001");

client.on("connect", function(){

console.log("MQTT Connected");

client.subscribe("cinema/stair1/state");
client.subscribe("cinema/stair2/state");
client.subscribe("cinema/walllight/state");
client.subscribe("cinema/gas");
client.subscribe("temperature");
client.subscribe("humidity");

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

if(topic=="cinema/gas"){

let gas=parseInt(value);

gasGauge.data.datasets[0].data=[gas,500-gas];
gasGauge.update();

document.getElementById("gasValue").innerHTML=gas;

sensorChart.data.datasets[0].data[0]=gas;
sensorChart.update();

}

if(topic=="temperature"){

let temp=parseFloat(value);

tempGauge.data.datasets[0].data=[temp,100-temp];
tempGauge.update();

document.getElementById("tempValue").innerHTML=temp+" °C";

sensorChart.data.datasets[0].data[1]=temp;
sensorChart.update();

}

if(topic=="humidity"){

let hum=parseFloat(value);

humGauge.data.datasets[0].data=[hum,100-hum];
humGauge.update();

document.getElementById("humValue").innerHTML=hum+" %";

sensorChart.data.datasets[0].data[2]=hum;
sensorChart.update();

}
});

document.getElementById("wallSwitch").addEventListener("change",function(){

let value=this.checked?"ON":"OFF";

client.publish("cinema/walllight",value);

});
function send(topic,value){

client.publish(topic,value);

console.log("Send:",topic,value);

}

function createGauge(id,value,max,color){

return new Chart(document.getElementById(id),{

type:'doughnut',

data:{
datasets:[{
data:[value,max-value],
backgroundColor:[color,"#333"],
borderWidth:0
}]
},

options:{
rotation:270,
circumference:180,
cutout:'70%',
plugins:{legend:{display:false}}
}

});

}
const gasGauge = createGauge("gasGauge",gas,500,"#7CFC00");
const tempGauge = createGauge("tempGauge",temp,100,"#ff4444");
const humGauge = createGauge("humGauge",hum,100,"#e6ff00");


function playpause(){

let msg = {
method:"Input.ExecuteAction",
params:{
action:"playpause"
}
};

client.publish("cinema/command",JSON.stringify(msg));

}
</script>

</body>
</html>