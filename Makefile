VERSION=2.0~beta2

tgz:
	tar zcf phpreport_$(VERSION).tar.gz --exclude=Makefile --exclude=*~ \
		--exclude=config/config.php --exclude=phpreport_$(VERSION).tar.gz *

zip: tgz
	tar tf phpreport_$(VERSION).tar.gz | zip -@ phpreport_$(VERSION).zip

help:
	#create dirs
	mkdir -p help/user
	#copy images
	cp -r docs/user/i help/user; \
	#parse rst
	for i in `find -name *.rst` ; do \
	  FILE=`echo $$i | awk '{firstpart=substr($$i, 8);x=index(firstpart,".rst");print substr(firstpart, 1,x)}'`; \
	  rst2html $$i help/$${FILE}html; \
	  done

#prevent makefile docs are up-to-date
.PHONY: help
