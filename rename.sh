#!/usr/bin/env bash

echo "Begin renaming";

cd images/cards/compressed

for D in *; do
    echo "Renaming images:";

    # cd, foreach image cd out
    cd "$D";

    for F in *.jpg; do
        mv "$F" "${F//'.full'/}";
    done

    cd ..;

    echo "$D completed";
done