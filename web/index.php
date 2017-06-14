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
$confidences = "./data/".$dataDir."/".array_pop(preg_grep("/\.con\.js/", $dataFiles));
$count = getLineCount($alignments)-3;

//Show only existing sentences
$sentence=$sentence<1?1:$sentence;
$sentence=$sentence>$count?$count:$sentence;

//Load only the one line from each input file
$f1 = new SplFileObject($alignments);
$f2 = new SplFileObject($sources);
$f3 = new SplFileObject($targets);
$f4 = new SplFileObject($confidences);

//The line of the sentence
$f1->seek($sentence);
$f2->seek($sentence);
$f3->seek($sentence);
$f4->seek($sentence);

$scores 	= explode(", ", str_replace("]", "", str_replace("[", "", $f4->current())));
$source 	= getJSvalue($f2->current());
$target 	= getJSvalue($f3->current());
$CDP 		= round($scores[0] * 100, 2);
$APout 		= round($scores[1] * 100, 2);
$APin 		= round($scores[2] * 100, 2);
$confidence = round($scores[3] * 100, 2);

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
		br{clear:both;}
		.pr{margin-bottom:10px !important;}
		
		@media (min-width: 768px) {
		  .navbar-nav.navbar-center {
			position: absolute;
			left: 50%;
			transform: translatex(-50%);
		  }
		}
	</style>
	<link rel="stylesheet" href="scripts/css/bootstrap.min.css">
	<link rel="stylesheet" href="scripts/select/bootstrap-select.min.css">
	<script src="scripts/js/jquery-3.2.1.slim.min.js"></script>
	<script src="scripts/js/bootstrap.min.js"></script>
	<script src="scripts/select/bootstrap-select.min.js"></script>
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
      <a class="navbar-brand" href="#">NMT attention alignments</a>
    </div>

    <!-- Collect the nav links, forms, and other content for toggling -->
    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
      <ul class="nav navbar-nav navbar-center">
		<li>
			<a href="?s=<?php echo $sentence>1?$sentence-1:$count;?>&directory=<?php echo $dataDir; ?>">
				<span class="glyphicon glyphicon-arrow-left" aria-hidden="true"></span>
			</a>
		</li>
        <li>
			<form class="navbar-form" action="?" method="GET">
				<button id="save" style="display:inline;" class="btn btn-default">
					<span class="glyphicon glyphicon-save" aria-hidden="true"></span>
				</button>
				<input class="form-control" style="width:75px; display:inline;" name="s" value="<?php echo $sentence; ?>" type="text" /> 
				<button style="display:inline;" class="btn btn-default" type="submit">
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
        <li style="padding-top:8px; padding-right:5px;">
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
<div style="height:55px;display:block;"></div>
<div class="row" style="margin-left:5px;">
	<p style="margin-left:20px;">
		<span class="label label-default" style="width: 100px;display: inline-block;padding: 4px;">Source</span> 
		<span class="label label-danger"><?php echo $source; ?></span>
	</p>
</div>
<div class="row">
	<div id="svg"></div>
</div>
<div class="row" style="margin-left:5px;">
	<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
		<span class="label label-default" style="width: 100px;display: inline-block;float:left; padding:4px; vertical-align:middle;margin-right:5px;">Translation</span> 
		<div style="width:50%; float:left;" class="pr">
			<span class="label label-danger" style="padding:4px;"><?php echo $target; ?></span>
		</div>
	</div>
	<div class="col-xs-12 col-sm-6 col-md-3 col-lg-3">
		<span class="label label-default" style="width: 100px;display: inline-block;float:left; padding:4px; vertical-align:middle;margin-right:5px;">Confidence</span> 
		<div class="progress pr" style="width:50%; float:left;">
			<div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="<?php echo $confidence; ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $confidence; ?>%;">
				<?php echo $confidence; ?>%
			</div>
		</div>
	</div>
	<div class="col-xs-12 col-sm-6 col-md-3 col-lg-3">
		<span class="label label-default" style="width: 100px;display: inline-block;float:left; padding:4px; vertical-align:middle;margin-right:5px;">CDP</span> 
		<div class="progress pr" style="width:50%; float:left;">
			<div class="progress-bar progress-bar-warning" role="progressbar" aria-valuenow="<?php echo $CDP; ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $CDP; ?>%;">
				<?php echo $CDP; ?>%
			</div>
		</div>
	</div>
	<div class="col-xs-12 col-sm-6 col-md-3 col-lg-3">
		<span class="label label-default" style="width: 100px;display: inline-block;float:left; padding:4px; vertical-align:middle;margin-right:5px;">APout</span> 
		<div class="progress pr" style="width:50%; float:left;">
			<div class="progress-bar" role="progressbar" aria-valuenow="<?php echo $APout; ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $APout; ?>%;">
				<?php echo $APout; ?>%
			</div>
		</div>
	</div>
	<div class="col-xs-12 col-sm-6 col-md-3 col-lg-3">
		<span class="label label-default" style="width: 100px;display: inline-block;float:left; padding:4px; vertical-align:middle;margin-right:5px;">APin</span> 
		<div class="progress pr" style="width:50%; float:left;">
			<div class="progress-bar progress-bar-info" role="progressbar" aria-valuenow="<?php echo $APin; ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $APin; ?>%;">
				<?php echo $APin; ?>%
			</div>
		</div>
	</div>
</div>
<script src="scripts/d3.v3.min.js"></script>
<script src="scripts/attentionMR.js"></script>
<script src="scripts/saveSvgAsPng.js"></script>
<script>
<?php
//Data for our selected sentence
echo "var alignment_data = ".str_replace("], ],","] ];",$f1->current());
echo "var source = [".str_replace("],","]];",$f2->current());
echo "var target = [".str_replace("],","]];",$f3->current());
?>
var width = 2200, height = 600, margin ={b:0, t:40, l:-10, r:0};
var svg = d3.select("#svg")
	.append("svg")
	.attr("preserveAspectRatio", "xMinYMin meet")
	.attr("viewBox", "0 0 620 235")
	.attr("id", "ali")
	.classed("svg-content-responsive", true)
	.append("g")
	.attr("transform","translate("+ margin.l+","+margin.t+")");

var data = [ 
	{data:bP.partData(alignment_data,source,target), id:'SubWordAlignments'}
];

bP.draw(data, svg);

d3.select("#save").on("click", function(){
  saveSvgAsPng(document.getElementById("ali"), "alignments_"+Date.now()+".png", {scale: 3, backgroundColor: '#FFFFFF'});
});
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

function getJSvalue($string){
	return str_replace('@@ ', '', str_replace('["', '', str_replace('"],', '', str_replace('", "', ' ', $string))));
}