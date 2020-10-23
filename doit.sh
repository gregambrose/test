#!/bin/bash
for FILE in `find . -name "*.dec"`
do
    echo ${FILE}
    DIR=`dirname "${FILE}"`;
    FILENAME=`basename "${FILE}" .dec`;
    echo mv -f "$FILE" ${DIR}/${FILENAME};
done
