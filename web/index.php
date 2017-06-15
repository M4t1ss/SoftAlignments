<?php 
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
$f1 = gotoLine($alignments, $sentence);
$f2 = gotoLine($sources, $sentence);
$f3 = gotoLine($targets, $sentence);
$f4 = gotoLine($confidences, $sentence);

$source 	= getJSvalue($f2->current());
$target 	= getJSvalue($f3->current());
$CDP 		= getScores($f4->current(), 0);
$APout 		= getScores($f4->current(), 1);
$APin 		= getScores($f4->current(), 2);
$confidence = getScores($f4->current(), 3);

$allConfidences = getAllConfidences($f4, $count);

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta name="description" content="NMT Attention Alignments">
	<meta name="author" content="Matīss Rikters">
	<title>NMT Attention Alignments</title>
	<!--[if lt IE 9]>
		<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->
	<link rel="stylesheet" href="scripts/css/main.css">
	<link rel="stylesheet" href="scripts/css/bootstrap.min.css">
	<link rel="stylesheet" href="scripts/select/bootstrap-select.min.css">
	<link rel='stylesheet' href='scripts/css/perfect-scrollbar.min.css' />
	<script src="scripts/js/jquery-3.2.1.slim.min.js"></script>
	<script src="scripts/js/bootstrap.min.js"></script>
	<script src="scripts/js/perfect-scrollbar.jquery.min.js"></script>
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
      <a class="navbar-brand" href="#">NMT Attention Alignments</a>
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
				<button type="reset" id="save" style="display:inline;" class="btn btn-default">
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
	<p style="margin-left:15px;">
		<span class="label label-default" style="width: 100px;display: inline-block;padding: 4px;">Source</span> 
		<span class="label label-danger"><?php echo $source; ?></span>
	</p>
</div>
<div class="row">
	<div id="svg"></div>
</div>
<div class="row" style="margin-left:5px;">
	<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
		<span class="label label-default myLabel" style="cursor:default;">Translation</span> 
		<div style="width:50%; float:left;" class="pr">
			<span class="label label-danger" style="padding:4px;"><?php echo $target; ?></span>
		</div>
	</div>
	<div class="col-xs-12 col-sm-6 col-md-3 col-lg-3">
		<span data-toggle="collapse" data-target="#c1" class="label label-default myLabel">Confidence</span> 
		<div class="progress pr" style="width:50%; float:left;">
			<div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="<?php echo $confidence; ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $confidence; ?>%;">
				<?php echo $confidence; ?>%
			</div>
		</div>
	</div>
	<div class="col-xs-12 col-sm-6 col-md-3 col-lg-3">
		<span data-toggle="collapse" data-target="#c2" class="label label-default myLabel">CDP</span> 
		<div class="progress pr" style="width:50%; float:left;">
			<div class="progress-bar progress-bar-warning" role="progressbar" aria-valuenow="<?php echo $CDP; ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $CDP; ?>%;">
				<?php echo $CDP; ?>%
			</div>
		</div>
	</div>
	<div class="col-xs-12 col-sm-6 col-md-3 col-lg-3">
		<span data-toggle="collapse" data-target="#c3" class="label label-default myLabel">APout</span> 
		<div class="progress pr" style="width:50%; float:left;">
			<div class="progress-bar" role="progressbar" aria-valuenow="<?php echo $APout; ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $APout; ?>%;">
				<?php echo $APout; ?>%
			</div>
		</div>
	</div>
	<div class="col-xs-12 col-sm-6 col-md-3 col-lg-3">
		<span data-toggle="collapse" data-target="#c4" class="label label-default myLabel">APin</span> 
		<div class="progress pr" style="width:50%; float:left;">
			<div class="progress-bar progress-bar-info" role="progressbar" aria-valuenow="<?php echo $APin; ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $APin; ?>%;">
				<?php echo $APin; ?>%
			</div>
		</div>
	</div>
</div>
<div id="c1" class="row collapse">
	<div style="width:<?php echo count($allConfidences)*7;?>px;">
		<?php
			foreach($allConfidences as $key => $sentenceConfidences){
				echo '<a href="?s='.($key+1).'&directory='.$dataDir.'" title="Sentence '.($key+1).' - Confidence '.$sentenceConfidences[3].'">
						<div class="progress progress-bar-vertical">
							<div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="'.$sentenceConfidences[3].'" aria-valuemin="0" aria-valuemax="100" style="height: '.$sentenceConfidences[3].'%;">
								<span class="sr-only">'.$sentenceConfidences[3].'% Complete</span>
							</div>
						</div>
					 </a>';
			}
		?>
	</div>
</div>
<div id="c2" class="row collapse">
	<div style="width:<?php echo count($allConfidences)*7;?>px;">
		<?php
			foreach($allConfidences as $key => $sentenceConfidences){
				echo '<a href="?s='.($key+1).'&directory='.$dataDir.'" title="Sentence '.($key+1).' - CDP '.$sentenceConfidences[0].'">
						<div class="progress progress-bar-vertical">
							<div class="progress-bar progress-bar-warning" role="progressbar" aria-valuenow="'.$sentenceConfidences[0].'" aria-valuemin="0" aria-valuemax="100" style="height: '.$sentenceConfidences[0].'%;">
								<span class="sr-only">'.$sentenceConfidences[0].'% Complete</span>
							</div>
						</div>
					 </a>';
			}
		?>
	</div>
</div>
<div id="c3" class="row collapse">
	<div style="width:<?php echo count($allConfidences)*7;?>px;">
		<?php
			foreach($allConfidences as $key => $sentenceConfidences){
				echo '<a href="?s='.($key+1).'&directory='.$dataDir.'" title="Sentence '.($key+1).' - APout '.$sentenceConfidences[1].'">
						<div class="progress progress-bar-vertical">
							<div class="progress-bar" role="progressbar" aria-valuenow="'.$sentenceConfidences[1].'" aria-valuemin="0" aria-valuemax="100" style="height: '.$sentenceConfidences[1].'%;">
								<span class="sr-only">'.$sentenceConfidences[1].'% Complete</span>
							</div>
						</div>
					 </a>';
			}
		?>
	</div>
</div>
<div id="c4" class="row collapse">
	<div style="width:<?php echo count($allConfidences)*7;?>px;">
		<?php
			foreach($allConfidences as $key => $sentenceConfidences){
				echo '<a href="?s='.($key+1).'&directory='.$dataDir.'" title="Sentence '.($key+1).' - APin '.$sentenceConfidences[2].'">
						<div class="progress progress-bar-vertical">
							<div class="progress-bar progress-bar-info" role="progressbar" aria-valuenow="'.$sentenceConfidences[2].'" aria-valuemin="0" aria-valuemax="100" style="height: '.$sentenceConfidences[2].'%;">
								<span class="sr-only">'.$sentenceConfidences[2].'% Complete</span>
							</div>
						</div>
					 </a>';
			}
		?>
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

$('#c1').perfectScrollbar({
  suppressScrollY: true,
  useBothWheelAxes: true
});
$('#c2').perfectScrollbar({
  suppressScrollY: true,
  useBothWheelAxes: true
});
$('#c3').perfectScrollbar({
  suppressScrollY: true,
  useBothWheelAxes: true
});
$('#c4').perfectScrollbar({
  suppressScrollY: true,
  useBothWheelAxes: true
});
</script>
</body>
</html>