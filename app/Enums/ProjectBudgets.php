<?php

namespace App\Enums;

enum ProjectBudgets: string
{
    case Under5 = '<5k';
    case From5To10 = '5 ÷ 10k';
    case From10To20 = '10 ÷ 20k';
    case From20To50 = '20 ÷ 50k';
    case Over50 = '>50k';
}
