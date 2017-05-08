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
