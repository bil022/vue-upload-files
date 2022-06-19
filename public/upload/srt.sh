#!/bin/bash

for id in $*; do
  if ! [ -e $id ]; then
    for x in `ls $id.* | ./srt.pl`; do cat $x >> $id; done
  else
    echo skip $id
  fi
done
