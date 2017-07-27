// javascript util to visualize attention weights between source and target

!function(){
	var bP={};	
	var b=30, bb=150, height=600, buffMargin=1, minHeight=14;
	var c1=[-130, 40], c2=[-50, 100], c3=[-10, 140]; //Column positions of labels.
	var colors =["#3366CC", "#DC3912",  "#FF9900","#109618", "#990099", "#0099C6"];
	
	bP.partData = function(data,source,target){
		var sData={};
		sData.keys=[source[0],target[0]];
		sData.data = [	
			sData.keys[0].map( function(d){ return sData.keys[1].map( function(v){ return 10; }); }),//source[p],
			sData.keys[1].map( function(d){ return sData.keys[1].map( function(v){ return 10; }); }),
			sData.keys[0].map( function(d){ return sData.keys[1].map( function(v){ return 0; });}) 
		];
		data.forEach(function(d){ 
			sData.data[2][d[0]][d[2]] = d[1];
		});
		return sData;
	}
	
	function visualize(data){
		var vis ={};
		function calculatePosition(a, s, e, b, m){
			var total=d3.sum(a);//a :data
			var sum=0, neededHeight=0, leftoverHeight= e-s-2*b*a.length;
			var ret =[];
			a.forEach(
				function(d){ 
					var v={};
					v.percent = (total == 0 ? 0 : d/total); 
					v.value=d;
					v.height=Math.max(v.percent*(e-s-2*b*a.length), m);
					(v.height==m ? leftoverHeight-=m : neededHeight+=v.height );
					ret.push(v);
				}
			);
			
			var scaleFact=leftoverHeight/Math.max(neededHeight,1), sum=0;
			
			ret.forEach(
				function(d){ 
					d.percent = scaleFact*d.percent; 
					d.height=(d.height==m? m : d.height*scaleFact);
					d.middle=sum+b+d.height/2;
					d.y=s + d.middle - d.percent*(e-s-2*b*a.length)/2;
					d.h= d.percent*(e-s-2*b*a.length);
					d.percent = (total == 0 ? 0 : d.value/total);
					sum+=2*b+d.height;
				}
			);
			return ret;
		}
		function calculatePosition1(a, s, e, b, m,d1){
			var total=d3.sum(a);
			var sum=0, neededHeight=0, leftoverHeight= e-s-2*b*a.length;
			var ret =[];
			a.forEach(
				function(d){
					var v={};
					v.percent = (total == 0 ? 0 : d/total);
					v.value=d;
					v.height=Math.max(v.percent*(e-s-2*b*a.length), m);
					(v.height==m ? leftoverHeight-=m : neededHeight+=v.height );
					ret.push(v);
				}
			);

			var scaleFact=leftoverHeight/Math.max(neededHeight,1), sum=0;

			ret.forEach(
				function(d){
					d.percent = scaleFact*d.percent;
					d.height=(d.height==m? m : d.height*scaleFact);
					d.middle=sum+b+d.height/2;
					d.y=s + d.middle - d.percent*(e-s-2*b*a.length)/2;
					d.h= d.percent*(e-s-2*b*a.length);
					d.percent = (total == 0 ? 0 : d.value/total);
					sum+=2*b+d.height;
					d.p = d1;
				}
			);
			return ret;
		}
		vis.mainBars = [ 
			calculatePosition1( data.data[0].map(function(d){ return d3.sum(d);}), 0, height, buffMargin, minHeight,0),
			calculatePosition1( data.data[1].map(function(d){ return d3.sum(d);}), 0, height, buffMargin, minHeight,1)
		];
		vis.edges = [];
		vis.mainBars[0].forEach(function(pos,i){
			 vis.mainBars[1].forEach(function(bar, j){
				sBar = [];
				sBar.key1 = data.keys[0][j];//[i];
				sBar.key2 = data.keys[1][i];//[j];
				sBar.y1 = pos.middle;
				sBar.y2 = bar.middle;
				sBar.h1 =1;//bar.y;
				sBar.h2 = 160;
				sBar.va = data.data[2][i][j];
				vis.edges.push(sBar);	
				
			});
		});
		vis.keys=data.keys;
		return vis;
	}
	
	//draws top or bottom rows of text
	function drawPart(data, id, p){
		
		size_coef=1;
		if (Math.max(data.keys[0].length, data.keys[1].length) > 33){
			size_coef = 0.03 * Math.max(data.keys[0].length, data.keys[1].length)
		}
		
		d3.select("#"+id).append("g").attr("class","part"+p)
			.attr("transform","translate( 0," +( p*(bb+b))+")");
		d3.select("#"+id).select(".part"+p).append("g").attr("class","mainbars");
		
	    var me = "0em";
		var bar = d3.select("#"+id).select(".part"+p).select(".mainbars")
                        .selectAll(".mainbar").data(data.mainBars[p])
                        .enter();
			
		bar.append("text")
			.text(function(d,i) { return data.keys[p][i];})
			.attr("text-anchor", function(d){ 
				if (d.p == 1) {
					return "start";
				} else {
					return "end";
				}
			})
			.attr("transform", function(d) {
				var y = (d.p == 1 ? "-15" : "-2");
				return "translate("+(d.middle/size_coef+30)+","+y+")rotate(45)";
				
			})
			.attr("fill","black");
		}
	
	function drawEdges(data, id){
		
		size_coef=1;
		if (Math.max(data.keys[0].length, data.keys[1].length) > 33){
			size_coef = 0.03 * Math.max(data.keys[0].length, data.keys[1].length)
		}
		
		var color = d3.interpolateLab("#FBCEB1","#E34234");
		d3.select("#"+id).append("g").attr("class","edges").attr("transform","translate("+ b+",0)");

		d3.select("#"+id).select(".edges").selectAll(".edge")
			.data(data.edges).enter().append("line").attr("class","edge")
			.attr("x1", function(d) {return d.y1/size_coef;})     // x position of the first end of the line
			.attr("y1", function(d) {return d.h1;})      // y position of the first end of the line
			.attr("x2", function(d) {return d.y2/size_coef;})     // x position of the second end of the line
			.attr("y2", function(d) {return d.h2;})
			.style("stroke-width", function(d) {
				return d.va * 2; 
			})
			.style("stroke", function(d) {return color(d.va);});
	}	
	
	bP.draw = function(data, svg){
		data.forEach(function(biP,s){
			svg.append("g")
				.attr("id", biP.id)
				.attr("transform","translate( 0, " + (0)+")");
				
			var visData = visualize(biP.data);
			drawPart(visData, biP.id, 0);
			drawPart(visData, biP.id, 1); 
			drawEdges(visData, biP.id);
		});	
	}
		
	this.bP = bP;
}();


