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
	* Run [**nematus/translate.py**](https://github.com/EdinburghNLP/nematus#using-a-trained-model) with the **--output_alignment** or **-a** parameter

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
	* Or run **amun** with the parameters **--no-debpe --return-soft-alignment**
	
* [OpenNMT](https://github.com/OpenNMT/OpenNMT)
	Run translate.lua to translate with the `-save_attention` parameter to save attentions to a file

* [Sockeye](https://github.com/awslabs/sockeye)
	Run sockeye.translate to translate with the `--output-type` parameter set to `translation_with_alignment_matrix` to save attentions to a file

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
	-i test_data/amunmt/amu.out.lv \
	-s test_data/amunmt/amu.src.en \
	-o web \
	-f AmuNMT
	```

Parameters for process_alignments.py
---------

| Option | Description                   | Required 		 | Possible Values 			 						| Default Value  |
|:------:|:------------------------------|:-----------------:|:-------------------------------------------------|:---------------|
| -i     | input alignment file			 | yes     			 | Path to file										|				 |
| -o     | output alignment matrix type	 | No      		 	 | 'web', 'color', 'block', 'block2'				| 'web'			 |
| -s     | source sentence subword units | For Neural Monkey | Path to file			  	 						|				 |
| -t     | target sentence subword units | For Neural Monkey | Path to file			  	 						|				 |
| -f     | Where are the alignments from | No     	 		 | 'NeuralMonkey', 'Nematus', 'AmuNMT', 'OpenNMT', 'Sockeye' 	| 'NeuralMonkey' |
| -n     | Number of a specific sentence | No     	 		 | Integer 											| -1 (show all)	 |

Screenshots
---------
Color, Block, Block2  
![N|Solid](https://github.com/M4t1ss/sAliViz/blob/master/assets/Screenshots/colorAlignments.PNG?raw=true) ![N|Solid](https://github.com/M4t1ss/sAliViz/blob/master/assets/Screenshots/blockAlignments.PNG?raw=true) ![N|Solid](https://github.com/M4t1ss/sAliViz/blob/master/assets/Screenshots/block2.png?raw=true) 

Web
![N|Solid](https://github.com/M4t1ss/sAliViz/blob/master/assets/Screenshots/webAlignments.PNG?raw=true)
