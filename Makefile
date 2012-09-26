USER_SITE := $(shell python -c 'import site; print site.USER_SITE')

install: ${USER_SITE}/rdf.pth

${USER_SITE}/rdf.pth:
	mkdir -p ${USER_SITE}
	echo ${PWD}/lib/python > $@

callgraph: www/root/callgraph.png

www/root/callgraph.png:
	find -name '*.php' | grep -v arc2 | xargs phpcallgraph -f png -o $@

check-syntax:
	find -name '*.php' | xargs -n1 php -l
