<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="robots" content="noindex, nofollow">
  <meta name="googlebot" content="noindex, nofollow">
  <title>KPI</title>
<link rel="stylesheet" href="./style.css">
<style type="text/css">
#iframe1 { vertical-align: top; }
</style>

</head>
<body>
<div class="container">
			<ul id="menu" style="margin: 0px; padding: 0px;">			
    
<?php
$i = 0;
if (!empty($_GET['i']))
	$i = (int)$_GET['i']; 

$f = 0;
if (!empty($_GET['f']))
	$f = (int)$_GET['f']; 

$cont=0;
$database="imaje"; 
$username="root"; 
$password="soft5"; 
$host="172.16.0.24"; 
$ct = $_GET['ct']; 

$connect = mysql_connect($host,$username,$password) or die('<P class=error>Unable to connect to the database server at this time.</P>');   
@mysql_select_db($database) or die( "Unable to select database");

$sql = "SELECT numproc, puesto  FROM trescantosoec WHERE puesto LIKE '" . $ct ."%' GROUP BY numproc ORDER BY puesto, fechacrea, horaini ASC";
$result = mysql_query($sql);
while($row = mysql_fetch_array($result))
{ 
			$proceso = $row['0'];
			$puesto = $row['1'];
			$sql2 = "SELECT SUM(valorespunto) as puntos, COUNT(orden) as numof, horaini, fechacrea, numpers, op  FROM trescantosoec WHERE numproc ='" . $proceso ."' GROUP BY numproc order by puesto ASC";
			//echo "<br> sql-->" . $sql2 . "<br>";
			$result2 = mysql_query($sql2);
			while($row2 = mysql_fetch_array($result2))
			{ 
				$puntos = $row2['0'];
				$puntos80 = $puntos / 80;
				$numof = $row2['1'];
				$horaini = $row2['2'];
				$fechaini = $row2['3'];
				$avion = $row2['4'];
				$op = $row2['5'];
				$fini = $fechaini . " " . $horaini;
				
			}
			/*
			echo " PROCESO-->" . $proceso;
			echo " numof-->" . $numof;
			*/
			
			//echo " PUNTOS-->" . strval(round($puntos80, 0));
				$horasuma = strval(round($puntos80, 0));
			$cadenaini = date("d-m-Y H:i", strtotime($fini)); 
			$cadenainiturno = strval(date("Y-m-d H:i", strtotime($fini))); 
			$fini = date("d/m H:i", strtotime($fini));  
			//echo " cadenaini-->" . $cadenainiturno . "--";
			$cadenafin = date("d-m-Y H:i");  
			//echo " cadenafin-->" . $cadenafinturno . "--";
			
			$diff = abs(strtotime($cadenafin) - strtotime($cadenaini))/3600;  
			//echo " diff-->" . round($diff, 2);
			$hfin = str_replace(".", ":", round($diff, 2));
			$hfinpuntos = str_replace(".", ":", round($puntos80, 2));
			//echo " hfin-->" . $hfinpuntos;
			$split = explode(':', $hfin);
			$splitpuntos = explode(':', $hfinpuntos);
			$hours = array_shift(explode(':', $hfin));
			$hourspuntos = array_shift(explode(':', $hfinpuntos));
			$min = (int)$split[1];
			$minpuntos = (int)$splitpuntos[1];
			$min = round($min * 0.6, 0);
			$minpuntos = round($minpuntos * 0.6, 0);
			//echo " hours-->" . $hourspuntos;
			//echo " min-->" . $minpuntos;
			
			$lleva = 0;
		  //echo " PUNTOS80-->" . $puntos80;
			if (($puntos80 / $diff) > 1)
			{
				$efi = 100;
				$lleva = round(($diff * 100) / $puntos80, 0);
			}	
			else
			{
				$efi = ($puntos80 / $diff) * 100;
			}
			
			if ($i !== 0 && $f !== 0 && $horasuma !== "0") //MIRAR CALENDARIO**************
			{
				$newdate = add_deadline($cadenainiturno, $horasuma, strval($i),  strval($f));
				//echo " newdate-->" . $newdate . "--";
				$newdate2 = date('d/m H:i', strtotime($newdate)); 
				//echo " newdate2-->" . $newdate2 . "--";
				$ffin =$newdate2;
			} else  {
				$ffin = date('d/m H:i', strtotime('+' . $hourspuntos . ' hour +'. $minpuntos . ' minutes', strtotime($cadenaini)));
			 // echo " ffin-->" . $ffin;
			}
		
				
			//echo " EFICIENCIA-->" . round($efi, 2) . "%<br>";
			
			$cont = $cont + 1;
?>		
				<?php 
				$pr = "http://srvpw02/conta3c/rto0320.php?p=" . $proceso . "&e=" . $efi. "&pt=" . $puesto . "&hi=" . $fini. "&hf=" . $ffin . "&l=" . $lleva . "&a=" . $avion . "&o=" . $op; 
				$cr = "http://srvpw02/conta3c/crono.php?hf=" . $ffin; 
				//$pr = "http://srvpw02/conta3c/index.html"; 
				//echo " URL-->" . $pr . "<br>";
				?>
				
				<iframe height="370px" frameborder=0 margin=0 scrolling=no src="<?php echo ($pr) ?>"></iframe>
				<!--<iframe height="75px" frameborder=0 margin=0 scrolling=no src="<?php echo ($cr) ?>"></iframe>-->
				</li> 
				
<?php
			//if(( $cont % 6 ) == 0)
			//{
?>
		<!--
		<ul id="menu" style="margin: 0px; padding: 0px;">	
		<ul>
		-->
<?php		
		//}				
}
?>
	</ul>
</div>
<!-- partial -->
  <script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js'></script><script  src="./script.js"></script>
  
  <?php
  
function add_deadline($givenDate, $hours, $ini, $fin){
	$ini = $ini . "00";
	$ini = (int)$ini;
	//echo " i-->" . $ini ;
	$fin = $fin . "00";
	$fin = (int)$fin;
	//echo " f-->" . $fin ;
	
	$range = (ceil($hours/7)*120);
	$cnt=1;
	$days = array();
	$goodhours = array();
	$days[] = date("Y-m-d", strtotime($givenDate));
	foreach(range(1,$range) as $num):
	
		$datetime = date("Y-m-d H:i:s", strtotime('+'.$num.' hour',strtotime($givenDate)));
		$date = date("Y-m-d", strtotime('+'.$num.' hour',strtotime($givenDate)));
		$time = date("Hi", strtotime('+'.$num.' hour',strtotime($givenDate)));
		$day = date("D", strtotime('+'.$num.' hour', strtotime($givenDate)));
		//if($day != 'Sat' && $day != 'Sun' && $time >= 600 && $time <= 1700):
		if($day != 'Sat' && $day != 'Sun' && $time >= $ini && $time <= $fin):
		
			if(!in_array($date, $days)){
				$days[] = $date;
			}else{
				$goodhours[$cnt] = $datetime;
				
				if($cnt >= $hours && array_key_exists($hours,$goodhours)):
					return $goodhours[$hours];
					break;
				endif;
				
				$cnt++;
			}
		endif;
		
	endforeach;
}

?>
</body>
</html>