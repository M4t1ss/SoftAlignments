# MT Marathon 2019 Tutorial
	
Conect to the remote machine using a tunnel
---------

![N|Solid](https://github.com/M4t1ss/sAliViz/blob/master/assets/MT-Marathon-2019/putty.png?raw=true)

If you use Linux, either clone and run the SoftAlignments repository on your local machine or connect to the cloud machhine using 
`ssh -L 47155:remote_address:47155 mtm@remote_address`. 

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

Run each of these separately and open http://127.0.0.1:47155/ in your browser.
To sort datasets by length or the confidence scores click on `Translation` or any of the scores - `Confidence`, `CDP`, `APin`, `APout`, `Overlap`. Doing this will show bars for the data set where where each bar represents one sentence. The bars for `Translation` represent the source sentence length. Click a bar to navigate to that sentence.

![N|Solid](https://raw.githubusercontent.com/M4t1ss/SoftAlignments/master/assets/Screenshots/webCompare.png?raw=true)


 - RNN alignments

```
cd
cd lab2/SoftAlignments
python ./process_alignments.py -f Marian -i ../songs.rnn.alignments.en -s ../songs.piece.lt
python ./process_alignments.py -f Marian -i ../newstest2019.rnn.alignments.en -s ../newstest2019.piece.lt
```

 - Transformer alignments

```
python ./process_alignments.py -f Marian -i ../newstest2019.transformer.alignments.en -s ../newstest2019.piece.lt
python ./process_alignments.py -f Marian -i ../songs.transformer.alignments.en -s ../songs.piece.lt
```

 - Compare RNN and Transformer alignments

```
python ./process_alignments.py -f Marian -i ../songs.transformer.alignments.en -s ../songs.piece.lt -o compare -v Marian -w ../songs.rnn.alignments.en -x ../songs.piece.lt
python ./process_alignments.py -f Marian -i ../newstest2019.transformer.alignments.en -s ../newstest2019.piece.lt -o compare -v Marian -w ../newstest2019.rnn.alignments.en -x ../newstest2019.piece.lt
```

Translate with Sockeye
---------

 - Download the provided English -> Japanese model trained with Sockeye
 - Download the version of Sockeye that supports outputting attention for transformer models
  - This one https://github.com/tilde-nlp/sockeye/tree/transformer-attention
  - Or copy the changes from this PR https://github.com/awslabs/sockeye/pull/504/files to the latest version of Sockeye
 - Translate the provided test file using `translate.sh` and run the visualization

Here we will use the CPU version for translation
 
```
cd
cd lab2
wget http://lielakeda.lv/ENJA_Sockeye_model_data.zip
unzip ENJA_Sockeye_model_data.zip
chmod +x translate.sh
git clone https://github.com/tilde-nlp/sockeye.git
cd sockeye
git checkout transformer-attention
pip install -r requirements/requirements.txt
pip install .
cd ..
./translate.sh
cd SoftAlignments
python ./process_alignments.py -f Sockeye -i ../opensubs.dev.bpe.translated.ja
```

Try all of this on your own data
---------

 - Prepare training data up to the point of encoding it to subword units (Subword NMT, Sentencepiece or any other)
 - Edit the `data.sh` script accordingly
 - Use the `fast_align.sh` script to generate alignments for the training data (this may take some time)
 - Use the `train.sh` script to train your model (training takes a lot of time)
 - Edit the `translate.sh` script so that it translates using the newly trained model
 - Translate an evaluation set and browse the resulting translations using the Alignment Visualization
