<?php

namespace App\Services;

use PhpParser\Node\Stmt\TryCatch;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class FileService{
    //PROPIEDADES
    public $targetDirectory;

    public function __construct( string $targetDirectory ){
        $this->targetDirectory = $targetDirectory;
    }

    public function upload ( UploadedFile $file ) : ?String {

        $extension = $file->guessExtension();
        $fichero = uniqid().".$extension";

        try{
            $file->move($this->targetDirectory, $fichero);
        }catch (FileException $e ){
            return NULL;
        }
        return $fichero;
    }

    public function remove ( string $imagen ) {

        $filesystem = new Filesystem();
        return $filesystem->remove($this->targetDirectory.'/'.$imagen);

    }

    public function replace ( UploadedFile $file, String|NULL $imagenAntigua ): ?String {

        $extension = $file->guessExtension();
        $fichero = uniqid().".$extension";

        try{
            $file->move($this->targetDirectory, $fichero);

            if( $imagenAntigua ){
                $filesystem = new Filesystem();
                $filesystem->remove( $this->targetDirectory.'/'.$imagenAntigua);
            }
        }catch( FileException $e){
            return $imagenAntigua;
        }

        return $fichero;

    }

}