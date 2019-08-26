# MT Marathon 2019 Tutorial
	
Conect to PuTTY with port forwarding
---------

![N|Solid](https://github.com/M4t1ss/sAliViz/blob/master/assets/MT-Marathon-2019/putty.png?raw=true)

Get everything
---------

Clone the NMT Alignment Visualization repository and download the models and data

```
cd
mkdir lab2
cd lab2
git clone https://github.com/m4t1ss/SoftAlignments.git
wget http://lielakeda.lv/LTEN_models_data.zip
unzip LTEN_models_data.zip
```


Generate translations with attention alignments
---------

```
cd
cd lab2/SoftAlignments/assets/MT-Marathon-2019/scripts
chmod +x translate.sh
./translate.sh
```

Run the Alignment Visualization
---------

Run each of these separately and open http://127.0.0.1:47155/ in your browser

 - RNN alignments

 `python ./process_alignments.py -f Marian -i songs.rnn.alignments.en -s songs.piece.lt`

 `python ./process_alignments.py -f Marian -i newstest2019.rnn.alignments.en -s newstest2019.piece.lt`

 - Transformer alignments

 `python ./process_alignments.py -f Marian -i newstest2019.transformer.alignments.en -s newstest2019.piece.lt`

 `python ./process_alignments.py -f Marian -i songs.transformer.alignments.en -s songs.piece.lt`

 - Compare RNN and Transformer alignments

 `python ./process_alignments.py -f Marian -i songs.transformer.alignments.en -s songs.piece.lt -o compare -v Marian -w songs.rnn.alignments.en -x songs.piece.lt`

 `python ./process_alignments.py -f Marian -i newstest2019.transformer.alignments.en -s newstest2019.piece.lt -o compare -v Marian -w newstest2019.rnn.alignments.en -x newstest2019.piece.lt`
	

Try all of this on your own data
---------

 - Prepare training data up to the point of encoding it to subword units (Subword NMT, Sentencepiece or any other)
 - Edit the `data.sh` script accordingly
 - Use the `fast_align.sh` script to generate alignments for the training data (this may take some time)
 - Use the `train.sh` script to train your model (training takes a lot of time)
 - Edit the `translate.sh` script so that it translates using the newly trained model
 - Translate an evaluation set and browse the resulting translations using the Alignment Visualization
