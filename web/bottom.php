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
$targets 				= "./data/".$dataDir."/".array_pop(preg_grep("/\.trg\.js/", $dataFiles));
$references 			= "./data/".$dataDir."/".array_pop(preg_grep("/\.ref\.txt/", $dataFiles));
$confidences 			= "./data/".$dataDir."/".array_pop(preg_grep("/\.con\.js/", $dataFiles));
$subword_confidences 	= "./data/".$dataDir."/".array_pop(preg_grep("/\.sc\.js/", $dataFiles));
$count = getLineCount($targets)-2;

//Show only existing sentences
$sentence=$sentence<1?1:$sentence;
$sentence=$sentence>$count?$count:$sentence;

//Load only the one line from each input file
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
$similarity	= getScores($f4->current(), 6);
$BLEU 		= getScores($f4->current(), 7);

//Are there any references given?
if($references!="./data/".$dataDir."/"){
    $f6 = gotoLine($references, $sentence-1);
    $reference = str_replace("@@ ", "", trim($f6->current()));
}

$subword_scores = explode("], [",str_replace("], ],","",str_replace("[[","",trim($f5->current()))));
$tsw = explode(", ",$subword_scores[1]);

$source = replaceStuff($source);
$target = replaceStuff($target);

$longestMatch 	= str_replace("<EOS>","",trim(getLongestCommonSubsequence(trim($source), trim($target))));
$matchLen 		= strlen($longestMatch);
$matchStart 	= strpos(trim($target), $longestMatch);
$matchEnd 		= $matchStart+$matchLen;
$currentName    = str_replace(".js","",array_pop(preg_grep("/\.ali\.js/", cleanDirArray(scandir("./data/".$dataDir)))));
$bottom = strpos($dataDir, "NMT2") !== false;
?>
	<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" title="Translation from file '<?php echo $currentName; ?>'">
    <span class="label label-default myLabel" style="cursor:default; margin-bottom:3px;">File</span> 
    <div style="width:50%; float:left; margin-top:-2px;" class="pr"><span class="label" style="background-color: <?php echo $bottom?"#339933":"#FF5000";?>; padding:3px;"><?php echo $currentName; ?></span></div>
    <br/>
		<span data-toggle="collapse" data-target="#sortable-5" class="label label-default myLabel" onclick="toggleChart('sortable-5')">Translation</span> 
		<div style="width:50%; float:left; margin-top:-2px;" class="pr">
		<span class="label label-danger" style="cursor:help;padding:3px;"><?php 
		$sc=0;
		$pos=0;
		$posZ[] = $pos;
		$output = "";
		foreach(getSWvalue($f3->current()) as $targetToken){
            $targetToken = replaceStuff($targetToken);
			
			$strip = str_replace("@@", "", $targetToken);
			$under = (strlen($strip) > 0 
						&& strpos($longestMatch, $strip) > -1 
						&& $pos >= $matchStart-5 
						&& $pos <= $matchEnd) ? ' style="border-bottom: 1px solid;"' : '';
			$posZ[$pos] = $strip;
			$pos += strlen($strip);
			if($strip == $targetToken) $pos += 1;
			
			
			$output .= str_replace(
				"@@</span> ",
				"</span>",
				'<span'
				.$under
				.'><span data-toggle="tooltip" data-placement="top" title="Confidence: '
				.round($tsw[$sc]*100,2).'%">'
				.htmlspecialchars($targetToken)
				.'</span> '
				);
			$sc++;
		}
		if(strpos($output, '▁') !== false) {
			$output = format_sentencepiece($output);
		}
		echo $output;
		?></span>
		</div>
	</div>
<?php
if($reference){
?>
	<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
		<span class="label label-default myLabel" style="cursor: default;">Reference</span> 
		<div style="width:50%; float:left; margin-top:-2px;" class="pr">
            <span class="label" style="padding:3px; background-color:gray;">
                <?php echo $reference; ?>
            </span>
		</div>
	</div>
<?php
}
printChart("Confidence", $confidence, "success", "sortable-1");
printChart("CDP", $CDP, "warning", "sortable-2");
printChart("APout", $APout, "default", "sortable-3");
printChart("APin", $APin, "info", "sortable-4");

if($BLEU > 0){
    printChart("Overlap", $similarity, "pink", "sortable-7", "col-xs-12 col-sm-6 col-md-6 col-lg-6");
    printChart("BLEU", $BLEU, "purple", "sortable-6", "col-xs-12 col-sm-6 col-md-6 col-lg-6");
}else{
    printChart("Overlap", $similarity, "pink", "sortable-7", "col-xs-12 col-sm-12 col-md-12 col-lg-12");
}