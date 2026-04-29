<?php
namespace App\Services\Protocol;

class CastillaLeonProtocol implements ProtocolInterface {
    public function getCode(): string { return 'castilla_leon'; }
    public function getName(): string { return 'Castilla y León'; }
    public function isFullyImplemented(): bool { return false; }
    public function getManageUrl(int $reportId): string { return '/protocolo-acoso'; }
}