#!/bin/bash

set -ex

python process_alignments.py \
-i test_data/alignments_neural_monkey.npy \
-o color \
-s test_data/test.src.en.bpe \
-t test_data/test.out.lv.bpe \
-f NeuralMonkey

python process_alignments.py \
-i test_data/alignments_nematus.txt \
-o color \
-f Nematus

python process_alignments.py \
-i test_data/alignments_neural_monkey.npy \
-o block \
-s test_data/test.src.en.bpe \
-t test_data/test.out.lv.bpe \
-f NeuralMonkey

python process_alignments.py \
-i test_data/alignments_neural_monkey.npy \
-o block2 \
-s test_data/test.src.en.bpe \
-t test_data/test.out.lv.bpe \
-f NeuralMonkey

echo Tests OK.