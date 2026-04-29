<?php
namespace App\Services\Protocol;

class RiojaProtocol implements ProtocolInterface {
    public function getCode(): string { return 'rioja'; }
    public function getName(): string { return 'La Rioja'; }
    public function isFullyImplemented(): bool { return false; }
    public function getManageUrl(int $reportId): string { return '/protocolo-acoso'; }
}