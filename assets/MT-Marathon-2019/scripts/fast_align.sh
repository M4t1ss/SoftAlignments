#!/bin/bash
. ./data.sh

# Concatenate source & target
$MOSES_DIR/scripts/ems/support/prepare-fast-align.perl $PREFIX.$EXP_SRC $PREFIX.$EXP_TRG > $PREFIX.$EXP_SRC-$EXP_TRG
	
# Clean up empty lines
cat $PREFIX.$EXP_SRC-$EXP_TRG | grep -v -e "||| $" | grep -v -e "^ |||" > c.$PREFIX.$EXP_SRC-$EXP_TRG
cat c.$PREFIX.$EXP_SRC-$EXP_TRG | cut -d '|' -f1 | awk '{$1=$1};1' > c.$PREFIX.$EXP_SRC
cat c.$PREFIX.$EXP_SRC-$EXP_TRG | cut -d '|' -f4 | awk '{$1=$1};1' > c.$PREFIX.$EXP_TRG
	
# Do the magic
$FAST_ALIGN_DIR/fast_align -i c.$PREFIX.$EXP_SRC-$EXP_TRG -d -o -v > c.$PREFIX.$EXP_SRC-$EXP_TRG.forward.align
$FAST_ALIGN_DIR/fast_align -i c.$PREFIX.$EXP_SRC-$EXP_TRG -d -o -v -r > c.$PREFIX.$EXP_SRC-$EXP_TRG.reverse.align

$FAST_ALIGN_DIR/atools -i c.$PREFIX.$EXP_SRC-$EXP_TRG.forward.align -j c.$PREFIX.$EXP_SRC-$EXP_TRG.reverse.align -c grow-diag-final-and > c.$PREFIX.$EXP_SRC-$EXP_TRG.grow-diag-final-and

