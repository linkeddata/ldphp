USER_SITE := $(shell python -c 'import site; print site.USER_SITE')

install: ${USER_SITE}/rdf.pth

${USER_SITE}/rdf.pth:
	mkdir -p ${USER_SITE}
	echo ${PWD}/lib/python > $@
