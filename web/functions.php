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

function printChart($name, $value, $color, $sortableId, $size = "col-xs-12 col-sm-6 col-md-3 col-lg-3"){
	echo '<div class="'.$size.'">';
    echo '<span data-toggle="collapse" data-target="#'.$sortableId.'" class="label label-default myLabel" onclick="toggleChart(\''.$sortableId.'\')">'.$name.'</span> ';
    echo '<div class="progress pr" >';
    echo '<div class="progress-bar progress-bar-'.$color.'" role="progressbar" aria-valuenow="'.$value.'" aria-valuemin="0" aria-valuemax="100" style="width: '.$value.'%;">';
    echo $value.'%</div></div></div>';
}

function replaceStuff($string){
	$string = str_replace('&quot;','"', $string);
	$string = str_replace("&apos;","'", $string);
	$string = str_replace("&amp;","&", $string);
	$string = str_replace("@-@","-", $string);
	
	return $string;
}

function getLongestCommonSubsequence($string_1, $string_2)
{
	$string_1_length = strlen($string_1);
	$string_2_length = strlen($string_2);
	$return          = '';
	
	if ($string_1_length === 0 || $string_2_length === 0)
	{
		// No similarities
		return $return;
	}
	
	$longest_common_subsequence = array();
	
	// Initialize the CSL array to assume there are no similarities
	$longest_common_subsequence = array_fill(0, $string_1_length, array_fill(0, $string_2_length, 0));
	
	$largest_size = 0;
	
	for ($i = 0; $i < $string_1_length; $i++)
	{
		for ($j = 0; $j < $string_2_length; $j++)
		{
			// Check every combination of characters
			if ($string_1[$i] === $string_2[$j])
			{
				// These are the same in both strings
				if ($i === 0 || $j === 0)
				{
					// It's the first character, so it's clearly only 1 character long
					$longest_common_subsequence[$i][$j] = 1;
				}
				else
				{
					// It's one character longer than the string from the previous character
					$longest_common_subsequence[$i][$j] = $longest_common_subsequence[$i - 1][$j - 1] + 1;
				}
				
				if ($longest_common_subsequence[$i][$j] > $largest_size)
				{
					// Remember this as the largest
					$largest_size = $longest_common_subsequence[$i][$j];
					// Wipe any previous results
					$return       = '';
					// And then fall through to remember this new value
				}
				
				if ($longest_common_subsequence[$i][$j] === $largest_size)
				{
					// Remember the largest string(s)
					$return = substr($string_1, $i - $largest_size + 1, $largest_size);
				}
			}
			// Else, $CSL should be set to 0, which it was already initialized to
		}
	}
	
	// Return the list of matches
	return $return;
}