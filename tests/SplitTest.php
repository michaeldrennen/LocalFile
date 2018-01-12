<?php
namespace MichaelDrennen\LocalFile\Tests;

use MichaelDrennen\LocalFile\Exceptions\CantWriteToReadOnlyDirectory;
use MichaelDrennen\LocalFile\Exceptions\SourceFileDoesNotExist;
use org\bovigo\vfs\vfsStreamDirectory;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;
use MichaelDrennen\LocalFile\LocalFile;

class SplitTest extends TestCase {

    const VFS_ROOT_DIR               = 'vfsRootDir';
    const WRITEABLE_DIR_NAME         = 'writeableDir';
    const ANOTHER_WRITEABLE_DIR_NAME = 'anotherWriteableDir';
    const READ_ONLY_DIR_NAME         = 'readOnlyDirName';

    const SOURCE_FILE_NAME          = 'phpUnitTestFile.txt';
    const PATH_TO_SOURCE_FILE       = './tests/testFiles/' . self::SOURCE_FILE_NAME;
    const PATH_TO_NON_EXISTENT_FILE = './tests/testFiles/thisFileDoesNotExist.txt';

    /**
     * @var  vfsStreamDirectory $vfsRootDirObject
     */
    protected static $vfsRootDirObject;

    /**
     * @var  vfsStreamDirectory $writeableDirectory
     */
    protected static $writeableDirectory;

    /**
     * @var vfsStreamDirectory $anotherWriteableDirectory
     */
    protected static $anotherWriteableDirectory;

    /**
     * @var  vfsStreamDirectory $readOnlyDirectory
     */
    protected static $readOnlyDirectory;


    /**
     * Set up test environment
     */
    public static function setUpBeforeClass() {
        self::$vfsRootDirObject   = vfsStream::setup( self::VFS_ROOT_DIR );
        self::$writeableDirectory = vfsStream::url( self::VFS_ROOT_DIR ) . DIRECTORY_SEPARATOR . self::WRITEABLE_DIR_NAME;
        mkdir( self::$writeableDirectory, 0777 );
        self::$anotherWriteableDirectory = vfsStream::url( self::VFS_ROOT_DIR ) . DIRECTORY_SEPARATOR . self::ANOTHER_WRITEABLE_DIR_NAME;
        mkdir( self::$anotherWriteableDirectory, 0777 );
        self::$readOnlyDirectory = vfsStream::url( self::VFS_ROOT_DIR ) . DIRECTORY_SEPARATOR . self::READ_ONLY_DIR_NAME;
        mkdir( self::$readOnlyDirectory, 0444 );
    }


    public function testSplitWithNonExistentFileShouldThrowException() {
        $this->expectException( SourceFileDoesNotExist::class );
        LocalFile::split( self::PATH_TO_NON_EXISTENT_FILE );
    }

    public function testSplitIntoReadOnlyDirectoryShouldThrowException() {
        $this->expectException( CantWriteToReadOnlyDirectory::class );
        LocalFile::split( self::PATH_TO_SOURCE_FILE, 1, 'split_', self::$readOnlyDirectory );
    }


    public function testSplitShouldMakeFiveFilesInSameDirectory() {
        $copied = copy( self::PATH_TO_SOURCE_FILE, self::$writeableDirectory . DIRECTORY_SEPARATOR . self::SOURCE_FILE_NAME );

        var_dump( $copied );
        var_dump( file( $copied ) );

        $absolutePathToSourceFile = self::$writeableDirectory . DIRECTORY_SEPARATOR . self::SOURCE_FILE_NAME;
        var_dump( $absolutePathToSourceFile );
        LocalFile::split( self::$writeableDirectory . self::SOURCE_FILE_NAME, 1 );

        $files = scandir( self::$writeableDirectory );
        $this->assertCount( 7, $files ); // includes . and ..
    }
}