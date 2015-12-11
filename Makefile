.PHONY: build clean test

build:
	composer install --dev

clean:
	rm -rf $(CURDIR)/vendor
	rm -f $(CURDIR)/composer.lock

test: build
	$(CURDIR)/vendor/bin/phpunit tests
