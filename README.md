# NMT Attention Alignment Visualizations
An attention alignment visualization tool for command line and web. 

A part of the web version was borrowed from [Nematus utils](https://github.com/rsennrich/nematus/tree/master/utils)

[![Build Status](https://travis-ci.org/M4t1ss/SoftAlignments.svg?branch=master)](https://travis-ci.org/M4t1ss/SoftAlignments)

Usage
---------

  - Train a neural MT system (like [Neural Monkey](https://github.com/ufal/neuralmonkey/) or [Nematus](https://github.com/rsennrich/nematus/))
  - Translate text and get word or subword level alignments
  - Visualize the alignments
    - in the command line standard output
    - in a web browser (PHP required)

Requirements
---------

* Python 2 or 3

* PHP 5.4 or newer (for web visualization)

How to get alignment files from NMT systems
---------

* [Nematus](https://github.com/EdinburghNLP/nematus)
	* Run [**nematus/translate.py**](https://github.com/EdinburghNLP/nematus#using-a-trained-model) with the `--output_alignment` or `-a` parameter

* [Neural Monkey](https://github.com/ufal/neuralmonkey)
	* In the training.ini file add

	```sh
	[alignment_saver]
	class=runners.word_alignment_runner.WordAlignmentRunner
	output_series="ali"
	encoder=<encoder>
	decoder=<decoder>
	```

	and add __alignment_saver__ to the __runners__ in **main**

	```sh
	runners=[<runner_greedy>, <alignment_saver>]
	```

	* In the translation.ini file in **eval_data** add
	```sh
	s_ali_out="out.alignment"
	```

* [Marian](https://github.com/marian-nmt/marian)
	* In the config.yml file add
	```sh
	no-debpe: true
	return-alignment: false
	return-soft-alignment: true
	```
	* Or run **amun** with the parameters `--no-debpe --return-soft-alignment`
	
* [OpenNMT](https://github.com/OpenNMT/OpenNMT)
	Run translate.lua to translate with the `-save_attention` parameter to save attentions to a file

* [Sockeye](https://github.com/awslabs/sockeye)
	Run sockeye.translate to translate with the `--output-type` parameter set to `translation_with_alignment_matrix` to save attentions to a file

* **Other**
	The esiest format to use (and convert to) is the one used by Nematus.
	
	For each sentence the first row should be `<sentence id> ||| <target words> ||| <score> ||| <source words> ||| <number of source words> ||| <number of target words>`
	
	After that follow `<number of target words> + 1 (for <EOS> token)` rows with `<number of source words> + 1 (for <EOS> token)` columns with attention weights separated by spaces. 
	
	After each sentence there should be an empty line.
	
	Note that the values of `<sentence id>`, ` <score>`, `<number of source words>` and `<number of target words>` are actually ignored when creating visualizations, so they may as well all be 0.
	
	An example:
	```sh
	0 ||| Obama welcomes Netanyahu ||| 0 ||| Obama empfängt Net@@ any@@ ah@@ u ||| 7 4
	0.723834 0.0471278 0.126415 0.000413103 0.000774298 0.000715227 0.10072 
	0.00572539 0.743366 0.0342341 0.000315413 0.00550132 0.00150629 0.209351 
	0.0122618 0.0073559 0.909192 0.000606444 0.00614908 0.00256837 0.0618667 
	0.00110054 0.0214063 0.0759918 0.000446028 0.104856 0.0435644 0.752634 
	
	```
		
Publications
---------

If you use this tool, please cite the following paper:

Matīss Rikters, Mark Fishel, Ondřej Bojar (2017). "[Visualizing Neural Machine Translation Attention and Confidence.](https://ufal.mff.cuni.cz/pbml)" In The Prague Bulletin of Mathematical Linguistics volume 109 (2017).

```
@inproceedings{Rikters-EtAl2017PBML,
	author = {Rikters, Matīss and Fishel, Mark and Bojar, Ond\v{r}ej},
	journal={The Prague Bulletin of Mathematical Linguistics},
	volume={109},
	pages = {1--12},
	title = {{Visualizing Neural Machine Translation Attention and Confidence}},
	address={Lisbon, Portugal},
	year = {2017}
}
```
	
Examples
---------

  - in the command line as shaded blocks. Example with Neural Monkey alignments (separate source and target subword unit files are required)
	
	```sh
	python process_alignments.py \
	-i test_data/neuralmonkey/alignments.npy  \
	-o color \
	-s test_data/neuralmonkey/src.en.bpe \
	-t test_data/neuralmonkey/out.lv.bpe \
	-f NeuralMonkey
	```
	
  - the same with Nematus alignments (source and target subword units are in the same file)
	
	```sh
	python process_alignments.py \
	-i test_data/nematus/alignments.txt \
	-o color \
	-f Nematus
	```
	
  - in a text file as Unicode block elements
	
	```sh
	python process_alignments.py \
	-i test_data/neuralmonkey/alignments.npy  \
	-o block \
	-s test_data/neuralmonkey/src.en.bpe \
	-t test_data/neuralmonkey/out.lv.bpe \
	-f NeuralMonkey
	```
	
	  or
		
		python process_alignments.py \
		-i test_data/neuralmonkey/alignments.npy  \
		-o block2 \
		-s test_data/neuralmonkey/src.en.bpe \
		-t test_data/neuralmonkey/out.lv.bpe \
		-f NeuralMonkey
	
  - in the browser as links between words (demo [here](http://lielakeda.lv/other/NLP/alignments/?s=19))
	
	```sh
	python process_alignments.py \
	-i test_data/marian/marian.out.lv \
	-s test_data/marian/marian.src.en \
	-o web \
	-f Marian
	```

Parameters for process_alignments.py
---------

| Option | Description                   					| Required 		 										| Possible Values 			 									| Default Value  |
|:------:|:-------------------------------------------------|:-----------------------------------------------------:|:--------------------------------------------------------------|:---------------|
| -f     | NMT framework where the alignments are from 		| No     	 		 									| 'NeuralMonkey', 'Nematus', 'Marian', 'OpenNMT', 'Sockeye' 	| 'NeuralMonkey' |
| -i     | input alignment file			 					| Only if no configuration file is provided				| Path to file													|				 |
| -s     | source sentence subword units 					| For Neural Monkey or Marian 							| Path to file			  	 									|				 |
| -t     | target sentence subword units 					| For Neural Monkey 									| Path to file			  	 									|				 |
| -o     | output type					 					| No      		 	 									| 'web', 'color', 'block', 'block2', 'compare'					| 'web'			 |
| -n     | Number of a specific sentence 					| No     	 		 									| Integer 														| -1 (show all)	 |
| -c     | configuration file 								| No     	 		 									| Path to file													| 				 |
|		 |													|														|																|				 |
| -v     | NMT framework where the 2nd alignments are from 	| For output type'compare'     							| 'NeuralMonkey', 'Nematus', 'Marian', 'OpenNMT', 'Sockeye' 	| 'NeuralMonkey' |
| -w     | input file for the 2nd alignments		 		| For output type'compare'     							| Path to file													|				 |
| -x     | 2nd source sentence subword unit file  			| For output type'compare' and Neural Monkey or Marian 	| Path to file			  	 									|				 |
| -y     | 2nd target sentence subword unit file 			| For output type'compare' and Neural Monkey 			| Path to file			  	 									|				 |

Configuration file
---------

The parameters can be provided via configuration .ini file to have a smaller mess in the command line when calling the script. 

| Block 		| Option	 | Description                   				   |
|:-------------:|:-----------|:------------------------------------------------|
| AlignmentsOne | 			 |								 				   |
|				| From 		 | NMT framework where the alignments are from 	   |
|				| InputFile  | input alignment file			 				   |
|				| SourceFile | source sentence subword units 				   |
|				| TargetFile | target sentence subword units 				   |
| Options 		|			 |												   |
|				| OutputType | output type					 				   |
|				| Number	 | Number of a specific sentence 				   |
| AlignmentsTwo	|			 |												   |
|				| From 		 | NMT framework where the 2nd alignments are from |
|				| InputFile  | input file for the 2nd alignments		 	   |
|				| SourceFile | 2nd source sentence subword unit file  		   |
|				| TargetFile | 2nd target sentence subword unit file 		   |

An example config.ini file:
```Ini
[AlignmentsOne]
InputFile: ./test_data/neuralmonkey/alignments.npy
SourceFile: ./test_data/neuralmonkey/src.en.bpe
TargetFile: ./test_data/neuralmonkey/out.lv.bpe
From: NeuralMonkey

[Options]
OutputType: color
```

Screenshots
---------
Color, Block, Block2  
![N|Solid](https://github.com/M4t1ss/sAliViz/blob/master/assets/Screenshots/colorAlignments.PNG?raw=true) ![N|Solid](https://github.com/M4t1ss/sAliViz/blob/master/assets/Screenshots/blockAlignments.PNG?raw=true) ![N|Solid](https://github.com/M4t1ss/sAliViz/blob/master/assets/Screenshots/block2.png?raw=true) 

Web
![N|Solid](https://github.com/M4t1ss/sAliViz/blob/master/assets/Screenshots/webAlignments.PNG?raw=true)
