<?php

use PHPUnit\Framework\TestCase;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;

class VfsStreamTest extends TestCase {

    const VFS_ROOT_DIR       = 'vfsRootDir';
    const WRITEABLE_DIR_NAME = 'writeableDir';
    const READ_ONLY_DIR_NAME = 'readOnlyDirName';

    /**
     * @var  vfsStreamDirectory $vfsRootDirObject
     */
    protected $vfsRootDirObject;

    /**
     * @var  vfsStreamDirectory $writeableDirectory
     */
    protected $writeableDirectory;

    /**
     * @var  vfsStreamDirectory $readOnlyDirectory
     */
    protected $readOnlyDirectory;


    /**
     * Set up test environment
     */
    public function setUp() {
        $this->vfsRootDirObject   = vfsStream::setup( self::VFS_ROOT_DIR );
        $this->writeableDirectory = vfsStream::url( self::VFS_ROOT_DIR ) . DIRECTORY_SEPARATOR . self::WRITEABLE_DIR_NAME;
        mkdir( $this->writeableDirectory, 0777 );
        $this->readOnlyDirectory = vfsStream::url( self::VFS_ROOT_DIR ) . DIRECTORY_SEPARATOR . self::READ_ONLY_DIR_NAME;
        mkdir( $this->readOnlyDirectory, 0444 );
    }

    public function testDirectoriesExistWithCorrectPermissions() {
        $this->assertTrue( file_exists( vfsStream::url( self::VFS_ROOT_DIR ) ) );
        $this->assertTrue( file_exists( $this->writeableDirectory ) );
        $this->assertTrue( is_writable( $this->writeableDirectory ) );

        $this->assertTrue( file_exists( $this->readOnlyDirectory ) );
        $this->assertTrue( is_readable( $this->readOnlyDirectory ) );
        $this->assertFalse( is_writable( $this->readOnlyDirectory ) );
    }


}