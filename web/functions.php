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

function getSWvalue($string){
	return explode('", "', str_replace('asc@@', '', str_replace('["', '', str_replace('"],', '', trim($string)))));
}

function gotoLine($fileName, $sentence){
	$file = new SplFileObject($fileName);
	$file->seek($sentence);
	return $file;
}

function getScores($string, $score_num = NULL){
	$scores 	= explode(", ", str_replace("]", "", str_replace("[", "", $string)));
	$CDP 		= $scores[0];
	$APout 		= $scores[1];
	$APin 		= $scores[2];
	$confidence = $scores[3];
	$length_pr 	= $scores[4];
	$length 	= $scores[5];
	$similarity	= $scores[6];
	$BLEU 		= (count($scores) > 7 && trim($scores[7])!="") ? $scores[7] : null;
	if($score_num!==null && $score_num < count($scores)){
		return $scores[$score_num];
	}else{
		return array($CDP, $APout, $APin, $confidence, $length_pr, $length, $similarity, $BLEU);
	}
}

function getAllConfidences($file, $count){
	$confidences = array();
	for ($i = 1; $i <= $count; $i++){
		$file->seek($i);
		$confidences[] = getScores($file->current());
	}
	return $confidences;
}

function printRow($name, $rowId, $sortId, $dataId, $dataDir, $allConfidences, $color, $idOne, $idTwo, $textOne, $textTwo){
    echo '<div id="'.$rowId.'" class="row collapse">
        <span class="glyphicon glyphicon-sort sort" style="margin-top:10px;" onclick="sortAll('.$sortId.')"></span>
        <span class="glyphicon glyphicon-repeat sort" style="margin-top:40px;" onclick="sortAll(1)"></span>
        <div id="'.$name.'" style="margin-left:20px;width:'.(count($allConfidences)*7).'px;">';
                foreach($allConfidences as $key => $scfd){
                    $textNum = $name == "length" ? $scfd[5] : $scfd[$dataId];
                    echo '<a id="'.$idOne.'-'.($key+1).'-'.implode("-",$scfd).'" ';
                    echo 'href="?directory='.$dataDir.'&s='.($key+1).'" title="Sentence '.($key+1).' - '.$textOne.' '.$textNum.$textTwo.'">';
                    echo '<div class="progress progress-bar-vertical">';
                    echo '<div id="'.$idTwo.'-'.($key+1).'" class="progress-bar progress-bar-'.$color.'" role="progressbar" aria-valuenow="'.$scfd[$dataId].'" ';
                    echo 'aria-valuemin="0" aria-valuemax="100" style="height: '.$scfd[$dataId].'%;">';
                    echo '<span class="sr-only">'.$scfd[$dataId].'% Complete</span>';
                    echo '</div></div></a>';
                }
        echo'</div></div>';
}