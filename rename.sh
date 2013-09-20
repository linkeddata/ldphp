#/bin/bash
find . | while read -r file
do
    newfile=$(echo "$file" | sed 's/meta/acl/g')
    mv $file $newfile
done
