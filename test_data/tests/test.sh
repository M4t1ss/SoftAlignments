#!/bin/bash
DIR=test_data/tests

set -ex

DIFF1=`comm -3 <(sort $DIR/weHope.txt) <(sort $DIR/weHope-test.txt) | wc -c`

if [ $DIFF1 == 0 ] ; then
	echo "Tests OK."
else
	echo "Test 1 Filed."
fi

rm -rf $DIR/weHope-test.txt
