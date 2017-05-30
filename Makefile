VERSION=2.17

tgz: help
	cp config/config.php config/config.defaults
	tar zcf phpreport_$(VERSION).tar.gz --exclude=Makefile --exclude=*~ \
		--exclude=docs --exclude=web/ext/ext-all-debug* \
		--exclude=config/config.php --exclude=*.tar.gz --exclude=*.zip *

zip: tgz
	tar tf phpreport_$(VERSION).tar.gz | zip -@ phpreport_$(VERSION).zip

help:
	#create dirs
	mkdir -p help/user
	mkdir -p help/admin
	mkdir -p help/developer
	#copy images
	cp -r docs/user/i help/user
	cp -r docs/developer/i help/developer
	#generate footer \
	echo -e '\n.. class:: credits\n\n  This file is part of PhpReport $(VERSION) documentation.' > footer; \
	echo -ne '  Generated on ' >> footer; \
	date >> footer; \
	#parse rst
	for i in `find -name *.rst` ; do \
	  #rename .rst for .html in links to other doc pages \
	  sed 's/\.rst/\.html/g' $$i > tmp; \
	  #append footer \
	  cat footer >> tmp; \
	  #extract file name to be used as the output file name \
	  FILE=`echo $$i | awk '{firstpart=substr($$i, 8);x=index(firstpart,".rst");print substr(firstpart, 1,x)}'`; \
	  #generate html file \
	  rst2html tmp help/$${FILE}html; \
	  done; \
	rm tmp; \
	rm footer;

#prevent makefile docs are up-to-date
.PHONY: help
