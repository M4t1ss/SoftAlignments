# NMT soft alignment visualisations
Soft alignment visualisations tools for command line and web. Parts of the web version are borrowed from [Nematus utils](https://github.com/rsennrich/nematus/tree/master/utils)
  - Train a neural MT system (like [Neural Monkey](https://github.com/ufal/neuralmonkey/) or [Nematus](https://github.com/rsennrich/nematus/)
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
    - in the command line as shaded blocks
    ```sh
    python2 process_alignments.py -i test_data/alignments.npy -o color -s test_data/test.src.en.bpe -t test.out.lv.bpe
    ```
	- in a text file as Unicode block elements
    ```sh
    python2 process_alignments.py -i test_data/alignments.npy -o block -s test_data/test.src.en.bpe -t test.out.lv.bpe
    ```
	or
    ```sh
    python2 process_alignments.py -i test_data/alignments.npy -o block2 -s test_data/test.src.en.bpe -t test.out.lv.bpe
    ```
	- in the browser as links between words (demo [here](http://lielakeda.lv/other/NLP/alignments/?s=19))


Screenshots:
  - Block
  
![N|Solid](https://github.com/M4t1ss/sAliViz/blob/master/screenshots/blockAlignments.PNG?raw=true)
  - Block2
  
![N|Solid](https://github.com/M4t1ss/sAliViz/blob/master/screenshots/block2.png?raw=true)
  - Color
  
![N|Solid](https://github.com/M4t1ss/sAliViz/blob/master/screenshots/colorAlignments.PNG?raw=true)
  - Web
  
![N|Solid](https://github.com/M4t1ss/sAliViz/blob/master/screenshots/webAlignments.PNG?raw=true)
