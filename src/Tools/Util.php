<?php

namespace App\Tools;

use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class Util
{

    const LISTADO_MESES=['Enero'=>1,'Febrero'=>2,'Marzo'=>3,'Abril'=>4,
                 'Mayo'=>5,'Junio'=>6,'Julio'=>7,'Agosto'=>8,
                 'Septiembre'=>9,'Octubre'=>10,'Noviembre'=>11,'Diciembre'=>12
                ];

    public static function getMesKey(int $pos=null){
        $keys=array_keys(self::LISTADO_MESES);
        if(null==$pos)
            return $keys;
        elseif($pos<1 || $pos>12)
            throw new \Exception('Indice incorrecto');
        else
            return $keys[$pos-1];
    }
}