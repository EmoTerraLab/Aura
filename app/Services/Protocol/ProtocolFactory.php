<?php
namespace App\Services\Protocol;

/**
 * ProtocolFactory - Devuelve el módulo correcto según ccaa_code.
 */
class ProtocolFactory {
    public static function make(string $ccaaCode): ProtocolInterface {
        $ccaaCode = strtoupper($ccaaCode);
        return match($ccaaCode) {
            'AND' => new AndaluciaProtocol(),
            'ARA'      => new AragonProtocol(),
            'ARAGON'   => new AragonProtocol(),
            'AST' => new AsturiasProtocol(),
            'BAL' => new BalearesProtocol(),
            'CAN' => new CanariasProtocol(),
            'CNT' => new CantabriaProtocol(),
            'CYL' => new CastillaLeonProtocol(),
            'CLM' => new CastillaLaManchaProtocol(),
            'CAT'      => new CatalunaProtocol(),
            'CATALUNA' => new CatalunaProtocol(),
            'VAL' => new ComunidadValencianaProtocol(),
            'EXT' => new ExtremaduraProtocol(),
            'GAL' => new GaliciaProtocol(),
            'MAD' => new MadridProtocol(),
            'MUR' => new MurciaProtocol(),
            'NAV' => new NavarraProtocol(),
            'PV'  => new PaisVascoProtocol(),
            'RIO' => new RiojaProtocol(),
            'CEU' => new CeutaProtocol(),
            'MEL' => new MelillaProtocol(),
            default => new MadridProtocol(), // Fallback seguro
        };
    }
}
