.PHONY: build build-dev clean test

build:
	composer install --no-dev

build-dev:
	composer install --dev

clean:
	rm -rf $(CURDIR)/vendor
	rm -f $(CURDIR)/composer.lock

test: build-dev
	$(CURDIR)/vendor/bin/phpunit tests
