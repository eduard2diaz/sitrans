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

    /*
     * Funcion que devuelve el mes anterior a un determinado mes
     */
    public static function mesAnterior($mes,$anno){
        $mes--;
        if($mes==0){
            $mes=12;
            $anno--;
        }
        return ['mes'=>$mes,'anno'=>$anno];
    }

    /*
     * Funcion que devuelve la maxima cantidad de dias de un determinado mes, anno
     */
    public static function maxDays($mes,$anno){
        switch ($mes){
            case 1:
            case 3:
            case 5:
            case 7:
            case 8:
            case 10:
            case 12:
                $last=31;
                break;
            case 4:
            case 6:
            case 9:
            case 11:
                $last=30;
                break;
            case 2:
                {
                    $last=28;
                    if($anno%4==0)
                        $last++;
                }
                break;
        }
        return $last;
    }

}