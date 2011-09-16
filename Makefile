VERSION=2.0~beta1

tgz:
	tar zcf phpreport_$(VERSION).tar.gz --exclude=Makefile --exclude=*~ \
		--exclude=config/config.php --exclude=phpreport_$(VERSION).tar.gz *

zip: tgz
	tar tf phpreport_$(VERSION).tar.gz | zip -@ phpreport_$(VERSION).zip
