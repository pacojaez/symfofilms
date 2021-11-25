<?php

namespace App\Services;

class EntityFakerService {

    public function getMock( $className ){

        $fullName = '\\App\\Entity\\'.$className;

        return new $fullName;
    }
}