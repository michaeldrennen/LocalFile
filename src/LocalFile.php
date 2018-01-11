<?php
namespace MichaelDrennen\LocalFile;

use MichaelDrennen\LocalFile\Exceptions\UnableToOpenFile;

class LocalFile {

    /**
     * A php function that returns the number of lines in a file on the local file system.
     *
     * @param string $filePath
     *
     * @return int
     * @throws \MichaelDrennen\LocalFile\Exceptions\UnableToOpenFile
     */
    public static function lineCount( string $filePath ): int {

        $lineCount = 0;
        $handle    = @fopen( $filePath, "r" );
        if ( $handle === false ) {
            throw new UnableToOpenFile( "Unable to open the file at: " . $filePath );
        }
        while ( ! feof( $handle ) ) {
            fgets( $handle );
            $lineCount++;
        }

        fclose( $handle );
        return $lineCount;
    }


    public static function split( string $pathToSourceFile, $linesPerFile = 1000, string $prefix = 'split_', string $destinationPath = null ) {
        // expands all symbolic links and resolves references to '/./', '/../' and extra '/' characters
        $pathToSourceFile = realpath( $pathToSourceFile );

        if ( false === file_exists( $pathToSourceFile ) ):
            throw new \Exception( "Can't split [" . $pathToSourceFile . "] because it doesn't exist." );
        endif;

        $sourceDirectory = dirname( $pathToSourceFile );

        var_dump( $sourceDirectory );

        if ( is_null( $destinationPath ) ):
            $destinationPath = $sourceDirectory;
        endif;

        if ( false === is_writeable( $destinationPath ) ):
            throw new \Exception( "Can't split [" . $pathToSourceFile . "] because the destination path at [" . $destinationPath . "] is not writeable." );
        endif;

        var_dump( $destinationPath );


    }
}