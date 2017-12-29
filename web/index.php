<?php 
// Report simple running errors
error_reporting(E_ERROR | E_WARNING | E_PARSE);
header('Content-Type: text/html; charset=utf-8');
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
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
$compare = false;
if(file_exists("./data/".$dataDir."/NMT1") && file_exists("./data/".$dataDir."/NMT2")) $compare = true;

//Get a list of all confidences for browsing
$dataFiles = cleanDirArray(scandir("./data/".$dataDir.($compare?"/NMT1":"")));
$confidences = "./data/".$dataDir.($compare?"/NMT1/":"/").array_pop(preg_grep("/\.con\.js/", $dataFiles));
$f4 = gotoLine($confidences, $sentence);
$count = getLineCount($confidences)-2;
$allConfidences = getAllConfidences($f4, $count);
if($sentence > $count) $sentence = $count;

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta name="description" content="NMT Attention Alignments">
	<meta name="author" content="Matīss Rikters">
	<title><?php echo ($compare?"Compare ":""); ?>NMT Attention Alignments</title>
	<!--[if lt IE 9]>
		<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->
	<link rel="stylesheet" href="scripts/css/main.css">
	<link rel="stylesheet" href="scripts/css/bootstrap.min.css">
	<link rel="stylesheet" href="scripts/select/bootstrap-select.min.css">
	<link rel='stylesheet' href='scripts/css/perfect-scrollbar.min.css' />
	<script src="scripts/js/jquery-3.2.1.min.js"></script>
	<script src="scripts/js/bootstrap.min.js"></script>
	<script src="scripts/js/perfect-scrollbar.jquery.min.js"></script>
	<script src="scripts/select/bootstrap-select.min.js"></script>
    <script src="scripts/attentionMR.js"></script>
    <script src="scripts/d3.v3.min.js"></script>
    <script src="scripts/saveSvgAsPng.js"></script>
    <script src="scripts/js/html2canvas.js"></script>
    <script type="text/javascript">
    var sentenceNum = <?php echo $sentence; ?>;
    var dataDir = "<?php echo $dataDir; ?>";
	var compare = <?php echo ($compare?"true":"false"); ?>;
    </script>
    <script src="scripts/index.js"></script>
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
      <a class="navbar-brand" href="#"><?php echo ($compare?"Compare ":""); ?>NMT Attention Alignments </a>
    </div>

    <!-- Collect the nav links, forms, and other content for toggling -->
    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
      <ul class="nav navbar-nav navbar-center">
		<li>
			<a href="<?php echo ($compare?"?directory=".$dataDir."&s=".($sentence-1):"#"); ?>" <?php echo (!$compare?'onclick="getPrev(dataDir, sentenceNum);"':''); ?>>
				<span class="glyphicon glyphicon-arrow-left" aria-hidden="true"></span>
			</a>
		</li>
        <li>
			<form class="navbar-form" action="?" method="GET" <?php echo (!$compare?'onsubmit="return jumpForm()"':''); ?>>
                <div class="btn-group" data-toggle="buttons">
					<?php echo (!$compare?'
					  <label class="btn btn-default active" id="svgBut">
						<input type="radio" name="type" value="svg" autocomplete="off" checked><span class="glyphicon glyphicon-random" aria-hidden="true"></span>
					  </label>
					  <label class="btn btn-default" id="matBut">
						<input type="radio" name="type" value="matrix" autocomplete="off"><span class="glyphicon glyphicon-th" aria-hidden="true"></span>
					  </label>
				  ':'
                    <button type="reset" style="display:inline;" class="btn btn-default" onclick="toggle(\'#svg\')">
                        <span id="togglesvg" class="glyphicon glyphicon-eye-open" aria-hidden="true"></span>
                    </button>
                    <a type="reset" id="save" style="display:inline;" class="btn btn-default">
                        <span class="glyphicon glyphicon-save" aria-hidden="true"></span>
                    </a>
					'); ?>
                </div>
				<input class="form-control" style="width:75px; display:inline;" name="s" id="sentenceNum" value="<?php echo $sentence; ?>" type="text" /> 
                <div class="btn-group" role="group">
                    <button style="display:inline;" class="btn btn-default" type="submit">
                        <span class="glyphicon glyphicon-refresh" aria-hidden="true"></span>
                    </button>
					<?php echo (!$compare?'
                    <a type="reset" id="save" style="display:inline;" class="btn btn-default">
                        <span class="glyphicon glyphicon-save" aria-hidden="true"></span>
                    </a>
				  ':'
                    <button type="reset" style="display:inline;" class="btn btn-default" onclick="toggle(\'#other\')">
                        <span id="toggleother" class="glyphicon glyphicon-eye-open" aria-hidden="true"></span>
                    </button>'); ?>
                </div>
				<input type="hidden" name="directory" value="<?php echo $dataDir; ?>" />
				<input type="hidden" name="changeNum" value="True" />
			</form>
		</li>
		<li>
			<a href="<?php echo ($compare?"?directory=".$dataDir."&s=".($sentence+1):"#"); ?>" <?php echo (!$compare?'onclick="getNext(dataDir, sentenceNum);"':''); ?>>
				<span class="glyphicon glyphicon-arrow-right" aria-hidden="true"></span>
			</a>
		</li>
      </ul>
      <ul class="nav navbar-nav navbar-right">
        <li style="padding-top:8px; padding-right:5px;">
			<form action="?">
				<select class="selectpicker" data-live-search="true" name="directory" onchange="setCookie('sortBy', '', 1);setCookie('sortOrder', '', 1);this.form.submit()">
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
    <div id="loadCont">
		<img id="load" src="scripts/load.svg">
	</div>
<div class="row" style="margin-left:5px;" id="topRow">
</div>
<div class="row">
	<div id="svg"></div>
	<div id="other"></div>
    <div id="matrix"></div>
</div>
<div class="row<?php echo ($compare?" bottomRow":""); ?>" style="margin-left:5px;" id="bottomRow">
</div>
<div class="row<?php echo ($compare?" bottomRow2":""); ?>" style="margin-left:5px;" id="bottomRow2">
</div>
<?php
printRow("length", "sortable-5", "6", 4, $dataDir, $allConfidences, "danger", "le", "translation", "Length", " symbols");
printRow("confidence", "sortable-1", "5", 3, $dataDir, $allConfidences, "success", "co", "confidence", "Confidence", "%");
printRow("cdp", "sortable-2", "2", 0, $dataDir, $allConfidences, "warning", "cd", "deviation", "CDP", "%");
printRow("apout", "sortable-3", "3", 1, $dataDir, $allConfidences, "default", "ao", "apout", "APout", "%");
printRow("apin", "sortable-4", "4", 2, $dataDir, $allConfidences, "info", "ai", "apin", "APin", "%");
printRow("similarity", "sortable-7", "8", 6, $dataDir, $allConfidences, "pink", "si", "similarity", "Overlap", "%");
if($allConfidences[0][7] != null){
    printRow("sent-bleu", "sortable-6", "9", 7, $dataDir, $allConfidences, "purple", "bl", "bleu", "BLEU", "");
}
?>

</body>
</html>