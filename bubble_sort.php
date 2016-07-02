<?php
//shuffle
if($_SERVER['REQUEST_METHOD']==="POST"){
            $bubbles_array=array();
            for ($i = 0; $i < 10; $i++ ) {
                $bubbles_array[]=rand(0,100);
            }
        echo json_encode($bubbles_array);
        die;
        
}
//step
if($_SERVER['REQUEST_METHOD']==="PUT"){
        $puts=json_decode(file_get_contents("php://input"));
        $bubbles_array=$puts->bubbles_array;
        $check=$bubbles_array;
        sort($check);
        if($check===$bubbles_array){
            die("complete");
        }
        $i=$puts->bubbles_index;
        if($bubbles_array[$i+1] < $bubbles_array[$i]){
            $tmp = $bubbles_array[$i];
            $bubbles_array[$i] = $bubbles_array[$i+1];
            $bubbles_array[$i+1] = $tmp;
        }
        echo json_encode($bubbles_array);
    die;
}

?>
<!DOCTYPE html>
<html>
<head>
<title> Bubble Sort Aglo</title>
<link rel="stylesheet" type="text/css" href="//www.woodstitch.com/css/bubble_sort.css" >
</head>
<body>
<div id="tays_app_main">
    <h1> Bubble Sort by Taylor Hawkes </h1>
    <button class="tays-click"  tays-click="shuffle()">shuffle</button>
    <button class="tays-click" id="step" tays-click="step()">step</button>
    <button class="tays-click"  tays-click="play()">Play</button>
         <div class="tays_row">
            <div class="tays-foreach" tays-foreach="bubbles_array">
             <div class="tays_td"  style="width:{{value|divide|totalwidth}}%;background:#{{value|int_to_rgb}}">
             {{value}}
             </div>
            </div>
            <div class="clear"></div>
        </div>
</div>
<div class="notes">
Note: I built this on top of a small Javascript library that I developed a few weeks ago in order to help with a SPA that I was building (basically a poor man's Angular). This makes my JS look a little over engineered for this task, however I wanted to show off and continue to work at my JS/SPA library so I decided to go with it anyway.  Thanks -Taylor 
</div>
<script type="text/javascript" src="//www.woodstitch.com/js/controller_base.js"></script>
<script>
window.onload=function(){
    TaysControllerBubbleSort.prototype=new TaysController;  

    function TaysControllerBubbleSort(){
            this.setUpListeners();
            this.dataBind();
            this.array_vars.add("bubbles_array");
            this.vars.set("bubble_sort_index",0);
     }

    TaysControllerBubbleSort.prototype.shuffle=function(){
       var that=this;
       this.read_file("bubble_sort.php","POST").done(function(response){
        var bubbles_array = JSON.parse(response.responseText);
        var totalwidth=0; 
        for (var i = 0; i < bubbles_array.length; i++) {
            totalwidth+=bubbles_array[i];
        }
            that.vars.set("totalwidth",100);
            that.array_vars.bubbles_array._set(bubbles_array);
            document.getElementById("step").style.display="inline-block";
            document.getElementsByClassName("tays_td")[0].className += " active";
            document.getElementsByClassName("tays_td")[1].className += " active";

       });
    }

    TaysControllerBubbleSort.prototype.step=function(){
       var that=this;
       var params={};
       // increment index by one or reset
       var c_index=(this.vars.get("bubble_sort_index") >= 8) ? 0 : (+this.vars.get("bubble_sort_index")+1) ;
           this.vars.set("bubble_sort_index",c_index);
           params.bubbles_index=c_index;
           params.bubbles_array=this.array_vars.bubbles_array;

       this.read_file("bubble_sort.php","PUT",params).done(function(response){
         if(response.responseText=="complete"){
            document.getElementById("step").style.display="none";
            return;
         }
       var bubbles_array = JSON.parse(response.responseText);
            that.array_vars.bubbles_array._set(bubbles_array);
            document.getElementsByClassName("tays_td")[c_index].className += " active";
            document.getElementsByClassName("tays_td")[c_index+1].className += " active";
       });
    }

   TaysControllerBubbleSort.prototype.play=function(){
        if(document.getElementById("step").style.display !=="none"){
            var that=this;
            this.step();
             setTimeout(function(){ that.play(); },200);
        };
  }

   var c= new TaysControllerBubbleSort();
       c.shuffle();
}
</script>
</body>
</html>
