<?php 
header('Content-Type: text/html; charset=utf-8');
if (!isset($_GET['s'])){
	$_GET['s'] = 0;
}
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
$alignments = './baseline/alignment.npy.ali.js';
$sources = './baseline/alignment.npy.src.js';
$targets = './baseline/alignment.npy.trg.js';
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
Neural MT alignment visualization. <br/>
Forked from <a href="https://github.com/rsennrich/nematus/tree/master/utils">Nematus utils</a><br/>
<a href="?s=<?php echo $_GET['s']>0?$_GET['s']-1:$count-1;?>">< previous</a>
<a style="position:absolute; width:90%; text-align:center;">Showing sentence <?php echo $_GET['s']+1; ?><a>
<a href="?s=<?php echo $_GET['s']<$count-1?$_GET['s']+1:0;?>" style="float:right;"> next ></a>
<div id="area1"></div>
<script src="http://d3js.org/d3.v3.min.js"></script>
<script src="attentionMR.js"></script>
<script>
<?php include($alignments); ?>; 
<?php include($sources); ?>; 
<?php include($targets); ?>; 
var target = [targets[<?php echo $_GET['s'];?>]];
var source = [sources[<?php echo $_GET['s'];?>]];
var sales_data=alignments[<?php echo $_GET['s'];?>];

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
