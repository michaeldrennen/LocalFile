<?php

namespace MichaelDrennen\LocalFile\Tests;

use MichaelDrennen\LocalFile\Exceptions\CantWriteToReadOnlyDirectory;
use MichaelDrennen\LocalFile\Exceptions\SourceFileDoesNotExist;
use MichaelDrennen\LocalFile\Exceptions\UnableToReadFile;
use MichaelDrennen\LocalFile\Exceptions\UnableToWriteLineToSplitFile;
use org\bovigo\vfs\vfsStreamDirectory;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;
use MichaelDrennen\LocalFile\LocalFile;

class SplitTest extends TestCase
{

    const VFS_ROOT_DIR = 'vfsRootDir';
    const WRITEABLE_DIR_NAME = 'writeableDir';
    const ANOTHER_WRITEABLE_DIR_NAME = 'anotherWriteableDir';
    const READ_ONLY_DIR_NAME = 'readOnlyDirName';
    //const SOURCE_FILE_NAME           = 'phpUnitTestFile.txt';
    const SOURCE_FILE_NAME = 'YU.txt';
    const PATH_TO_SOURCE_FILE = './tests/testFiles/' . self::SOURCE_FILE_NAME;
    const PATH_TO_NON_EXISTENT_FILE = './tests/testFiles/thisFileDoesNotExist.txt';

    /**
     * @var  vfsStreamDirectory $vfsRootDirObject The root VFS object created in the setUp() method.
     */
    protected static $vfsRootDirObject;

    /**
     * @var  vfsStreamDirectory $writeableDirectory The path to a writeable directory on the VFS.
     */
    protected static $writeableDirectory;

    /**
     * @var vfsStreamDirectory $anotherWriteableDirectory A second path to a writeable directory on the VFS.
     */
    protected static $anotherWriteableDirectory;

    /**
     * @var  vfsStreamDirectory $readOnlyDirectory The path on the VFS to a directory that you can't write to.
     */
    protected static $readOnlyDirectory;

    /**
     * @var string $readableSourceFilePath The path on the VFS to a source file that is readable.
     */
    protected static $readableSourceFilePath;

    /**
     * @var string $unreadableSourceFilePath The path on the VFS to a source file that is unreadable.
     */
    protected static $unreadableSourceFilePath;


    /**
     * Set up test environment
     */
    public function setUp()
    {
        self::$vfsRootDirObject = vfsStream::setup(self::VFS_ROOT_DIR);
        self::$writeableDirectory = vfsStream::url(self::VFS_ROOT_DIR . DIRECTORY_SEPARATOR . self::WRITEABLE_DIR_NAME);
        mkdir(self::$writeableDirectory, 0777);
        self::$anotherWriteableDirectory = vfsStream::url(self::VFS_ROOT_DIR . DIRECTORY_SEPARATOR . self::ANOTHER_WRITEABLE_DIR_NAME);
        mkdir(self::$anotherWriteableDirectory, 0777);
        self::$readOnlyDirectory = vfsStream::url(self::VFS_ROOT_DIR . DIRECTORY_SEPARATOR . self::READ_ONLY_DIR_NAME);
        mkdir(self::$readOnlyDirectory, 0444);

        self::$readableSourceFilePath = vfsStream::url(self::VFS_ROOT_DIR . DIRECTORY_SEPARATOR . self::WRITEABLE_DIR_NAME . DIRECTORY_SEPARATOR . self::SOURCE_FILE_NAME);
        file_put_contents(self::$readableSourceFilePath, file_get_contents(self::PATH_TO_SOURCE_FILE));

        self::$unreadableSourceFilePath = vfsStream::url(self::VFS_ROOT_DIR . DIRECTORY_SEPARATOR . self::WRITEABLE_DIR_NAME . DIRECTORY_SEPARATOR . "unreadable_" . self::SOURCE_FILE_NAME);
        file_put_contents(self::$unreadableSourceFilePath, file_get_contents(self::PATH_TO_SOURCE_FILE));
        chmod(self::$unreadableSourceFilePath, '0100');
    }


    public function testSplitWithNonExistentFileShouldThrowException()
    {
        $this->expectException(SourceFileDoesNotExist::class);
        LocalFile::split(self::PATH_TO_NON_EXISTENT_FILE);
    }

    public function testSplitWithUnreadableFileShouldThrowException()
    {
        $this->expectException(UnableToReadFile::class);
        LocalFile::split(self::$unreadableSourceFilePath);
    }

    public function testSplitIntoReadOnlyDirectoryShouldThrowException()
    {
        $this->expectException(CantWriteToReadOnlyDirectory::class);
        LocalFile::split(self::PATH_TO_SOURCE_FILE, 1, 'split_', self::$readOnlyDirectory);
    }

    /**
     * @group quota
     */
    public function testSplitIntoFullDiskShouldThrowException()
    {
        $this->expectException(UnableToWriteLineToSplitFile::class);
        // Gives me enough room to write the source file.
        $bytesInSourceFile = filesize(self::PATH_TO_SOURCE_FILE);

        $virtualSourceFilePath = vfsStream::url(self::VFS_ROOT_DIR . DIRECTORY_SEPARATOR . self::WRITEABLE_DIR_NAME . DIRECTORY_SEPARATOR . self::SOURCE_FILE_NAME);
        @file_put_contents($virtualSourceFilePath, file_get_contents(self::PATH_TO_SOURCE_FILE));
        vfsStream::setQuota($bytesInSourceFile - 1);
        LocalFile::split($virtualSourceFilePath, 1, null, self::$anotherWriteableDirectory);
        // Stop here and mark this test as incomplete.
        $this->markTestIncomplete(
            "Incomplete test. Need to look into why this isn't throwing an exception when I set the quota to 18 bytes."
        );
    }


    public function testSplitShouldMakeFiveFilesInSameDirectory()
    {
        LocalFile::split(self::$readableSourceFilePath, 4);
        $files = scandir(self::$writeableDirectory);
        $this->assertCount(9, $files); // includes . and ..
    }

    /**
     * @throws \MichaelDrennen\LocalFile\Exceptions\CantWriteToReadOnlyDirectory
     * @throws \MichaelDrennen\LocalFile\Exceptions\SourceFileDoesNotExist
     * @throws \MichaelDrennen\LocalFile\Exceptions\UnableToOpenSplitFileHandle
     * @throws \MichaelDrennen\LocalFile\Exceptions\UnableToReadFile
     * @throws \MichaelDrennen\LocalFile\Exceptions\UnableToWriteLineToSplitFile
     * @group mike
     */
    public function testSplitShouldMakeFiveFilesInAnotherDirectory()
    {
        $expectedFinalFileCount = 5;
        $linesPerFile = 4;
        $virtualSourceFilePath = vfsStream::url(self::VFS_ROOT_DIR . DIRECTORY_SEPARATOR .
            self::WRITEABLE_DIR_NAME . DIRECTORY_SEPARATOR .
            self::SOURCE_FILE_NAME);
        file_put_contents($virtualSourceFilePath, file_get_contents(self::PATH_TO_SOURCE_FILE));

        LocalFile::split($virtualSourceFilePath, $linesPerFile, null, self::$anotherWriteableDirectory);

        $files = scandir(self::$anotherWriteableDirectory);
        array_shift($files); // Remove .
        array_shift($files); // Remove ..

        $this->assertCount($expectedFinalFileCount, $files); // includes . and ..
    }
}