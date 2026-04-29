<?php
namespace App\Services\Protocol;

/**
 * Factoría para la instanciación de protocolos legales según la CCAA.
 * Centraliza la lógica de selección de normativa aplicable.
 */
class ProtocolFactory {
    /**
     * Crea una instancia del protocolo correspondiente al código de CCAA.
     * 
     * @param string $ccaaCode Código identificador de la comunidad (ej: 'aragon', 'cataluna').
     * @return ProtocolInterface Instancia del protocolo solicitado o NullProtocol como fallback.
     */
    public static function make(string $ccaaCode): ProtocolInterface {
        return match($ccaaCode) {
            'cataluna' => new CatalunaProtocol(),
            'aragon'   => new AragonProtocol(),
            default    => new NullProtocol($ccaaCode),
        };
    }
}