function toggleChart(chart){
	setCookie(chart, getCookie(chart)*-1, 1);
}

var sortBy = getCookie('sortBy');
var sortOrder = getCookie('sortOrder');
var hide = getCookie('hide');
var show = getCookie('show');
if (hide == '' || show == ''){
    hide = 'matrix';
    show = 'svg';
}

if(sortBy == "") sortBy = 1;
if(sortOrder == "") sortOrder = 'ASC';

$(document).ready(function(){
	sortAll(sortBy, sortOrder, false);
	
	var c1 = getCookie('c1');
	var c2 = getCookie('c2');
	var c3 = getCookie('c3');
	var c4 = getCookie('c4');
	var c5 = getCookie('c5');
	
	if(c1 == 1){
		$('#c1').collapse("show");
	}else{
		setCookie('c1', -1, 1);
	}
	if(c2 == 1){
		$('#c2').collapse("show");
	}else{
		setCookie('c2', -1, 1);
	}
	if(c3 == 1){
		$('#c3').collapse("show");
	}else{
		setCookie('c3', -1, 1);
	}
	if(c4 == 1){
		$('#c4').collapse("show");
	}else{
		setCookie('c4', -1, 1);
	}
	if(c5 == 1){
		$('#c5').collapse("show");
	}else{
		setCookie('c5', -1, 1);
	}

}) 

function sortAll(SortBy, order = "", reorder = true){
	if(order == ""){
		order = getCookie('sortOrder');
		if(order == "") 
			order = "ASC";
	}
	if(reorder && SortBy == getCookie('sortBy'))
		if(order == "ASC") 
			order = "DESC";
		else
			order = "ASC";
	
	mySort('length', SortBy, order);
	mySort('confidence', SortBy, order);
	mySort('apin', SortBy, order);
	mySort('apout', SortBy, order);
	mySort('cdp', SortBy, order);
	
	setCookie('sortBy', SortBy, 1);
	setCookie('sortOrder', order, 1);
}

