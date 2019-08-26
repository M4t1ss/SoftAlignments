#!/bin/bash
DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"

export DATA_DIR=/home/mtm/lab2
export MOSES_DIR=
export MARIAN_DIR=/home/mtm/marian-dev/build
export FAST_ALIGN_DIR=

export EXP_SRC=en
export EXP_TRG=lt
export EXP_MODEL_PREFIX=lten
export PREFIX=corpus.piece

export TRAIN_PREFIX=corpus.clean
export DEVEL_PREFIX=newsdev2019
export EVAL_PREFIX=newstest2019

export EXP_TRAIN_SRC=$DATA_DI/$TRAIN_PREFIX.piece.$EXP_SRC
export EXP_TRAIN_TRG=$DATA_DI/$TRAIN_PREFIX.piece.$EXP_TRG

export EXP_VALID_SRC=$DATA_DI/$DEVEL_PREFIX.piece.$EXP_SRC
export EXP_VALID_TRG=$DATA_DI/$DEVEL_PREFIX.piece.$EXP_TRG

export EXP_EVAL_SRC=$DATA_DI/$EVAL_PREFIX.piece.$EXP_SRC
export EXP_EVAL_TRG=$DATA_DI/$EVAL_PREFIX.piece.$EXP_TRG

export EXP_ALIGNMENT=$DATA_DI/aligned.$EXP_SRC$EXP_TRG.grow-diag-final-and

export EXP_DICT_SRC=$DATA_DI/shared-vocab.$EXP_SRC$EXP_TRG.yml
export EXP_DICT_TRG=$DATA_DI/shared-vocab.$EXP_SRC$EXP_TRG.yml

export EXP_MODEL_DIR=models-$EXP_MODEL_PREFIX-$EXP_SRC-$EXP_TRG
export EXP_MODELS_SAVETO=$EXP_MODEL_DIR/model.npz

mkdir -p $DIR/$EXP_MODEL_DIR
