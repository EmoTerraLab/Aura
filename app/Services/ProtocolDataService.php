<?php
namespace App\Services;

use App\Core\Config;
use App\Data\BullyingProtocols;

class ProtocolDataService
{
    /**
     * Obtiene los datos del protocolo configurado actualmente.
     */
    public function getCurrentProtocol(): ?array
    {
        $ccaaCode = Config::get('ccaa_code');
        if (empty($ccaaCode)) {
            return null;
        }
        return BullyingProtocols::getByCode($ccaaCode);
    }

    /**
     * Verifica si el protocolo está activo y configurado.
     */
    public function isConfigured(): bool
    {
        return !empty(Config::get('ccaa_code'));
    }

    /**
     * Verifica si el protocolo es visible para alumnos.
     */
    public function isVisibleToStudents(): bool
    {
        return Config::get('ccaa_protocol_active', '1') === '1' 
               && Config::get('ccaa_show_to_students', '1') === '1';
    }
}
