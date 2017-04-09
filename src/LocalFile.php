<?php
namespace MichaelDrennen\LocalFile;
use Exception;
class LocalFile{

    /**
     * A php function that returns the number of lines in a file on the local file system.
     * @param string $filePath
     * @return int
     * @throws Exception
     */
    public static function lineCount(string $filePath): int{
        return count(file($filePath));
    }
}
