#!/bin/bash
. ./data.sh

$MOSES_DIR/scripts/ems/support/prepare-fast-align.perl $PREFIX.$EXP_SRC $PREFIX.$EXP_TRG > $PREFIX.$EXP_SRC-$EXP_TRG
	

$FAST_ALIGN_DIR/fast_align -i $PREFIX.$EXP_SRC-$EXP_TRG -d -o -v > $PREFIX.$EXP_SRC-$EXP_TRG.forward.align
$FAST_ALIGN_DIR/fast_align -i $PREFIX.$EXP_SRC-$EXP_TRG -d -o -v -r > $PREFIX.$EXP_SRC-$EXP_TRG.reverse.align

$FAST_ALIGN_DIR/atools -i $PREFIX.$EXP_SRC-$EXP_TRG.forward.align -j $PREFIX.$EXP_SRC-$EXP_TRG.reverse.align -c grow-diag-final-and > $PREFIX.$EXP_SRC-$EXP_TRG.grow-diag-final-and
