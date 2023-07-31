tgz: help minify
	cp config/config.php config/config.defaults
	VERSION=`./version.sh`;	\
	tar zcf phpreport_$${VERSION}.tar.gz --exclude=Makefile --exclude=*~ \
		--exclude=version.sh --exclude=docs --exclude=config/config.php \
		--exclude=web/APITest.php --exclude=web/js/APITest.js \
		--exclude=*.tar.gz --exclude=*.zip *

zip: tgz
	VERSION=`./version.sh`;	\
	tar tf phpreport_$${VERSION}.tar.gz | zip -@ phpreport_$${VERSION}.zip


minify:
	# WARNING: this will remove any unstaged changes! Do not run on a development directory
	for i in `find web -name "*.min.js" -not -path "*web/vuejs/*"`; do rm $$i; done
	for i in `find web -name "*.min.js.map" -not -path "*web/vuejs/*"`; do rm $$i; done
	#revert any previous minification changes
	git reset --hard HEAD
	for i in `find web -name "*.js" -not -path "*web/vuejs/*"` ; do \
	  #extract file name to be used in the uglify output \
	  FILE=`basename -s .js $$i`; \
	  DIR=`dirname $$i`; \
	  HASH=`md5sum $$i | awk '{ print $$1 }'`; \
	  cd $$DIR; \
	  uglifyjs $${FILE}.js --output $${FILE}.$${HASH}.min.js \
	      --source-map "filename='$${FILE}.$${HASH}.min.js.map'" -m; \
	  cd -; \
	  for j in `find web -name "*.php"`; do \
	    #modify script tags to link the minified file \
	    sed "s/<script src=\"\(.*\)$${FILE}.js\">/<script src=\"\1$${FILE}.$${HASH}.min.js\">/" $$j > tmp; \
	    cp tmp $$j; \
	    done \
	  done
	rm tmp

