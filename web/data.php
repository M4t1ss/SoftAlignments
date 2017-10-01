<?php 
// Report simple running errors
error_reporting(E_ERROR | E_WARNING | E_PARSE);
header('Content-Type: text/html; charset=utf-8');
include('functions.php');

// Get a list of directories in ./data
// And remove first two (. and ..)
foreach(glob('./data/*', GLOB_ONLYDIR) as $dir) {
    $dataDirs[] = basename($dir);
}

// What do we want to see?
if (!isset($_GET['s'])){
	$sentence = 1;
}else{
	$sentence = $_GET['s'];
}
if (!isset($_GET['directory'])){
	$dataDir = $dataDirs[0];
}else{
	$dataDir = $_GET['directory'];
}
if(!file_exists("./data/".$dataDir) || strlen($dataDir) < 1){
	die("Experiment directory not found!");
}
$dataFiles = cleanDirArray(scandir("./data/".$dataDir));

//Get the data files
$alignments 			= "./data/".$dataDir."/".array_pop(preg_grep("/\.ali\.js/", $dataFiles));
$sources 				= "./data/".$dataDir."/".array_pop(preg_grep("/\.src\.js/", $dataFiles));
$targets 				= "./data/".$dataDir."/".array_pop(preg_grep("/\.trg\.js/", $dataFiles));
$confidences 			= "./data/".$dataDir."/".array_pop(preg_grep("/\.con\.js/", $dataFiles));
$subword_confidences 	= "./data/".$dataDir."/".array_pop(preg_grep("/\.sc\.js/", $dataFiles));
$count = getLineCount($alignments)-3;

//Show only existing sentences
$sentence=$sentence<1?1:$sentence;
$sentence=$sentence>$count?$count:$sentence;

//Load only the one line from each input file
$f1 = gotoLine($alignments, $sentence);
$f2 = gotoLine($sources, $sentence);
$f3 = gotoLine($targets, $sentence);
$f4 = gotoLine($confidences, $sentence);
$f5 = gotoLine($subword_confidences, $sentence);

$source 	= getJSvalue($f2->current());
$target 	= getJSvalue($f3->current());
$CDP 		= getScores($f4->current(), 0);
$APout 		= getScores($f4->current(), 1);
$APin 		= getScores($f4->current(), 2);
$confidence = getScores($f4->current(), 3);

$subword_scores = explode("], [",str_replace("], ],","",str_replace("[[","",trim($f5->current()))));
$ssw = explode(", ",$subword_scores[0]);
$tsw = explode(", ",$subword_scores[1]);
$allConfidences = getAllConfidences($f4, $count);


//Data for our selected sentence
echo "{";
echo '"alignment_data" : '.str_replace("], ],","] ],",$f1->current());
echo '"source" : ['.str_replace("],","]",$f2->current()).'],';
echo '"target" : ['.str_replace("],","]",$f3->current()).'],';
echo '"count" : '.$count;
echo "}";