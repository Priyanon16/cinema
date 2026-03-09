<?php

$host="localhost";
$user="admin_man";
$pass="66010914015";
$db="cinemadb";

$conn = new mysqli($host,$user,$pass,$db);

if($conn->connect_error){
    die("Connection error: ".$conn->connect_error);
}

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

$conn->close();

?>
<!DOCTYPE html>
<html>
<head>

<title>Smart Cinema Dashboard</title>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>

body{
background:radial-gradient(circle,#2b0000,#000);
font-family:Segoe UI;
color:white;
text-align:center;
}

.header{
font-size:32px;
padding:20px;
background:linear-gradient(90deg,#7a0000,#ff0000);
}

.dashboard{
display:flex;
justify-content:center;
gap:30px;
margin-top:40px;
}

.card{
background:#111;
padding:25px;
border-radius:15px;
width:220px;
}

.value{
font-size:40px;
}

.control{
margin-top:50px;
}

button{
padding:12px 20px;
margin:8px;
border:none;
border-radius:8px;
background:#ff0000;
color:white;
font-weight:bold;
cursor:pointer;
}

button:hover{
background:#ff4444;
}

</style>

</head>

<body>

<div class="header">
🎬 SMART CINEMA CONTROL
</div>

<div class="dashboard">

<div class="card">
<h2>Gas</h2>
<div class="value"><?php echo $gas ?></div>
</div>

<div class="card">
<h2>Temp</h2>
<div class="value"><?php echo $temp ?></div>
</div>

<div class="card">
<h2>Humidity</h2>
<div class="value"><?php echo $hum ?></div>
</div>

</div>

<div style="width:70%;margin:auto;margin-top:40px">
<canvas id="chart"></canvas>
</div>


<div class="control">

<h2>💡 Light Control</h2>

<button onclick="send('cinema/stair1','ON')">Stair1 ON</button>
<button onclick="send('cinema/stair1','OFF')">Stair1 OFF</button>

<button onclick="send('cinema/stair2','ON')">Stair2 ON</button>
<button onclick="send('cinema/stair2','OFF')">Stair2 OFF</button>

<button onclick="send('cinema/walllight','ON')">Wall ON</button>
<button onclick="send('cinema/walllight','OFF')">Wall OFF</button>

</div>


<div class="control">

<h2>🎬 Movie Control</h2>

<button onclick="send('cinema/command','Input.Left')">⬅</button>
<button onclick="send('cinema/command','Input.Right')">➡</button>
<button onclick="send('cinema/command','Input.Up')">⬆</button>
<button onclick="send('cinema/command','Input.Down')">⬇</button>

<button onclick="send('cinema/command','Input.Select')">Select</button>
<button onclick="send('cinema/command','Input.Back')">Back</button>
<button onclick="send('cinema/command','Input.Home')">Home</button>

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