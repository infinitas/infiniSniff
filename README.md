PHP CodeSniffer for Infinitas
=============================

This repository contains the Infinitas standard for use with the pear package [PHP CodeSniffer](http://pear.php.net/package/PHP_CodeSniffer)

This is a copy of AD7six's CakePHP code sniffer rules with a few light modifications. Its designed to be used as part of the Infinitas pre-commit hooks that can be found https://github.com/Infinitas-project/infiniHooks

# Installation

Obviously, you need [PHP CodeSniffer](http://pear.php.net/package/PHP_CodeSniffer/download) for this repository to have any meaning.

Locate the `CodeSniffer/Standards` folder in your CodeSniffer install, and install this repository with the name "Infinitas" e.g.:

	cd /usr/share/pear/PHP/CodeSniffer/Standards/
	git clone https://dogmatic69@github.com/Infinitas-project/infiniSniff.git Infinitas

	other places the standards could be found
		/usr/share/php/PHP/CodeSniffer/Standards

If you work mainly with Infinitas, or your work simply follows the same coding standards, you may wish to configure PHP Code Sniffer to use this standard by default:

	phpcs --config-set default_standard Infinitas

# Use as a git pre-commit hook

To run `phpcs` checks each time you commit a file, you can use the pre-commit hook provided in this repo. For example

	cd /var/www/apps/myapp/.git/hooks/
	cp /usr/share/pear/PHP/CodeSniffer/Standards/Infinitas/pre-commit .
	chmod +x pre-commit

# References and Credits

* [http://lifeisbetter.in/blog/2010/08/09/yet-another-version-of-php-codesniffer-for-cakephp](http://lifeisbetter.in/blog/2010/08/09/yet-another-version-of-php-codesniffer-for-cakephp)
* [https://github.com/venkatrs/Cake_PHP_CodeSniffer](https://github.com/venkatrs/Cake_PHP_CodeSniffer)
* [http://www.sanisoft.com/downloads/cakephp_sniffs](http://www.sanisoft.com/downloads/cakephp_sniffs)
* [https://github.com/AD7six/cakephp-codesniffs](https://github.com/AD7six/cakephp-codesniffs)