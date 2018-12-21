<?php

namespace App\Tools;

class Util
{
    const meses=['Enero'=>1,'Febrero'=>2,'Marzo'=>3,'Abril'=>4,
                 'Mayo'=>5,'Junio'=>6,'Julio'=>7,'Agosto'=>8,
                 'Septiembre'=>9,'Octubre'=>10,'Noviembre'=>11,'Diciembre'=>12
                ];

    public static function getMesKey(int $pos=null){
        $keys=array_keys(self::meses);
        if(null==$pos)
            return $keys;
        elseif($pos<1 || $pos>12)
            throw new \Exception('Indice incorrecto');
        else
            return $keys[$pos-1];
    }


}