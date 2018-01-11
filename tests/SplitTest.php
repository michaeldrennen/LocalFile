<?php

use PHPUnit\Framework\TestCase;
use MichaelDrennen\LocalFile\LocalFile;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;

class SplitTest extends TestCase {

    /**
     * @var  vfsStreamDirectory $root
     */
    private $root;

    /**
     * set up test environmemt
     */
    public function setUp() {
        $this->root = vfsStream::setup( 'testDir' );

    }



    //public function testSplitWithNonExistentFileShouldThrowException(){
    //    $this->expectException( Exception::class );
    //    LocalFile::split('./tests/thisFileDoesNotExist.txt');
    //}


    //public function testSplitShouldMakeFiveFilesInSameDirectory(){
    //
    //    LocalFile::split('./tests/phpUnitTestFile.txt');
    //}
}