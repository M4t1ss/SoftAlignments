#/bin/bash

PATH_TO_MARIAN=

$PATH_TO_MARIAN/marian-decoder \
	-c transformer-model.npz.best-translation.npz.decoder.yml \
	--alignment soft \
	--quiet-translation \
	< newstest2019.piece.lt \
	> newstest2019.transformer.alignments.en
$PATH_TO_MARIAN/marian-decoder \
	-c transformer-model.npz.best-translation.npz.decoder.yml \
	--alignment soft \
	--quiet-translation \
	< songs.piece.lt \
	> songs.transformer.alignments.en

$PATH_TO_MARIAN/marian-decoder \
	-c rnn-model.npz.best-translation.npz.decoder.yml \
	--alignment soft \
	--quiet-translation \
	< newstest2019.piece.lt \
	> newstest2019.rnn.alignments.en
$PATH_TO_MARIAN/marian-decoder \
	-c rnn-model.npz.best-translation.npz.decoder.yml \
	--alignment soft \
	--quiet-translation \
	< songs.piece.lt \
	> songs.rnn.alignments.en

python format-output.py newstest2019.transformer.alignments.en newstest2019.piece.lt > newstest2019.transformer.alignments.formatted.en
python format-output.py songs.transformer.alignments.en songs.piece.lt > songs.transformer.alignments.formatted.en

python format-output.py newstest2019.rnn.alignments.en newstest2019.piece.lt > newstest2019.rnn.alignments.formatted.en
python format-output.py songs.rnn.alignments.en songs.piece.lt > songs.rnn.alignments.formatted.en