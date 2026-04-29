<?php
namespace App\Services\Protocol;

class AsturiasProtocol implements ProtocolInterface {
    public function getCode(): string { return 'asturias'; }
    public function getName(): string { return 'Asturias'; }
    public function isFullyImplemented(): bool { return false; }
    public function getManageUrl(int $reportId): string { return '/protocolo-acoso'; }
}