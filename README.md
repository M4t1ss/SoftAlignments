# NMT soft alignment visualisations
Soft alignment visualisations tools for command line and web
  - Train a neural MT system (like Neural Monkey or Nematus)
  - Get a NumPy 3d tensor (or json) of alignments (word level or BPE)
  - Convert the alignments to a text file
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
    ```sh
    python2 alTXTtoJSON.py -i alignments.txt
    ```

Screenshots:
  - Block
  
![N|Solid](https://github.com/M4t1ss/sAliViz/blob/master/screenshots/blockAlignments.PNG?raw=true)
  - Color
  
![N|Solid](https://github.com/M4t1ss/sAliViz/blob/master/screenshots/colorAlignments.PNG?raw=true)
  - Web
  
![N|Solid](https://github.com/M4t1ss/sAliViz/blob/master/screenshots/webAlignments.PNG?raw=true)
