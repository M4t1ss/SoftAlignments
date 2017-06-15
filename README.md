# NMT Attention Alignment Visualizations
An attention alignment visualization tool for command line and web. A part of the web version was borrowed from [Nematus utils](https://github.com/rsennrich/nematus/tree/master/utils)

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

* Nematus
	* Run [**nematus/translate.py**](https://github.com/rsennrich/nematus/#using-a-trained-model) with the **--output_alignment** or **-a** parameter

* Neural Monkey
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

Examples
---------

  - in the command line as shaded blocks. Example with Neural Monkey alignments (separate source and target subword unit files are required)
	
	```sh
	python process_alignments.py \
	-i test_data/alignments_neural_monkey.npy \
	-o color \
	-s test_data/test.src.en.bpe \
	-t test_data/test.out.lv.bpe \
	-f NeuralMonkey
	```
	
  - the same with Nematus alignments (source and target subword units are in the same file)
	
	```sh
	python process_alignments.py \
	-i test_data/alignments_nematus.txt \
	-o color \
	-f Nematus
	```
	
  - in a text file as Unicode block elements
	
	```sh
	python process_alignments.py \
	-i test_data/alignments_neural_monkey.npy \
	-o block \
	-s test_data/test.src.en.bpe \
	-t test_data/test.out.lv.bpe \
	-f NeuralMonkey
	```
	
	  or
		
		```sh
		python process_alignments.py \
		-i test_data/alignments_neural_monkey.npy \
		-o block2 \
		-s test_data/test.src.en.bpe \
		-t test_data/test.out.lv.bpe \
		-f NeuralMonkey
		```
	
  - in the browser as links between words (demo [here](http://lielakeda.lv/other/NLP/alignments/?s=19))
	
	```sh
	python process_alignments.py \
	-i test_data/alignments_nematus.txt \
	-o web \
	-f Nematus
	```

Parameters for process_alignments.py
---------

| Option | Description                   | Required 		 | Possible Values 			 		| Default Value  |
|:------:|:------------------------------|:-----------------:|:---------------------------------|:---------------|
| -i     | input alignment file			 | yes     			 | Path to file						|				 |
| -o     | output alignment matrix type	 | No      		 	 | 'web', 'color', 'block', 'block2'| 'web'			 |
| -s     | source sentence subword units | For Neural Monkey | Path to file			  	 		|				 |
| -t     | target sentence subword units | For Neural Monkey | Path to file			  	 		|				 |
| -f     | Where are the alignments from | No     	 		 | 'NeuralMonkey', 'Nematus' 		| 'NeuralMonkey' |

Screenshots
---------
Color, Block, Block2  
![N|Solid](https://github.com/M4t1ss/sAliViz/blob/master/screenshots/colorAlignments.PNG?raw=true) ![N|Solid](https://github.com/M4t1ss/sAliViz/blob/master/screenshots/blockAlignments.PNG?raw=true) ![N|Solid](https://github.com/M4t1ss/sAliViz/blob/master/screenshots/block2.png?raw=true) 

Web
![N|Solid](https://github.com/M4t1ss/sAliViz/blob/master/screenshots/webAlignments.PNG?raw=true)
