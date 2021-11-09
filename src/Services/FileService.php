<?php

namespace App\Services;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Filesystem\Filesystem;

class FileService{
    //PROPIEDADES
    public $targetDirectory;

    public function __construct( string $targetDirectory ){
        $this->targetDirectory = $targetDirectory;
    }

    public function upload ( UploadedFile $file ) : String {

        $extension = $file->guessExtension();
        $fichero = uniqid().".$extension";

        $file->move($this->targetDirectory, $fichero);

        return $fichero;

    }

    public function remove ( string $imagen ): Boolean {

        $filesystem = new Filesystem();

        return $filesystem->remove($this->targetDirectory.'/'.$imagen);


    }

    public function update ( UploadedFile $file, ?string $imagenAntigua ){

        if( $imagenAntigua != NULL ){
            $filesystem = new Filesystem();
            $filesystem->remove ($this->targetDirectory.'/'.$imagenAntigua );
        }

        $extension = $file->guessExtension();
        $fichero = uniqid().".$extension";

        if( $file->move($this->targetDirectory, $fichero)){
            return $fichero;
        }else{
            return 'No se pudieron hacer los cambios en la DB';
        }

    }


}