# NMT soft alignment visualisations
Soft alignment visualisations tools for command line and web. Parts of the web version are borrowed from [Nematus utils](https://github.com/rsennrich/nematus/tree/master/utils)
  - Train a neural MT system (like [Neural Monkey](https://github.com/ufal/neuralmonkey/) or [Nematus](https://github.com/rsennrich/nematus/))
  - Get a NumPy 3d tensor of alignments (word level or BPE)
  - Convert the alignments to a bunch of text files
    - 2 files for command line visualisation
		- *.txt
		- *.block.txt, *.block2.txt or *.color.txt
    - 3 files for web visualisation
		- *.src.js
		- *.trg.js
		- *.ali.js
  
  - Visualize
    - in the command line as shaded blocks. Example with Neural Monkey alignments (source and target subword unit files are required)
    ```sh
    python2 process_alignments.py -i test_data/alignments_neural_monkey.npy -o color -s test_data/test.src.en.bpe -t test.out.lv.bpe -f NeuralMonkey
    ```
    - the same with Nematus alignments (source and target subword units are in the same file)
    ```sh
    python2 process_alignments.py -i test_data/alignments_nematus.txt -o color -f Nematus
    ```
	- in a text file as Unicode block elements
    ```sh
    python2 process_alignments.py -i test_data/alignments_neural_monkey.npy -o block -s test_data/test.src.en.bpe -t test.out.lv.bpe -f NeuralMonkey
    ```
	or
    ```sh
    python2 process_alignments.py -i test_data/alignments_neural_monkey.npy -o block2 -s test_data/test.src.en.bpe -t test.out.lv.bpe -f NeuralMonkey
    ```
	- in the browser as links between words (demo [here](http://lielakeda.lv/other/NLP/alignments/?s=19))

Parameters for process_alignments.py:

| Option | Description                   | Required 		 | Possible Values 			 |
|:------:|:------------------------------|:-----------------:|:--------------------------|
| -i     | input alignment file			 | yes     			 | Path to file			  	 |
| -o     | output alignment matrix type	 | yes      		 | 'color', 'block', 'block2'|
| -s     | source sentence subword units | For Neural Monkey | Path to file			  	 |
| -t     | target sentence subword units | For Neural Monkey | Path to file			  	 |
| -f     | Where are the alignments from | yes      		 | 'NeuralMonkey', 'Nematus' |

Screenshots:
  - Block
  
![N|Solid](https://github.com/M4t1ss/sAliViz/blob/master/screenshots/blockAlignments.PNG?raw=true)
  - Block2
  
![N|Solid](https://github.com/M4t1ss/sAliViz/blob/master/screenshots/block2.png?raw=true)
  - Color
  
![N|Solid](https://github.com/M4t1ss/sAliViz/blob/master/screenshots/colorAlignments.PNG?raw=true)
  - Web
  
![N|Solid](https://github.com/M4t1ss/sAliViz/blob/master/screenshots/webAlignments.PNG?raw=true)
