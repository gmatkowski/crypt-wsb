#!/usr/bin/env bash

if [[ $OSTYPE == 'darwin'* ]]; then
    echo 'macOS'
    docker exec -it wsb-app bash
else
    winpty docker exec -it wsb-app bash
fi
