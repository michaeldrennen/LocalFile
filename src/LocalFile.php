<?php
namespace MichaelDrennen\LocalFile;

use MichaelDrennen\LocalFile\Exceptions\CantWriteToReadOnlyDirectory;
use MichaelDrennen\LocalFile\Exceptions\SourceFileDoesNotExist;
use MichaelDrennen\LocalFile\Exceptions\UnableToOpenFile;
use MichaelDrennen\LocalFile\Exceptions\UnableToOpenSplitFileHandle;
use MichaelDrennen\LocalFile\Exceptions\UnableToReadFile;
use MichaelDrennen\LocalFile\Exceptions\UnableToWriteLineToSplitFile;

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


    /**
     * @param string      $pathToSourceFile
     * @param int         $linesPerFile
     * @param string      $prefix
     * @param string|null $destinationPath
     *
     * @returns boolean
     * @throws \MichaelDrennen\LocalFile\Exceptions\CantWriteToReadOnlyDirectory
     * @throws \MichaelDrennen\LocalFile\Exceptions\SourceFileDoesNotExist
     * @throws \MichaelDrennen\LocalFile\Exceptions\UnableToReadFile
     * @throws \MichaelDrennen\LocalFile\Exceptions\UnableToOpenSplitFileHandle
     * @throws \MichaelDrennen\LocalFile\Exceptions\UnableToWriteLineToSplitFile
     */
    public static function split( string $pathToSourceFile, $linesPerFile = 1000, string $prefix = 'split_', string $destinationPath = null ): bool {

        if ( false === file_exists( $pathToSourceFile ) ):
            throw new SourceFileDoesNotExist( "Can't split [" . $pathToSourceFile . "] because it doesn't exist." );
        endif;


        $sourceFileParts     = pathinfo( $pathToSourceFile );
        $sourceDirectory     = $sourceFileParts[ 'dirname' ];
        $sourceFileName      = $sourceFileParts[ 'filename' ];
        $sourceFileExtension = $sourceFileParts[ 'extension' ];

        //var_dump( $sourceDirectory );

        if ( is_null( $destinationPath ) ):
            $destinationPath = $sourceDirectory;
        endif;

        if ( false === is_writeable( $destinationPath ) ):
            throw new CantWriteToReadOnlyDirectory( "Can't split [" . $pathToSourceFile . "] because the destination path at [" . $destinationPath . "] is not writeable." );
        endif;

        //var_dump( $destinationPath );

        $sourceHandle = fopen( $pathToSourceFile, "r" );

        if ( false === $sourceHandle ):
            throw new UnableToReadFile( "Unable to read the source file at [" . $pathToSourceFile . "]" );
        endif;


        $totalLineCount        = 0;
        $currentChunkLineCount = 0;
        $totalChunkCount       = 0;

        while ( ( $line = fgets( $sourceHandle ) ) !== false ):
            // process the line read.
            if ( 0 === $currentChunkLineCount ):
                // Create new split file.
                $totalChunkCount++;
                $suffix             = $totalChunkCount;
                $newSplitFileName   = $prefix . $sourceFileName . '_' . $suffix . '.' . $sourceFileExtension;
                $newSplitFileHandle = fopen( $destinationPath . DIRECTORY_SEPARATOR . $newSplitFileName, 'w' );
            endif;

            if ( isset( $newSplitFileHandle ) && false !== $newSplitFileHandle ):
                $bytesWritten = fwrite( $newSplitFileHandle, trim( $line ) );

                if ( false === $bytesWritten ):
                    throw new UnableToWriteLineToSplitFile( "Unable to write line #" . ( $totalLineCount + 1 ) . " from " . $pathToSourceFile . " to split file named " . $newSplitFileName );
                endif;

                $totalLineCount++;
                $currentChunkLineCount++;

                if ( $currentChunkLineCount >= $linesPerFile ):
                    @fclose( $newSplitFileHandle );
                    $currentChunkLineCount = 0;
                endif;
            else:
                throw new UnableToOpenSplitFileHandle( "Unable to open the file handle to a new split file named [" . $newSplitFileName . "]" );
            endif;
        endwhile;

        if ( isset( $newSplitFileHandle ) ):
            @fclose( $newSplitFileHandle ); // Just in case.
        endif;


        @fclose( $sourceHandle );

        return true;
    }
}