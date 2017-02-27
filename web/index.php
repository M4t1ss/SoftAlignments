<?php 
header('Content-Type: text/html; charset=utf-8');
if (!isset($_GET['s'])){
	$_GET['s'] = 0;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta name="description" content="The HTML5 Herald">
  <meta name="author" content="SitePoint">
<title>Visualization Demo</title>
    <link href="_assets/css/global.css" type="text/css" rel="stylesheet">
    <link href="_assets/css/fonts.css" rel='stylesheet' type='text/css'>

    <link href="_assets/css/basic.css" type="text/css" rel="stylesheet" />
    <link href="_assets/css/visualize.css" type="text/css" rel="stylesheet" />
    <link href="_assets/css/visualize-light.css" type="text/css" rel="stylesheet" />
    <script type="text/javascript" src="_assets/js/d3.min.js"  charset="utf-8"></script>
    <script src="_assets/js/jquery-1.9.1.js"></script>
    <script src="_assets/js/jquery.min.js"></script>
    <script type="text/javascript" src="_assets/js/jquery.validate.min.js"></script>
    <script type="text/javascript" src="_assets/js/visualize.jQuery.js"></script>
    <script type="text/javascript" src="_assets/js/plugins.js" ></script>
    <script type="text/javascript" src="_assets/js/global.js" ></script>
    <script language="javascript" src="_assets/calendar/calendar/calendar.js"></script>

  <!--[if lt IE 9]>
    <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
  <![endif]-->
</head>
<style>
body{
	width:98%;
}
svg text{
	font-size:10px;
}
rect{
	shape-rendering:crispEdges;
}
</style>
<body>
Neural MT alignment visualization. <br/>
Forked from <a href="https://github.com/rsennrich/nematus/tree/master/utils">Nematus utils</a><br/>
<a href="?s=<?php echo $_GET['s']>0?$_GET['s']-1:0;?>">< previous</a>
<a href="?s=<?php echo $_GET['s']<50?$_GET['s']+1:50;?>" style="float:right;"> next ></a>
<div id="area1"></div>
<script src="http://d3js.org/d3.v3.min.js"></script>
<script src="attentionMR.js"></script>
<script>
<?php include('alignments.npy.ali.js'); ?>
<?php include('alignments.npy.src.js'); ?>
<?php include('alignments.npy.trg.js'); ?>
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
