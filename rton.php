
 <link rel="stylesheet" href="./style.css">

<?php
$proc = $_GET['p']; 
$proc = substr($proc, 10);
$pt = $_GET['pt']; 
$efic = $_GET['e']; 
$hi = $_GET['hi'];
$hf = $_GET['hf'];
$lle = $_GET['l'];
$av = $_GET['a'];
$op = $_GET['o'];
//$hi = date("H:i", strtotime($hi));  

/*
echo " proc-->" . $proc . "<br>";
echo " pt-->" . $pt . "<br>";
echo " hi-->" . $hi . "<br>";
*/
$efic = round($efic, 2);
//echo " efic-->" . $efic . "<br>";		

?>

	
<!-- partial -->
  <script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js'></script><script  src="./script.js"></script>
  <script src="CountDown/CountDownJS.js"></script>
<?php

$dia  = strtok($hf, '/');
//echo " dia-->" . $dia . "<br>";
$mes = strtok('/');	
//echo " mes-->" . $mes . "<br>";
$mes = substr( $mes, 0, 2); 
//echo " mes-->" . $mes . "<br>";
$ano = "2020";	
//echo " ano-->" . $ano . "<br>";
$horafins  = strtok($hf, ':');
$horafins = (int)substr( $horafins, 5, 7); 
//echo " horafins-->" . $horafins . "<br>";
$min = strtok(':');	
//echo " min-->" . $min . "<br>";	

?>

<div align="center" style = "align:center; vertical-align: top; margin:0 auto; width: 73%">										
<script>
			countDown = new CountDownObject(<?php echo (int)$proc; ?>); //add countdown as object
			countDown.TIME_ZONE = +2, // your time zone (-12 ... +14)
			countDown.SET_YOUR_SEC = 10,
			countDown.SET_YOUR_MIN = <?php echo (int)$min; ?>,
			countDown.SET_YOUR_HOUR = <?php echo (int)$horafins; ?>,
			countDown.SET_YOUR_DAY = <?php echo (int)$dia; ?>,
			countDown.SET_YOUR_MONTH = <?php echo (int)$mes; ?>,
			countDown.SET_YOUR_YEAR = <?php echo (int)$ano; ?>,
			countDown.NUM_OF_ELEMENTS = 6, // number of flip-elements(from 1 to 9)
			countDown.NUM_OF_REMOVE_ELEMENTS = 2;
			countDown.TIME_ANIMATION = 900, // time of flip animation in milliseconds(from 50 to 950)
			countDown.TEXT_COLOR = "#d6dfe6", // text color under flip elements(seconds, minutes and etc.)
			countDown.IS_DYNAMIC_COLOR = false, // back color will vary(true or false)
			countDown.CANVAS_NAME = "CountDownCanvas";  //canvas name in html-code
			countDown.Start(); //start countdown
</script>
</div>
<li class="animated" data-provide="circular" data-fill-color="#00a19b" data-percent="true" data-initial-value="<?php echo ($efic) ?>" data-max-value="100" 
			        	data-label="<?php echo ($pt . "   &nbsp;&nbsp; " . $hi . "  " . $hf . "  " . $proc . "  " . $op . "  " . $av) ?>" data-title="<?php echo ($proc) ?>" data-dates="" style="width: 272px; height: 272px;">

