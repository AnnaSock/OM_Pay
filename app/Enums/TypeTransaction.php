<?php

namespace App\Enums;

enum TypeTransaction: string
{
    case DEPOT = 'Depot';
    case RETRAIT = 'Retrait';
    case PAYEMENT = 'Payement';

}
