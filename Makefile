VERSION=2.0~beta1

tgz:
	tar zcf phpreport_$(VERSION).tar.gz --exclude=Makefile --exclude=*~ \
		--exclude=config/config.php --exclude=phpreport_$(VERSION).tar.gz *
