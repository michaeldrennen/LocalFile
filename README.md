# LocalFile v2.x
[![Latest Stable Version](https://poser.pugx.org/michaeldrennen/local-file/version)](https://packagist.org/packages/michaeldrennen/local-file) [![Total Downloads](https://poser.pugx.org/michaeldrennen/local-file/downloads)](https://packagist.org/packages/michaeldrennen/local-file) [![License](https://poser.pugx.org/michaeldrennen/local-file/license)](https://packagist.org/packages/michaeldrennen/local-file) [![Build Status](https://travis-ci.org/michaeldrennen/LocalFile.svg?branch=v1.0.2)](https://travis-ci.org/michaeldrennen/LocalFile) [![Coverage Status](https://coveralls.io/repos/github/michaeldrennen/LocalFile/badge.svg?branch=master)](https://coveralls.io/github/michaeldrennen/LocalFile?branch=master)
	
A small php library that handles some common functions needed for local files.


And then run `composer install` from the terminal.

### Quick Installation

Use the following command in your terminal:

    composer require "michaeldrennen/local-file"
    
### Usage
    
#### Get the number of lines in a file as an integer
```php
use MichaelDrennen\LocalFile\LocalFile;

$lineCount = LocalFile::lineCount('/path/to/your/file.txt);
echo $lineCount;
```


#### Split a text file into chunks. Each chunk has 1000 lines.
```php
use MichaelDrennen\LocalFile\LocalFile;

$splitFilePaths = LocalFile::split('/path/to/your/file.txt);
print_r($splitFilePaths);
```