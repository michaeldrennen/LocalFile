<?php
namespace MichaelDrennen\LocalFile\Tests;

use PHPUnit\Framework\TestCase;
use MichaelDrennen\LocalFile\LocalFile;

class LocalFileTest extends TestCase {

    private $pathToUnwriteableDirectory = './tests/testFiles/notWriteableDirectory';

    public function setUp() {
        // Make unwriteable directory read only.
        chmod( $this->pathToUnwriteableDirectory, 0444 );
    }

    public function testNumLines() {
        $pathToFile       = 'beeMovieScript.txt';
        $lineCount        = LocalFile::lineCount( $pathToFile );
        $expectedNumLines = 4564;
        $this->assertEquals( $expectedNumLines, $lineCount );
    }

    public function testNumLinesOnMissingFileShouldThrowException() {
        $this->expectException( \Exception::class );
        $pathToFile = 'thisFileDoesNotExist.txt';
        LocalFile::lineCount( $pathToFile );
    }


}