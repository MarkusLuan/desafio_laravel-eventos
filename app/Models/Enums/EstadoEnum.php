<?php

namespace App\Models\Enums;

use Illuminate\Support\Str;

enum EstadoEnum {
    case AC;
    case AL;
    case AP;
    case AM;
    case BA;
    case CE;
    case DF;
    case ES;
    case GO;
    case MA;
    case MT;
    case MS;
    case MG;
    case PA;
    case PB;
    case PR;
    case PE;
    case PI;
    case RJ;
    case RN;
    case RS;
    case RO;
    case RR;
    case SC;
    case SP;
    case SE;
    case TO;

    function toString(): String {
        return Str::title(
            str_replace('_', ' ', $this->name)
        );
    }
}