function mySort(ParentID, SortBy, order = ""){
	var toSort = document.getElementById(ParentID).children;
	toSort = Array.prototype.slice.call(toSort, 0);

	toSort.sort(function(a, b) {
		var aord = +a.id.split('-')[SortBy];
		var bord = +b.id.split('-')[SortBy];
		if(order == "ASC"){
			return aord - bord;
		}else{
			return bord - aord;
		}
	});

	var parent = document.getElementById(ParentID);
	parent.innerHTML = "";

	for(var i = 0, l = toSort.length; i < l; i++) {
		parent.appendChild(toSort[i]);
	}
}

function setCookie(cname, cvalue, exdays) {
    var d = new Date();
    d.setTime(d.getTime() + (exdays*24*60*60*1000));
    var expires = "expires="+ d.toUTCString();
    document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
}

function getCookie(cname) {
    var name = cname + "=";
    var decodedCookie = decodeURIComponent(document.cookie);
    var ca = decodedCookie.split(';');
    for(var i = 0; i <ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
        }
    }
    return "";
}

var sentence = [];

function loadAlignment(name, line) {
    if (!sentence) sentence = {};
    var maxRow = null, maxCol = null;
    
    line.forEach(function(alignment) {
        if (!alignment) return;
        var x = alignment[0];
        var y = alignment[2];
        if (!sentence[x]) sentence[x] = {};
        if (!sentence[x][y]) sentence[x][y] = {};
        if (!sentence[x][y][name]) sentence[x][y][name] = {};
        sentence[x][y][name] = true;
        sentence[x][y]['weight'] = alignment[1];
        if (!sentence.maxRow || x > sentence.maxRow) sentence.maxRow = x;
        if (!sentence.maxCol || y > sentence.maxCol) sentence.maxCol = y;
    });
}

function loadText(name, line) {
    if (!sentence) sentence = {};
    if (!sentence.tokens) sentence.tokens = {};
    sentence.tokens[name] = line;
}

function render(M_sources, M_targets, M_alignments) {
    loadText('src', M_sources[0]);
    loadText('trg', M_targets[0]);
    loadAlignment('sym', M_alignments);
    
	$('#matrix table').remove();
	$table = $('<table/>');
	$tr = $('<tr class="top"><th>&nbsp;</th></tr>');
	for (var c = 0; c <= sentence.maxCol; c++) {
		$tr.append($('<th/>').attr('data-col', c).text(sentence.tokens.trg[c]));
	}
	$table.append($tr);
	for (var r = 0; r <= sentence.maxRow; r++) {
		$tr = $('<tr/>');
		$tr.append($('<th class="left"/>').text(sentence.tokens.src[r]));
		for (var c = 0; c <= sentence.maxCol; c++) {
			$td = $('<td/>');
            $td.attr('data-col', c);
            $td.attr('data-row', r);
			if (sentence[r] && sentence[r][c] && sentence[r][c]) {
				for (var key in sentence[r][c]) {
					if (sentence[r][c][key]) {
						$td.css("background-color", lighten("FFFFFF", sentence[r][c]['weight'] * -1));
                        
					}
				}
			}
            $td.hover(function() { $('table [data-col=' + $(this).attr('data-col') + ']').addClass('hover'); }, 
                      function() { $('table [data-col=' + $(this).attr('data-col') + ']').removeClass('hover'); });
			$tr.append($td);
		}
		$table.append($tr);
	}
	$('#matrix').append($table);
}

function hideShow(m_hide,m_show){
    $("#"+m_hide).hide();
    $("#"+m_show).show();
	setCookie('hide', m_hide, 1);
	setCookie('show', m_show, 1);
    hide = m_hide;
    show = m_show;
}

function lighten(color, luminosity) {

	color = new String(color).replace(/[^0-9a-f]/gi, '');
	if (color.length < 6) {
		color = color[0]+ color[0]+ color[1]+ color[1]+ color[2]+ color[2];
	}
	luminosity = luminosity || 0;

	var newColor = "#", c, i, black = 0, white = 255;
	for (i = 0; i < 3; i++) {
		c = parseInt(color.substr(i*2,2), 16);
		c = Math.round(Math.min(Math.max(black, c + (luminosity * white)), white)).toString(16);
		newColor += ("00"+c).substr(c.length);
	}
	return newColor; 
}