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
	$CDP 		= $scores[0];
	$APout 		= $scores[1];
	$APin 		= $scores[2];
	$confidence = $scores[3];
	$length_pr 	= $scores[4];
	$length 	= $scores[5];
	if($score_num!==null && $score_num < count($scores)){
		return $scores[$score_num];
	}else{
		return array($CDP, $APout, $APin, $confidence, $length_pr, $length);
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