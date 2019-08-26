#/bin/bash
. ./data.sh


$MARIAN_DIR/marian-decoder \
	-c transformer-model.npz.best-translation.npz.decoder.yml \
	--alignment soft \
	--quiet-translation \
	< newstest2019.piece.lt \
	> newstest2019.transformer.alignments.en
$MARIAN_DIR/marian-decoder \
	-c transformer-model.npz.best-translation.npz.decoder.yml \
	--alignment soft \
	--quiet-translation \
	< songs.piece.lt \
	> songs.transformer.alignments.en

$MARIAN_DIR/marian-decoder \
	-c rnn-model.npz.best-translation.npz.decoder.yml \
	--alignment soft \
	--quiet-translation \
	< newstest2019.piece.lt \
	> newstest2019.rnn.alignments.en
$MARIAN_DIR/marian-decoder \
	-c rnn-model.npz.best-translation.npz.decoder.yml \
	--alignment soft \
	--quiet-translation \
	< songs.piece.lt \
	> songs.rnn.alignments.en