<?php

function splitArray($data=Array(), $size){
	if (sizeof($data>0)){
		$sliceArray = [];
		$i = 0;
		while(sizeof($data)>0){
		  for($j=0;$j<$size;$j++){
			  $sliceArray[$i][$j] = array_shift($data);
		  }
		  $i++;
		}
	}
	return $sliceArray;
}

$bigArray = [];

for($i=0;$i<1000;$i++){
 $bigArray[$i] = $i;
}

$newArray = splitArray($bigArray,200);

print sizeof($newArray);
print_r($newArray[4]);
