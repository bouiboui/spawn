# spawn

[![Software License][ico-license]](LICENSE)

Recently I've worked on several projects that call micro-processes for simple tasks (sending JSON data to a webservice, uploading a file, sending an email) in bulk, with sensibly different arguments. Spawn is a command-line utility that makes these calls and displays the progress with a progress bar like so:

```bash
Starting processes
 0/3 [>---------------------------]   0% (php send_updates.php "data/client1.json") < 1 sec/< 1 sec 1.0 MiB
 1/3 [=========>------------------]  33% (php send_updates.php "data/client1.json") 4 secs/12 secs 1.2 MiB
 2/3 [==================>---------]  66% (php send_updates.php "data/client2.json") 9 secs/14 secs 1.2 MiB
 3/3 [============================] 100% (php send_updates.php "data/client3.json") 14 secs/14 secs 1.2 MiB
```

**Warning: This tool is in its very early stages. Bugs are expected to happen.**

**Spawn is not intended to be launched from a remote server, only from your local command line.**


## Install

``` bash
git clone https://github.com/bouiboui/spawn.git
composer install
```

Spawn will be compiled to a .phar in the future.

## Usage

Run a single process
``` bash
php spawn.php run "php send_bulk_data.php"

Starting processes
 1/1 [============================] 100% (php send_bulk_data.php)  1 min/1 min  1.2 MiB
```
Pass arguments
``` bash
php spawn.php run "php send_latest_invoices.php" --args "startdate=2016-01-01"

Starting processes
 1/1 [============================] 100% (php send_latest_invoices.php "startdate=2016-01-01") 13 secs/13 secs 1.2 MiB
```
Add a range in the arguments
``` bash
php spawn.php run "php convert_pdfs.php" --args "document{1-42}.pdf"

Starting processes
  0/42 [>---------------------------]   0% (php convert_pdfs.php "document1.pdf") < 1 sec/< 1 sec 1.0 MiB
  4/42 [==>-------------------------]   9% (php convert_pdfs.php "document4.pdf") < 1 sec/< 1 sec 1.2 MiB
  8/42 [=====>----------------------]  19% (php convert_pdfs.php "document8.pdf")  1 sec/5 secs 1.2 MiB
 12/42 [========>-------------------]  28% (php convert_pdfs.php "document12.pdf") 2 secs/7 secs 1.2 MiB
 16/42 [==========>-----------------]  38% (php convert_pdfs.php "document16.pdf") 3 secs/8 secs 1.2 MiB
 20/42 [=============>--------------]  47% (php convert_pdfs.php "document20.pdf") 4 secs/8 secs 1.2 MiB
 24/42 [================>-----------]  57% (php convert_pdfs.php "document24.pdf") 5 secs/9 secs 1.2 MiB
 28/42 [==================>---------]  66% (php convert_pdfs.php "document28.pdf") 6 secs/9 secs 1.2 MiB
 32/42 [=====================>------]  76% (php convert_pdfs.php "document32.pdf") 6 secs/8 secs 1.2 MiB
 36/42 [========================>---]  85% (php convert_pdfs.php "document36.pdf") 7 secs/8 secs 1.2 MiB
 40/42 [==========================>-]  95% (php convert_pdfs.php "document40.pdf") 8 secs/8 secs 1.2 MiB
 42/42 [============================] 100% (php convert_pdfs.php "document42.pdf") 9 secs/9 secs 1.2 MiB

```
Run process for each file in a directory
``` bash
php spawn.php run "php get_gravatar.php" --dir "data/users"

Starting processes
 0/3 [>---------------------------]   0% (php get_gravatar.php "data/users/martin-fowler.json") < 1 sec/< 1 sec 1.0 MiB
 1/3 [=========>------------------]  33% (php get_gravatar.php "data/users/martin-fowler.json") 4 secs/12 secs 1.2 MiB
 2/3 [==================>---------]  66% (php get_gravatar.php "data/users/rasmus-lerdorf.json") 5 secs/8 secs 1.2 MiB
 3/3 [============================] 100% (php get_gravatar.php "data/users/aaron-saray.json") 9 secs/9 secs 1.2 MiB
```
Save output to a file
``` bash
php spawn.php run "php get_twitter_handle.php" --dir "data/users" --outfile=handles.txt

# handles.txt
$ php get_twitter_handle.php "data/users/martin-fowler.json"
@martinfowler

$ php get_twitter_handle.php "data/users/rasmus-lerdorf.json"
@rasmus

$ php get_twitter_handle.php "data/users/aaron-saray.json"
@aaronsaray
```


## Credits


- bouiboui — [Github](https://github.com/bouiboui) [Twitter](https://twitter.com/j_____________n) [Website](http://cod3.net)
- [All contributors](https://github.com/bouiboui/spawn/graphs/contributors)


## License

Unlicense. Please see [License File](LICENSE) for more information.

[ico-version]: https://img.shields.io/packagist/v/:vendor/:package_name.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-Unlicense-brightgreen.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/:vendor/:package_name
[link-author]: https://github.com/:author_username