<?php 
header('Content-Type: text/html; charset=utf-8');

// Get a list of directories in ./data
// And remove first two (. and ..)
$dataDirs = cleanDirArray(scandir("./data"));

// What do we want to see?
if (!isset($_GET['s'])){
	$sentence = 0;
}else{
	$sentence = $_GET['s'];
}
if (!isset($_GET['directory'])){
	$dataDir = $dataDirs[0];
}else{
	$dataDir = $_GET['directory'];
}
$dataFiles = cleanDirArray(scandir("./data/".$dataDir));

$alignments = "./data/".$dataDir."/".array_pop(preg_grep("/\.ali\.js/", $dataFiles));
$sources = "./data/".$dataDir."/".array_pop(preg_grep("/\.src\.js/", $dataFiles));
$targets = "./data/".$dataDir."/".array_pop(preg_grep("/\.trg\.js/", $dataFiles));
$count = getLineCount($alignments)-3;
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta name="description" content="Soft Alignment Visualization">
	<meta name="author" content="Matīss Rikters">
	<title>Soft Alignment Visualization</title>
	<!--[if lt IE 9]>
		<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->
	<style>
		body{width:98%;}
		svg text{font-size:10px;}
		rect{shape-rendering:crispEdges;}
	</style>
</head>
<body>
<b>Neural MT alignment visualization</b> <small>(forked from <a href="https://github.com/rsennrich/nematus/tree/master/utils">Nematus utils</a>)</small><br/>
<div style="display:block;position:absolute; width:90%; text-align:center;">Data directory:
<form action="?">
<select name="directory">
<?php 
foreach($dataDirs as $directory){
	$selected = $dataDir==$directory?" SELECTED":"";
	echo "<option value='$directory'$selected>$directory</option>";
}
?>
</select>
<button type="submit">show</button>
</form>
</div> 
<br/>
<a href="?s=<?php echo $sentence>0?$sentence-1:$count-1;?>&directory=<?php echo $dataDir; ?>">< previous</a>
<a style="position:absolute; width:90%; text-align:center;display:block;">Showing sentence <?php echo $sentence+1; ?><a>
<a href="?s=<?php echo $sentence<$count-1?$sentence+1:0;?>&directory=<?php echo $dataDir; ?>" style="float:right;"> next ></a>
<div id="area1"></div>
<script src="http://d3js.org/d3.v3.min.js"></script>
<script src="attentionMR.js"></script>
<script>
<?php include($alignments); ?>; 
<?php include($sources); ?>; 
<?php include($targets); ?>; 
var target = [targets[<?php echo $sentence;?>]];
var source = [sources[<?php echo $sentence;?>]];
var sales_data=alignments[<?php echo $sentence;?>];

var width = 2200, height = 690, margin ={b:0, t:60, l:-20, r:0};
var c = "area1";
var svg = d3.select("#area1")
	.append("svg")
   .attr("preserveAspectRatio", "xMinYMin meet")
   .attr("viewBox", "0 0 600 400")
   .classed("svg-content-responsive", true)
	.append("g")
	.attr("transform","translate("+ margin.l+","+margin.t+")");

var data = [ 
	{data:bP.partData(sales_data,0,0,target,source), id:'SalesAttempts', header:["Channel","State", "Sales Attempts"]}
];

bP.draw(data, svg);
</script>
</body>
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