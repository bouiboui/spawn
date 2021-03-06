![Spawn logo](http://i.imgur.com/GCFZHRe.png)

[![Software License][ico-license]](LICENSE) [![SensioLabsInsight](https://insight.sensiolabs.com/projects/20fe60c6-7257-442e-bff4-9c057580afd6/mini.png)](https://insight.sensiolabs.com/projects/20fe60c6-7257-442e-bff4-9c057580afd6)

Recently I've worked on several projects that call micro-processes for simple tasks (sending JSON data to a webservice, uploading a file, sending an email) in bulk, with sensibly different arguments. Spawn is a command-line utility that makes these calls and displays the progress with a progress bar like so:

```bash
[Spawn] Starting 3 process(es)
 1/3 [=========>------------------]  33% ('php' 'send_updates.php' 'data/client1.json') 4 secs/12 secs 1.2 MiB
 2/3 [==================>---------]  66% ('php' 'send_updates.php' 'data/client2.json') 9 secs/14 secs 1.2 MiB
 3/3 [============================] 100% ('php' 'send_updates.php' 'data/client3.json') 14 secs/14 secs 1.2 MiB
```

**Spawn is not intended to be launched from a remote server, only from your local command line.**


## Install

**Windows**

[Download spawn.phar from the latest release](https://github.com/bouiboui/spawn/releases/download/0.3/spawn.phar)

**Linux/MacOS**

Composer
```bash
composer global require bouiboui/spawn
sudo ln -s ~/.composer/vendor/bin/spawn /usr/local/bin
```

Manual
```bash
wget https://github.com/bouiboui/spawn/releases/download/0.3/spawn.phar
chmod +x spawn.phar
sudo mv spawn.phar /usr/local/bin/spawn
```




## Usage

Run a process with arguments separated by spaces
``` bash
./spawn.phar php send_latest_invoices.php startdate=2016-01-01

[Spawn] Starting 1 process(es)
 1/1 [============================] 100% ('php' 'send_latest_invoices.php' 'startdate=2016-01-01') 13 secs/13 secs 1.2 MiB
```
Add a numerical range in the arguments
``` bash
./spawn.phar php convert_pdfs.php document{1-42}.pdf

[Spawn] Starting 42 process(es)
  4/42 [==>-------------------------]   9% ('php' 'convert_pdfs.php' 'document4.pdf') < 1 sec/< 1 sec 1.2 MiB
  8/42 [=====>----------------------]  19% ('php' 'convert_pdfs.php' 'document8.pdf')  1 sec/5 secs 1.2 MiB
 12/42 [========>-------------------]  28% ('php' 'convert_pdfs.php' 'document12.pdf') 2 secs/7 secs 1.2 MiB
 16/42 [==========>-----------------]  38% ('php' 'convert_pdfs.php' 'document16.pdf') 3 secs/8 secs 1.2 MiB
 20/42 [=============>--------------]  47% ('php' 'convert_pdfs.php' 'document20.pdf') 4 secs/8 secs 1.2 MiB
 24/42 [================>-----------]  57% ('php' 'convert_pdfs.php' 'document24.pdf') 5 secs/9 secs 1.2 MiB
 28/42 [==================>---------]  66% ('php' 'convert_pdfs.php' 'document28.pdf') 6 secs/9 secs 1.2 MiB
 32/42 [=====================>------]  76% ('php' 'convert_pdfs.php' 'document32.pdf') 6 secs/8 secs 1.2 MiB
 36/42 [========================>---]  85% ('php' 'convert_pdfs.php' 'document36.pdf') 7 secs/8 secs 1.2 MiB
 40/42 [==========================>-]  95% ('php' 'convert_pdfs.php' 'document40.pdf') 8 secs/8 secs 1.2 MiB
 42/42 [============================] 100% ('php' 'convert_pdfs.php' 'document42.pdf') 9 secs/9 secs 1.2 MiB

```
Make GET calls to an URL with a range
```bash
./spawn.phar curl http://webservice/api/v1/customers/{1-3}/

[Spawn] Starting 3 process(es)
 1/3 [=========>------------------]  33% ('curl' 'http://webservice/api/v1/customers/1/') 4 secs/12 secs 1.2 MiB
 2/3 [==================>---------]  66% ('curl' 'http://webservice/api/v1/customers/2/') 5 secs/8 secs 1.2 MiB
 3/3 [============================] 100% ('curl' 'http://webservice/api/v1/customers/3/') 9 secs/9 secs 1.2 MiB
```
POST JSON files from a directory to an URL

* Important: use `--` or Spawn will try to parse curl options
* `-d @` before the filename sends the contents of the file
```bash
./spawn.phar -- curl -XPOST -H "Content-type: application/json" -d @json/invoices http://webservice/api/v1/invoices/

[Spawn] Starting 3 process(es)
 1/3 [=========>------------------]  33% ("curl" "-XPOST" "-H" "Content-type: application/json" "-d" "@json/invoices/january-april.json" "http://webservice/api/v1/invoices/")  1 sec/3 secs 1.2 MiB
 2/3 [==================>---------]  66% ("curl" "-XPOST" "-H" "Content-type: application/json" "-d" "@json/invoices/may-august.json" "http://webservice/api/v1/invoices/")  1 sec/2 secs 1.5 MiB
 3/3 [============================] 100% ("curl" "-XPOST" "-H" "Content-type: application/json" "-d" "@json/invoices/september-december.json" "http://webservice/api/v1/invoices/") 2 secs/2 secs 1.5 MiB
```
Run process for each file in a directory, filtered by the .json extension
``` bash
./spawn.phar php get_gravatar.php data/users --find=*.json

[Spawn] Starting 3 process(es)
 1/3 [=========>------------------]  33% ('php' 'get_gravatar.php' 'data/users/martin-fowler.json') 4 secs/12 secs 1.2 MiB
 2/3 [==================>---------]  66% ('php' 'get_gravatar.php' 'data/users/rasmus-lerdorf.json') 5 secs/8 secs 1.2 MiB
 3/3 [============================] 100% ('php' 'get_gravatar.php' 'data/users/aaron-saray.json') 9 secs/9 secs 1.2 MiB
```
Save output to a file
``` bash
./spawn.phar php get_twitter_handle.php data/users --outfile=handles.txt

# handles.txt
$ php get_twitter_handle.php "data/users/martin-fowler.json"
@martinfowler

$ php get_twitter_handle.php "data/users/rasmus-lerdorf.json"
@rasmus

$ php get_twitter_handle.php "data/users/aaron-saray.json"
@aaronsaray
```
Verbose mode
```bash
./spawn.phar php tests/test.php tests/data -vvv

[Spawn] Starting 3 process(es)
  RUN  'php' 'tests/test.php' 'tests/data/1.txt'
  OUT  Contents from 1.txt
  RES  Command ran successfully
 1/3 [=========>------------------]  33% ('php' 'tests/test.php' 'tests/data/1.txt') < 1 sec/< 1 sec 2.5 MiB  
  RUN  'php' 'tests/test.php' 'tests/data/2.txt'
  OUT  Contents from 2.txt
  RES  Command ran successfully
 2/3 [==================>---------]  66% ('php' 'tests/test.php' 'tests/data/2.txt') 2 secs/3 secs 2.5 MiB  
  RUN  'php' 'tests/test.php' 'tests/data/3.txt'
  OUT  Contents from 3.txt
  RES  Command ran successfully
 3/3 [============================] 100% ('php' 'tests/test.php' 'tests/data/3.txt') 3 secs/3 secs 2.5 MiB%   
```

## Credits

- bouiboui — [Github](https://github.com/bouiboui) [Twitter](https://twitter.com/j_____________n) [Website](http://cod3.net)
- [All contributors](https://github.com/bouiboui/spawn/graphs/contributors)


## License

Unlicense. Public domain, basically. Please treat it kindly. See [License File](LICENSE) for more information. 

This project uses the following open source projects 
- [symfony/process](https://github.com/symfony/process) by [Fabien Potencier](https://github.com/fabpot) — [License](https://github.com/symfony/process/blob/master/LICENSE).
- [symfony/console](https://github.com/symfony/console) by [Fabien Potencier](https://github.com/fabpot) — [License](https://github.com/symfony/console/blob/master/LICENSE).
- [phpunit/phpunit](https://github.com/sebastianbergmann/phpunit) by [Sebastian Bergmann](https://github.com/sebastianbergmann) — [License](https://github.com/sebastianbergmann/phpunit/blob/master/LICENSE).



[ico-version]: https://img.shields.io/packagist/v/:vendor/:package_name.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-Unlicense-brightgreen.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/:vendor/:package_name
[link-author]: https://github.com/:author_username
