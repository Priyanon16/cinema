const client = mqtt.connect("ws://103.114.201.199:9001");

client.on("connect",function(){

console.log("MQTT Connected");

client.subscribe("cinema/stair1/state");

client.subscribe("cinema/stair2/state");

});


client.on("message",function(topic,message){

let value = message.toString();

if(topic=="cinema/stair1/state"){

document.getElementById("stair1").innerHTML=value;

}

if(topic=="cinema/stair2/state"){

document.getElementById("stair2").innerHTML=value;

}

});


function send(topic,value){
    if(value=="Input.PlayPause"){
value="playpause";
}

client.publish(topic,value);

}