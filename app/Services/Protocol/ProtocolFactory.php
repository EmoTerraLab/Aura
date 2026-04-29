<?php
namespace App\Services\Protocol;

/**
 * ProtocolFactory - Devuelve el módulo correcto según ccaa_code.
 */
class ProtocolFactory {
    public static function make(string $ccaaCode): ProtocolInterface {
        return match($ccaaCode) {
            'cataluna' => new CatalunaProtocol(),
            'aragon'   => new AragonProtocol(),
            default    => new NullProtocol($ccaaCode),
        };
    }
}
