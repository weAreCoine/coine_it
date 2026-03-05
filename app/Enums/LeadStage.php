<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum LeadStage: string implements HasColor, HasLabel
{
    /** Lead appena acquisito, nessun contatto ancora effettuato */
    case NEW = 'new';

    /** Primo contatto effettuato, in attesa di risposta */
    case CONTACTED = 'contacted';

    /** Lead qualificato: ha espresso interesse reale ed è in target */
    case QUALIFIED = 'qualified';

    /** Offerta o preventivo inviato, in attesa di feedback */
    case PROPOSAL = 'proposal';

    /** In trattativa attiva su condizioni, prezzi o tempi */
    case NEGOTIATION = 'negotiation';

    /** Trattativa conclusa positivamente, convertito in cliente */
    case WON = 'won';

    /** Trattativa conclusa negativamente, lead non convertito */
    case LOST = 'lost';

    /** Temporaneamente inattivo, non risponde o ha rimandato */
    case DORMANT = 'dormant';

    public function getLabel(): string
    {
        return match ($this) {
            self::NEW => 'Nuovo',
            self::CONTACTED => 'Contattato',
            self::QUALIFIED => 'Qualificato',
            self::PROPOSAL => 'Proposta',
            self::NEGOTIATION => 'Trattativa',
            self::WON => 'Vinto',
            self::LOST => 'Perso',
            self::DORMANT => 'Dormiente',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::NEW => 'info',
            self::CONTACTED => 'primary',
            self::QUALIFIED => 'success',
            self::PROPOSAL => 'warning',
            self::NEGOTIATION => 'warning',
            self::WON => 'success',
            self::LOST => 'danger',
            self::DORMANT => 'gray',
        };
    }
}
