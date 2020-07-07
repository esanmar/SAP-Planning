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
</head>
<body>
<div class="container">
			<ul class="graphs stats-container centered biggie">			
    
<?php
$cont=0;
$database="imaje"; 
$username="root"; 
$password="soft5"; 
$host="172.16.0.24"; 
$ct = $_GET['ct']; 

$connect = mysql_connect($host,$username,$password) or die('<P class=error>Unable to connect to the database server at this time.</P>');   
@mysql_select_db($database) or die( "Unable to select database");

$sql = "SELECT numproc, puesto  FROM trescantosoec WHERE puesto LIKE '" . $ct ."%' GROUP BY numproc";
$result = mysql_query($sql);
while($row = mysql_fetch_array($result))
{ 
			$proceso = $row['0'];
			$puesto = $row['1'];
			$sql2 = "SELECT SUM(valorespunto) as puntos, COUNT(orden) as numof, horaini, fechacrea  FROM trescantosoec WHERE numproc ='" . $proceso ."' GROUP BY numproc";
			//echo "<br> sql-->" . $sql2 . "<br>";
			$result2 = mysql_query($sql2);
			while($row2 = mysql_fetch_array($result2))
			{ 
				$puntos = $row2['0'];
				$puntos80 = $puntos / 80;
				$numof = $row2['1'];
				$horaini = $row2['2'];
				$fechaini = $row2['3'];
				$fini = $fechaini . " " . $horaini;
				
			}
			/*
			echo " PROCESO-->" . $proceso;
			echo " numof-->" . $numof;
			*/
			
			//echo " PUNTOS-->" . round($puntos80, 2);
			
			$cadenaini = date("d-m-Y H:i", strtotime($fini));  
			$fini = date("d/m H:i", strtotime($fini));  
			//echo " cadenaini-->" . $cadenaini;
			$cadenafin = date("d-m-Y H:i");  
			//echo " cadenafin-->" . $cadenafin;
			
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
			
			
		  //echo " PUNTOS80-->" . $puntos80;
			if (($puntos80 / $diff) > 1)
			{
				$efi = 100;
				//$ffin = date('d/m H:i', strtotime('+' . $hourspuntos . ' hour +'. $minpuntos . ' minutes', strtotime($cadenaini)));
				//$ffin = date('d/m H:i', strtotime('+' . $hours . ' hour +'. $min . ' minutes', strtotime($cadenaini)));
			}	
			else
			{
				$efi = ($puntos80 / $diff) * 100;
				//$ffin = date('d/m H:i', strtotime('+' . $hourspuntos . ' hour +'. $minpuntos . ' minutes', strtotime($cadenaini)));
			}
			
			$ffin = date('d/m H:i', strtotime('+' . $hourspuntos . ' hour +'. $minpuntos . ' minutes', strtotime($cadenaini)));
			//echo " ffin-->" . $ffin;
				
			//echo " EFICIENCIA-->" . round($efi, 2) . "%<br>";
			
			$cont = $cont + 1;
?>		
				<?php 
				$pr = "http://srvpw02/conta3c/rton.php?p=" . $proceso . "&e=" . $efi. "&pt=" . $puesto . "&hi=" . $fini. "&hf=" . $ffin; 
				$cr = "http://srvpw02/conta3c/crono.php?hf=" . $ffin; 
				//$pr = "http://srvpw02/conta3c/index.html"; 
				//echo " URL-->" . $pr . "<br>";
				?>
				<iframe height="285px" frameborder=0 margin=0 scrolling=no src="<?php echo ($pr) ?>"></iframe>
			
<?php
			if(( $cont % 6 ) == 0)
			{
?>		
		</ul>
		<ul class="graphs stats-container centered biggie">
<?php		
		}				
}
?>
	</ul>
</div>
<!-- partial -->
  <script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js'></script><script  src="./script.js"></script>

</body>
</html>