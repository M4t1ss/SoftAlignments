<?php 

function getLineCount($fileName){
	$linecount = 0;
	$handle = fopen($fileName, "r");
	while(!feof($handle)){
	  $line = fgets($handle);
	  $linecount++;
	}
	fclose($handle);
	return $linecount;
}

function cleanDirArray($data){
	array_shift($data);
	array_shift($data);
	return $data;
}

function getJSvalue($string){
	return str_replace('@@ ', '', str_replace('["', '', str_replace('"],', '', str_replace('", "', ' ', $string))));
}

function getSWvalue($string){
	return explode('", "', str_replace('asc@@', '', str_replace('["', '', str_replace('"],', '', trim($string)))));
}

function gotoLine($fileName, $sentence){
	$file = new SplFileObject($fileName);
	$file->seek($sentence);
	return $file;
}

function getScores($string, $score_num = NULL){
	$scores 	= explode(", ", str_replace("]", "", str_replace("[", "", $string)));
	$CDP 		= round($scores[0] * 100, 2);
	$APout 		= round($scores[1] * 100, 2);
	$APin 		= round($scores[2] * 100, 2);
	$confidence = round($scores[3] * 100, 2);
	$length 	= round($scores[4] * 100, 2);
	if($score_num!==null && $score_num < count($scores)){
		return round($scores[$score_num] * 100, 2);
	}else{
		return array($CDP, $APout, $APin, $confidence, $length);
	}
}

function getAllConfidences($file, $count){
	$confidences = array();
	for ($i = 1; $i <= $count; $i++){
		$file->seek($i);
		$confidences[] = getScores($file->current());
	}
	return $confidences;
}