<?php
namespace App\Services\Protocol;

class CeutaProtocol implements ProtocolInterface {
    public function getCode(): string { return 'ceuta'; }
    public function getName(): string { return 'Ceuta'; }
    public function isFullyImplemented(): bool { return false; }
    public function getManageUrl(int $reportId): string { return '/protocolo-acoso'; }
}