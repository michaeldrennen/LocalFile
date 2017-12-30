# LocalFile
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