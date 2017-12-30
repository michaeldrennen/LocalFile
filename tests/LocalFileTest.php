<?php

use PHPUnit\Framework\TestCase;
use MichaelDrennen\LocalFile\LocalFile;

class LocalFileTest extends TestCase {

    public function testNumLines() {
        $pathToFile       = 'beeMovieScript.txt';
        $lineCount        = LocalFile::lineCount( $pathToFile );
        $expectedNumLines = 4564;
        $this->assertEquals( $expectedNumLines, $lineCount );
    }

    public function testNumLinesOnMissingFileShouldThrowException() {
        $this->expectException( Exception::class );
        $pathToFile = 'thisFileDoesNotExist.txt';
        LocalFile::lineCount( $pathToFile );
    }
}