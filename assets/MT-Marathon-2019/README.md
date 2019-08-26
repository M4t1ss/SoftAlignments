# MT Marathon 2019 Tutorial

Models and data
---------

  - Models and data are available [here](https://failiem.lv/u/sw6jqmm2)
  - There are two English to Lithuanian Marian NMT models tained on WMT 2019 data
    - One is a transformer model and the other is an RNN
  - Evaluation data
	- WMT 2019 test set
	- Several Lithuanian folk songs
	

Scripts
---------

  - `data.sh` - This is used in all other scripts. Fill out the variables for data, moses decoder, marian and fast_align directories.
  - `fast_align.sh` - A small example of running fast_align to generate alignments for training Marian with guided alignments.
  - `train.sh` - Example of taining a transformer with Marian using guided alignments.
  - `translate.sh` - A script for translating the evaluation data with the provided models.

Presentation
---------

  - [Here](https://github.com/M4t1ss/SoftAlignments/tree/master/assets/MT-Marathon-2019/presentation/MTM-2019-presentation.pdf)

