#/bin/bash

PATH_TO_MARIAN=

$PATH_TO_MARIAN/marian-decoder -c model.npz.best-translation.npz.decoder.yml --alignment soft --quiet-translation < newstest2019.piece.lt > newstest2019.translation.alignments.en
$PATH_TO_MARIAN/marian-decoder -c model.npz.best-translation.npz.decoder.yml --alignment soft --quiet-translation < songs.piece.lt > songs.translation.alignments.en

python format-output.py newstest2019.translation.alignments.en newstest2019.piece.lt > newstest2019.translation.alignments.formatted.en
python format-output.py songs.translation.alignments.en songs.piece.lt > songs.translation.alignments.formatted.en