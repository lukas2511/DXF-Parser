<?php

$debug=1;

echo 'require("luacrumbs")'."\n";
//echo 'luacrumbs.generateSVG(true)'."\n";
echo 'luacrumbs.init("bla")'."\n";

$inv=array();

function getdimensions($file,$line){
	global $inv,$debug;
	$i=$line+1;
	$seek="";
	$values=array();
	while(is_numeric(trim($file[$i]))){
		switch(trim($file[$i])){
			case '10': $seek="X"; break;
			case '20': $seek="Y"; break;
			case '30': $seek="Z"; break;
			default:
				if($seek!=""){
					$values[$seek]=trim($file[$i]);
				}
				$seek="";
				break;
		}
		$i++;
	}
	if(isset($values['X'])){ $inv['X']=$values['X']; }
	if(isset($values['Y'])){ $inv['Y']=$values['Y']; }
	if(isset($values['Z'])){ $inv['Z']=$values['Z']; }
	if(isset($values['X']) && isset($values['Y']) && $debug){
		echo "luacrumbs.line(0,0,0,".$values['Y'].")\n";
		echo "luacrumbs.line(0,".$values['Y'].",".$values['X'].",".$values['Y'].")\n";
		echo "luacrumbs.line(".$values['X'].",".$values['Y'].",".$values['X'].",0)\n";
		echo "luacrumbs.line(0,0,".$values['X'].",0)\n";
	}
}

function parsecircle($file,$line){
	global $inv;
	$i=$line+1;
	$seek="";
	$values=array();
	$break=0;
	while(is_numeric(trim($file[$i]))){
		switch(trim($file[$i])){
			case '10': $seek="X"; break;
			case '20': $seek="Y"; break;
			case '30': $seek="Z"; break;
			case '40': $seek="R"; break;
			// case '11': 
			// case '21':
			// case '31':
			// case '210': 
			// case '220':
			// case '230':
			default:
				if($break) break;
				if($seek!=""){
					$values[$seek]=trim($file[$i]);
					if($seek=="Y" && isset($inv[$seek])){
						$values[$seek]=$inv[$seek]-$values[$seek];
					}
				}
				$seek="";
				break;
		}
		if($break) break;
		$i++;
	}
	if(trim($file[$i])!="AcDbArc"){
		echo "luacrumbs.pencilUp()\n";
		echo "luacrumbs.moveTo(".$values['X'].",".$values['Y'].")\n";
		echo "luacrumbs.pencilDown()\n";
		echo "luacrumbs.pencilUp()\n";
		global $debug;
		if($debug){
			echo "luacrumbs.pencilDown()\n";
			echo "luacrumbs.circleAt(".$values['X'].", ".$values['Y'].", ".$values['R'].", true)\n";
			echo "luacrumbs.pencilUp()\n";
		}
	}
}

$file=file($_SERVER['argv'][1]);

foreach($file as $line => $cont){
	$cont=trim($cont);
	switch($cont){
		case '$EXTMAX': getdimensions($file,$line); break;
		case 'AcDbCircle': parsecircle($file,$line); break;
	}
}

echo 'luacrumbs.pencilUp()'."\n";
echo 'luacrumbs.close()'."\n";

?>
