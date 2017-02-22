# sAliViz
Soft alignment visualisations tools for command line and web
  - Train a neural MT system (like Neural Monkey or Nematus)
  - Get a NumPy 3d tensor (or json) of alignments (word level or BPE)
  - Convert the alignments to a text file
  - Visualize
    - in the command line as shaded blocks
    ```sh
    python2 sAliViz.py -i numpytensor.npy -o color -s source_sentence_bpe.txt -t target_sentence_bpe.txt
    ```
	- in a text file as Unicode block elements
    ```sh
    python2 sAliViz.py -i numpytensor.npy -o block -s source_sentence_bpe.txt -t target_sentence_bpe.txt
    ```
	or
    ```sh
    python2 sAliViz.py -i numpytensor.npy -o block2 -s source_sentence_bpe.txt -t target_sentence_bpe.txt
    ```
	- in the browser as links between words (demo [here](http://lielakeda.lv/other/NLP/alignments/?s=19))
    ```sh
    python2 alTXTtoJSON.py -i alignments.txt
    ```
