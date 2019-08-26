# MT Marathon 2019 Tutorial

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
mkdir lab2
cd lab2/assets/MT-Marathon-2019/scripts
./translate.sh
```

Run the Alignment Visualization
---------

Run each of these separately and open http://127.0.0.1:47155/ in your browser

`python ./process_alignments.py -f Marian -i songs.rnn.alignments.en -s songs.piece.lt`
`python ./process_alignments.py -f Marian -i newstest2019.rnn.alignments.en -s newstest2019.piece.lt`

`python ./process_alignments.py -f Marian -i newstest2019.transformer.alignments.en -s newstest2019.piece.lt`
`python ./process_alignments.py -f Marian -i songs.transformer.alignments.en -s songs.piece.lt`

`python ./process_alignments.py -f Marian -i songs.transformer.alignments.en -s songs.piece.lt -o compare -v Marian -w songs.rnn.alignments.en -x songs.piece.lt`
`python ./process_alignments.py -f Marian -i newstest2019.transformer.alignments.en -s newstest2019.piece.lt -o compare -v Marian -w newstest2019.rnn.alignments.en -x newstest2019.piece.lt`
	

Marian
---------

```
git clone https://github.com/marian-nmt/marian-dev.git
cd marian-dev
mkdir build
cd build
cmake ..
make -j
```