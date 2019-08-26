#/bin/bash
. ./data.sh


$MARIAN_DIR/marian-decoder \
	-c $DATA_DIR/transformer-model.npz.best-translation.npz.decoder.yml \
	--alignment soft \
	--quiet-translation \
	< $DATA_DIR/newstest2019.piece.lt \
	> $DATA_DIR/newstest2019.transformer.alignments.en
$MARIAN_DIR/marian-decoder \
	-c $DATA_DIR/transformer-model.npz.best-translation.npz.decoder.yml \
	--alignment soft \
	--quiet-translation \
	< $DATA_DIR/songs.piece.lt \
	> $DATA_DIR/songs.transformer.alignments.en

$MARIAN_DIR/marian-decoder \
	-c $DATA_DIR/rnn-model.npz.best-translation.npz.decoder.yml \
	--alignment soft \
	--quiet-translation \
	< $DATA_DIR/newstest2019.piece.lt \
	> $DATA_DIR/newstest2019.rnn.alignments.en
$MARIAN_DIR/marian-decoder \
	-c $DATA_DIR/rnn-model.npz.best-translation.npz.decoder.yml \
	--alignment soft \
	--quiet-translation \
	< $DATA_DIR/songs.piece.lt \
	> $DATA_DIR/songs.rnn.alignments.en