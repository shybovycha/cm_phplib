# CloudMade PHP Client

## Installing

First of all you should sign-up for a CloudMade services and obtain a valid **API key**.

Then, install `PHP5` and some web server *(like Apache or nginx)*. Pretty good instructions on
how to do it on Ubuntu/Debian system are given in [HOWTOForge](http://www.howtoforge.com/ubuntu_debian_lamp_server).

When you are done with all the requirements, just unzip the archive or clone repository version to any  directory on
your PC and you are ready to go.

## Unit testing

cm_phplib comes with unit tests. To run those, you'll need to have an installed version of `PHPUnit`. Then go to
the `tests/` directory and run

    phpunit %test_file_name%

## Examples

Sample code is given in the `examples/` directory. To run those, go to `examples/`
and run

    php %example_file_name%

For this option you'll need to have `php-cli` package *(Debian systems)* installed, but it's
recommended for you to run those examples on your server and browse the results in your
browser.

## Further reading

For more info read documentation in the `doc/` directory. And do not forget to check out the `examples/`.
