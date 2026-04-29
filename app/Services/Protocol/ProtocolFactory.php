<?php
namespace App\Services\Protocol;

class ProtocolFactory {
    public static function make(string $ccaaCode): ProtocolInterface {
        return match($ccaaCode) {
            'cataluna' => new CatalunaProtocol(),
            'aragon'   => new AragonProtocol(),
            default    => new NullProtocol($ccaaCode),
        };
    }
}