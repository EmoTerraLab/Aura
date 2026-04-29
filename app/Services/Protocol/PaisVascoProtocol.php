<?php
namespace App\Services\Protocol;

class PaisVascoProtocol implements ProtocolInterface {
    public function getCode(): string { return 'pais_vasco'; }
    public function getName(): string { return 'País Vasco'; }
    public function isFullyImplemented(): bool { return false; }
    public function getManageUrl(int $reportId): string { return '/protocolo-acoso'; }
}