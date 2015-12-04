#!/bin/bash

step=60 #间隔的秒数，不能大于60
for (( i = 0; i < 60; i=(i+step) )); do
    /usr/local/webserver/sphinx/bin/indexer --all --rotate
    sleep $step
done
exit
