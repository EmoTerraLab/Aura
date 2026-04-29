<?php
namespace App\Services\Protocol;

class ComunidadValencianaProtocol implements ProtocolInterface {
    public function getCode(): string { return 'valencia'; }
    public function getName(): string { return 'Comunidad Valenciana'; }
    public function isFullyImplemented(): bool { return false; }
    public function getManageUrl(int $reportId): string { return '/protocolo-acoso'; }
}