#!/usr/bin/env bash

echo "Begin compression";

cd images/cards/uncompressed

for D in *; do
    if [ ! -d "../compressed/$D" ]; then
        echo "Directory does not exist; creating ../compressed/$D";
        mkdir -p "../compressed/$D"
    fi

    echo "Compressing images:";
    mogrify -path "/home/brandin/www/cdn.mtgbracket/images/cards/compressed/$D" -quality 30 "$D/*.jpg"

    echo "$D completed";
done

echo "Compression complete";