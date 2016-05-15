# spawn

[![Software License][ico-license]](LICENSE)

Recently I've worked on several projects that call micro-processes for simple tasks (sending an email, uploading a file, sending JSON data to a webservice) many times with sensibly different arguments. Spawn is a command-line utility that makes it easy and displays the progress with a progress bar like so:

```bash
Starting processes
 3/3 [============================] 100% (tests/test.php "tests/data/3.txt")  1 sec/1 sec  1.5 MiB
```



**This tool is in its very early stages. Bugs are expected to happen.**

**Spawn is not intended to be launched from a remote server, only from your local command line.**


## Install


``` bash
git clone https://github.com/bouiboui/spawn.git
composer install
```

## Usage

``` bash
# Run a single process
php spawn.php run "tests/test.php"

 # Run a single process with "arg1" as argument
php spawn.php run "tests/test.php" --args "arg1"

 # Run a process for a range of arguments
php spawn.php run "tests/test.php" --args "arg{1-9}"

 # Run a process for each file in a directory as an argument
php spawn.php run "tests/test.php" --dir "tests/data"
```

## Credits


- bouiboui — [Github](https://github.com/bouiboui) [Twitter](https://twitter.com/j_____________n) [Website](http://cod3.net)
- [All contributors](https://github.com/bouiboui/spawn/graphs/contributors)


## License

Unlicense. Please see [License File](LICENSE) for more information.

[ico-version]: https://img.shields.io/packagist/v/:vendor/:package_name.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-Unlicense-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/:vendor/:package_name/master.svg?style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/coverage/g/:vendor/:package_name.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/:vendor/:package_name.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/:vendor/:package_name.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/:vendor/:package_name
[link-travis]: https://travis-ci.org/:vendor/:package_name
[link-scrutinizer]: https://scrutinizer-ci.com/g/:vendor/:package_name/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/:vendor/:package_name
[link-downloads]: https://packagist.org/packages/:vendor/:package_name
[link-author]: https://github.com/:author_username
[link-contributors]: ../../contributors