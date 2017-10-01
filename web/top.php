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
$sources 				= "./data/".$dataDir."/".array_pop(preg_grep("/\.src\.js/", $dataFiles));
$subword_confidences 	= "./data/".$dataDir."/".array_pop(preg_grep("/\.sc\.js/", $dataFiles));
$count = getLineCount($sources)-2;

//Show only existing sentences
$sentence=$sentence<1?1:$sentence;
$sentence=$sentence>$count?$count:$sentence;

//Load only the one line from each input file
$f2 = gotoLine($sources, $sentence);
$f5 = gotoLine($subword_confidences, $sentence);

$subword_scores = explode("], [",str_replace("], ],","",str_replace("[[","",trim($f5->current()))));
$ssw = explode(", ",$subword_scores[0]);


?>
<p style="margin-left:15px;">
    <span class="label label-default" style="width: 100px;display: inline-block;padding: 4px;">Source</span> 
    <span class="label label-danger" style="cursor:help;"><?php 
    $sc=0;
    foreach(getSWvalue($f2->current()) as $sourceToken){
        echo str_replace("@@</span> ","</span>",'<span data-toggle="tooltip" data-placement="bottom" title="Confidence: '.round($ssw[$sc]*100,2).'%">'.htmlspecialchars($sourceToken).'</span> ');
        $sc++;
    }
    ?></span>
</p>