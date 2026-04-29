<?php
namespace App\Services\Protocol;

class CastillaLaManchaProtocol implements ProtocolInterface {
    public function getCode(): string { return 'castilla_mancha'; }
    public function getName(): string { return 'Castilla-La Mancha'; }
    public function isFullyImplemented(): bool { return false; }
    public function getManageUrl(int $reportId): string { return '/protocolo-acoso'; }
}