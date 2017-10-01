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
$targets 				= "./data/".$dataDir."/".array_pop(preg_grep("/\.trg\.js/", $dataFiles));
$confidences 			= "./data/".$dataDir."/".array_pop(preg_grep("/\.con\.js/", $dataFiles));
$subword_confidences 	= "./data/".$dataDir."/".array_pop(preg_grep("/\.sc\.js/", $dataFiles));
$count = getLineCount($targets)-2;

//Show only existing sentences
$sentence=$sentence<1?1:$sentence;
$sentence=$sentence>$count?$count:$sentence;

//Load only the one line from each input file
$f3 = gotoLine($targets, $sentence);
$f4 = gotoLine($confidences, $sentence);
$f5 = gotoLine($subword_confidences, $sentence);

$target 	= getJSvalue($f3->current());
$CDP 		= getScores($f4->current(), 0);
$APout 		= getScores($f4->current(), 1);
$APin 		= getScores($f4->current(), 2);
$confidence = getScores($f4->current(), 3);

$subword_scores = explode("], [",str_replace("], ],","",str_replace("[[","",trim($f5->current()))));
$tsw = explode(", ",$subword_scores[1]);


?>
	<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
		<span data-toggle="collapse" data-target="#c5" class="label label-default myLabel" onclick="toggleChart('c5')">Translation</span> 
		<div style="width:50%; float:left;" class="pr">
		<span class="label label-danger" style="cursor:help;padding:4px;"><?php 
		$sc=0;
		foreach(getSWvalue($f3->current()) as $targetToken){
			echo str_replace("@@</span> ","</span>",'<span data-toggle="tooltip" data-placement="top" title="Confidence: '.round($tsw[$sc]*100,2).'%">'.htmlspecialchars($targetToken).'</span> ');
			$sc++;
		}
		?></span>
		</div>
	</div>
	<div class="col-xs-12 col-sm-6 col-md-3 col-lg-3">
		<span data-toggle="collapse" data-target="#c1" class="label label-default myLabel" onclick="toggleChart('c1')">Confidence</span> 
		<div class="progress pr" style="width:50%; float:left;">
			<div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="<?php echo $confidence; ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $confidence; ?>%;">
				<?php echo $confidence; ?>%
			</div>
		</div>
	</div>
	<div class="col-xs-12 col-sm-6 col-md-3 col-lg-3">
		<span data-toggle="collapse" data-target="#c2" class="label label-default myLabel" onclick="toggleChart('c2')">CDP</span> 
		<div class="progress pr" style="width:50%; float:left;">
			<div class="progress-bar progress-bar-warning" role="progressbar" aria-valuenow="<?php echo $CDP; ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $CDP; ?>%;">
				<?php echo $CDP; ?>%
			</div>
		</div>
	</div>
	<div class="col-xs-12 col-sm-6 col-md-3 col-lg-3">
		<span data-toggle="collapse" data-target="#c3" class="label label-default myLabel" onclick="toggleChart('c3')">APout</span> 
		<div class="progress pr" style="width:50%; float:left;">
			<div class="progress-bar" role="progressbar" aria-valuenow="<?php echo $APout; ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $APout; ?>%;">
				<?php echo $APout; ?>%
			</div>
		</div>
	</div>
	<div class="col-xs-12 col-sm-6 col-md-3 col-lg-3">
		<span data-toggle="collapse" data-target="#c4" class="label label-default myLabel" onclick="toggleChart('c4')">APin</span> 
		<div class="progress pr" style="width:50%; float:left;">
			<div class="progress-bar progress-bar-info" role="progressbar" aria-valuenow="<?php echo $APin; ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $APin; ?>%;">
				<?php echo $APin; ?>%
			</div>
		</div>