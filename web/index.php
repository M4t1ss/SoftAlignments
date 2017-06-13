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

$source = str_replace('", "', ' ', $f2->current());
$target = str_replace('", "', ' ', $f3->current());
$source = str_replace('"],', '', $source);
$target = str_replace('"],', '', $target);
$source = str_replace('["', '', $source);
$target = str_replace('["', '', $target);
$source = str_replace('@@ ', '', $source);
$target = str_replace('@@ ', '', $target);

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
		svg{position: relative; z-index: -1;}
		svg text{font-size:6px;}
		p{font-size:16px;}
		rect{shape-rendering:crispEdges;}
		
		@media (min-width: 768px) {
		  .navbar-nav.navbar-center {
			position: absolute;
			left: 50%;
			transform: translatex(-50%);
		  }
		}
	</style>
	<link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
	<link rel="stylesheet" href="bootstrap/select/bootstrap-select.min.css">
	<script src="bootstrap/js/jquery-3.2.1.slim.min.js"></script>
	<script src="bootstrap/js/bootstrap.min.js"></script>
	<script src="bootstrap/select/bootstrap-select.min.js"></script>
</head>
<body>
<nav class="navbar navbar-default navbar-fixed-top">
  <div class="container-fluid">
    <!-- Brand and toggle get grouped for better mobile display -->
    <div class="navbar-header">
      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand" href="#">NMT alignment visualization</a>
    </div>

    <!-- Collect the nav links, forms, and other content for toggling -->
    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
      <ul class="nav navbar-nav navbar-center">
		<li>
			<a href="?s=<?php echo $sentence>1?$sentence-1:$count;?>&directory=<?php echo $dataDir; ?>">
				<span class="glyphicon glyphicon-arrow-left" aria-hidden="true"></span>
			</a>
		</li>
		<li><p class="navbar-text">Showing sentence</p></li>
        <li>
			<form class="navbar-form" action="?" method="GET">
				<input class="form-control" style="width:75px;" name="s" value="<?php echo $sentence; ?>" type="text" /> 
				<button class="btn btn-default" type="submit">
					<span class="glyphicon glyphicon-refresh" aria-hidden="true"></span>
				</button>
				<input type="hidden" name="directory" value="<?php echo $dataDir; ?>" />
				<input type="hidden" name="changeNum" value="True" />
			</form>
		</li>
		<li>
			<a href="?s=<?php echo $sentence<$count?$sentence+1:1;?>&directory=<?php echo $dataDir; ?>">
				<span class="glyphicon glyphicon-arrow-right" aria-hidden="true"></span>
			</a>
		</li>
      </ul>
      <ul class="nav navbar-nav navbar-right">
		<li><p class="navbar-text">Data directory</p></li>
        <li style="padding-top:8px;">
			<form action="?">
				<select class="selectpicker" data-live-search="true" name="directory" onchange="this.form.submit()">
				<?php 
				foreach($dataDirs as $directory){
					$selected = $dataDir==$directory?" SELECTED":"";
					echo "<option value='$directory'$selected>$directory</option>";
				}
				?>
				</select>
			</form>
        </li>
      </ul>
    </div>
  </div>
</nav>
<br/>
<br/>
<br/>
<br/>
<p style="text-align:center; margin-bottom:-70px;"><span class="label label-success">Source</span> <span class="label label-default"><?php echo $source; ?></span></p>
<div id="area1"></div>
<p style="text-align:center; margin-top:-5px;"><span class="label label-warning">Translation</span> <span class="label label-default"><?php echo $target; ?></span></p>
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