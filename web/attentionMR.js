// javascript util to visualize attention weights between source and target

!function(){
	var bP={};	
	var b=30, bb=150, height=600, buffMargin=1, minHeight=14;
	var c1=[-130, 40], c2=[-50, 100], c3=[-10, 140]; //Column positions of labels.
	var colors =["#3366CC", "#DC3912",  "#FF9900","#109618", "#990099", "#0099C6"];
	
	bP.partData = function(data,target,source){
		var sData={};
		sData.keys=[source[0],target[0]];
		sData.data = [	
			sData.keys[0].map( function(d){ return sData.keys[1].map( function(v){ return 10; }); }),//source[p],
			sData.keys[1].map( function(d){ return sData.keys[1].map( function(v){ return 10; }); }),
			sData.keys[0].map( function(d){ return sData.keys[1].map( function(v){ return 0; });}) 
		];
		data.forEach(function(d){ 
			if(sData.data[2][d[2]] != undefined){
				sData.data[2][d[2]][d[0]] = d[1];
			}
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
		vis.subBars = [[],[]];
		vis.edges = [];
		vis.mainBars[0].forEach(function(pos,i){
			 vis.mainBars[1].forEach(function(bar, j){
				sBar = [];
				sBar.key1 = data.keys[0][j];//[i];
				sBar.key2 = data.keys[1][i];//[j];
				sBar.y1 = pos.middle;
				sBar.y2 = bar.middle;
				sBar.h1 =1;//bar.y;
				sBar.h2 = 180;
				sBar.va = data.data[2][i][j];
				vis.edges.push(sBar);	
				
			});
		});
		vis.keys=data.keys;
		return vis;
	}
	
	function arcTween(a) {
		var i = d3.interpolate(this._current, a);
		this._current = i(0);
		return function(t) {
			return edgePolygon(i(t));
		};
	}
	
	function drawPart(data, id, p){
		d3.select("#"+id).append("g").attr("class","part"+p)
			.attr("transform","translate( 0," +( p*(bb+b))+")"); //+( p*(bb+b))+",0)");
		d3.select("#"+id).select(".part"+p).append("g").attr("class","subbars");
		d3.select("#"+id).select(".part"+p).append("g").attr("class","mainbars");
		
	    var me = "0em";
		var bar = d3.select("#"+id).select(".part"+p).select(".mainbars")
                        .selectAll(".mainbar").data(data.mainBars[p])
                        .enter();
			
		bar.append("rect").attr("class","mainbar")//"subbar")
			.attr("x", function(d){ return d.middle})
                        .attr("y",p)
			.attr("width",function(d){ return d.h})
			.attr("height",b)
			.style("fill","None");
		bar.append("text")
        	.attr("x", function(d) {return d.middle;})
			.attr("dy", function(d){ 
				if (d.p == 1) {
					return "0.5em";
				} else {
					return "-0.5em";
				}
			})//"1.35em")
			.attr("dx", "3em")
			.text(function(d,i) { return data.keys[p][i];})
			.attr("text-anchor", function(d){ 
				if (d.p == 1) {
					return "start";
				} else {
					return "end";
				}
			})
			.attr("style", "writing-mode: vertical-lr;")
			.attr("fill","black");
		}
	
	function drawEdges(data, id){
		var color = d3.interpolateLab("#FBCEB1","#E34234");
		d3.select("#"+id).append("g").attr("class","edges").attr("transform","translate("+ b+",0)");

		d3.select("#"+id).select(".edges").selectAll(".edge")
			.data(data.edges).enter().append("line").attr("class","edge")
			.attr("x1", function(d) {return d.y1;})     // x position of the first end of the line
			.attr("y1", function(d) {return d.h1;})      // y position of the first end of the line
			.attr("x2", function(d) {return d.y2;})     // x position of the second end of the line
			.attr("y2", function(d) {return d.h2;})
			.style("stroke-width", function(d) { //console.log(d); 
				return d.va * 2; 
			})//function(d) { //console.log(d); return d.value * 2; })
			.style("stroke", function(d) {return color(d.va);});
	}	
	
	function drawHeader(header, id){
		d3.select("#"+id).append("g").attr("class","header").append("text").text(header[2])
			.style("font-size","20").attr("x",108).attr("y",-20).style("text-anchor","middle")
			.style("font-weight","bold");
		
		[0,1].forEach(function(d){
			var h = d3.select("#"+id).select(".part"+d).append("g").attr("class","header");
			
			h.append("text").text(header[d]).attr("x", (c1[d]-5))
				.attr("y", -5).style("fill","grey");
			
			h.append("text").text("Count").attr("x", (c2[d]-10))
				.attr("y", -5).style("fill","grey");
			
			h.append("line").attr("x1",c1[d]-10).attr("y1", -2)
				.attr("x2",c3[d]+10).attr("y2", -2).style("stroke","black")
				.style("stroke-width","1").style("shape-rendering","crispEdges");
		});
	}
	
	function edgePolygon(d){
		return [0, d.y1, bb, d.y2, bb, d.y2+d.h2, 0, d.y1+d.h1].join(" ");
	}	
	
	function transitionPart(data, id, p){
		var mainbar = d3.select("#"+id).select(".part"+p).select(".mainbars")
			.selectAll(".mainbar").data(data.mainBars[p]);
		
		mainbar.select(".mainrect").transition().duration(500)
			.attr("y",function(d){ return d.middle-d.height/2;})
			.attr("height",function(d){ return d.height;});
			
		mainbar.select(".barlabel").transition().duration(500)
			.attr("y",function(d){ return d.middle+5;});
			
		mainbar.select(".barvalue").transition().duration(500)
			.attr("y",function(d){ return d.middle+5;}).text(function(d,i){ return d.value ;});
			
		mainbar.select(".barpercent").transition().duration(500)
			.attr("y",function(d){ return d.middle+5;})
			.text(function(d,i){ return "( "+Math.round(100*d.percent)+"%)" ;});
			
		d3.select("#"+id).select(".part"+p).select(".subbars")
			.selectAll(".subbar").data(data.subBars[p])
			.transition().duration(500)
			.attr("y",function(d){ return d.y}).attr("height",function(d){ return d.h});
	}
	
	function transitionEdges(data, id){
		d3.select("#"+id).append("g").attr("class","edges")
			.attr("transform","translate("+ b+",0)");

		d3.select("#"+id).select(".edges").selectAll(".edge").data(data.edges)
			.transition().duration(500)
			.attrTween("points", arcTween)
			.style("opacity",function(d){ return (d.h1 ==0 || d.h2 == 0 ? 0 : 0.5);});	
	}
	
	function transition(data, id){
		transitionPart(data, id, 0);
		transitionPart(data, id, 1);
		transitionEdges(data, id);
	}
	
	bP.draw = function(data, svg){
		data.forEach(function(biP,s){
			svg.append("g")
				.attr("id", biP.id)
				.attr("transform","translate( 0, " + (0)+")");//+ (550*s)+",0)");
				
			var visData = visualize(biP.data);
			drawPart(visData, biP.id, 0);
			drawPart(visData, biP.id, 1); 
			drawEdges(visData, biP.id);
		});	
	}
	
	bP.selectSegment = function(data, m, s){
		data.forEach(function(k){
			var newdata =  {keys:[], data:[]};	
				
			newdata.keys = k.data.keys.map( function(d){ return d;});
			
			newdata.data[m] = k.data.data[m].map( function(d){ return d;});
			
			newdata.data[1-m] = k.data.data[1-m]
				.map( function(v){ return v.map(function(d, i){ return (s==i ? d : 0);}); });
			
			transition(visualize(newdata), k.id);
				
			var selectedBar = d3.select("#"+k.id).select(".part"+m).select(".mainbars")
				.selectAll(".mainbar").filter(function(d,i){ return (i==s);});
			
			selectedBar.select(".mainrect").style("stroke-opacity",1);			
			selectedBar.select(".barlabel").style('font-weight','bold');
			selectedBar.select(".barvalue").style('font-weight','bold');
			selectedBar.select(".barpercent").style('font-weight','bold');
		});
	}	
	
	bP.deSelectSegment = function(data, m, s){
		data.forEach(function(k){
			transition(visualize(k.data), k.id);
			
			var selectedBar = d3.select("#"+k.id).select(".part"+m).select(".mainbars")
				.selectAll(".mainbar").filter(function(d,i){ return (i==s);});
			
			selectedBar.select(".mainrect").style("stroke-opacity",0);			
			selectedBar.select(".barlabel").style('font-weight','normal');
			selectedBar.select(".barvalue").style('font-weight','normal');
			selectedBar.select(".barpercent").style('font-weight','normal');
		});		
	}
	
	this.bP = bP;
}();
