<?php 
header('Content-Type: text/html; charset=utf-8');

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
$dataFiles = cleanDirArray(scandir("./data/".$dataDir));

//Get the data files
$alignments = "./data/".$dataDir."/".array_pop(preg_grep("/\.ali\.js/", $dataFiles));
$sources = "./data/".$dataDir."/".array_pop(preg_grep("/\.src\.js/", $dataFiles));
$targets = "./data/".$dataDir."/".array_pop(preg_grep("/\.trg\.js/", $dataFiles));
$count = getLineCount($alignments)-3;

//Show only existing sentences
$sentence=$sentence<1?1:$sentence;
$sentence=$sentence>$count?$count:$sentence;

//Load only the one line from each input file
$f1 = new SplFileObject($alignments);
$f2 = new SplFileObject($sources);
$f3 = new SplFileObject($targets);

//The line of the sentence
$f1->seek($sentence);
$f2->seek($sentence);
$f3->seek($sentence);

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
		svg text{font-size:6px;}
		rect{shape-rendering:crispEdges;}
	</style>
</head>
<body>
<b>Neural MT alignment visualization</b> <small>(forked from <a href="https://github.com/rsennrich/nematus/tree/master/utils">Nematus utils</a>)</small><br/>
<div style="text-align:center; display:block;">
	<form action="?">
		<span>Data directory: </span><select name="directory" onchange="this.form.submit()">
		<?php 
		foreach($dataDirs as $directory){
			$selected = $dataDir==$directory?" SELECTED":"";
			echo "<option value='$directory'$selected>$directory</option>";
		}
		?>
		</select>
	</form><br/>
	<form action="?" method="GET" style="margin-top:-10px;">
		Showing sentence <input name="s" value="<?php echo $sentence; ?>" type="text" style="width:35px; height:14px;"/>. <input type="submit" value="Change"/>
		<input type="hidden" name="directory" value="<?php echo $dataDir; ?>" />
		<input type="hidden" name="changeNum" value="True" />
	</form><br/>
	<a style="display:inline; float:left;margin-top:-50px;" href="?s=<?php echo $sentence>1?$sentence-1:$count;?>&directory=<?php echo $dataDir; ?>">< previous</a>
	<a style="display:inline; float:right;margin-top:-50px;" href="?s=<?php echo $sentence<$count?$sentence+1:1;?>&directory=<?php echo $dataDir; ?>"> next ></a>
</div>
<div id="area1"></div>
<script src="http://d3js.org/d3.v3.min.js"></script>
<script src="attentionMR.js"></script>
<script>
<?php
//Data for our selected sentence
echo "var alignment_data = ".str_replace("], ],","] ];",$f1->current());
echo "var source = [".str_replace("],","]];",$f2->current());
echo "var target = [".str_replace("],","]];",$f3->current());
?>
var width = 2200, height = 690, margin ={b:0, t:60, l:-20, r:0};
var c = "area1";
var svg = d3.select("#area1")
	.append("svg")
   .attr("preserveAspectRatio", "xMinYMin meet")
   .attr("viewBox", "0 0 600 250")
   .classed("svg-content-responsive", true)
	.append("g")
	.attr("transform","translate("+ margin.l+","+margin.t+")");

var data = [ 
	{data:bP.partData(alignment_data,source,target), id:'SubWordAlignments'}
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