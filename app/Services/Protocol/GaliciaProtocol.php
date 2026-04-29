<?php
namespace App\Services\Protocol;

class GaliciaProtocol implements ProtocolInterface {
    public function getCode(): string { return 'galicia'; }
    public function getName(): string { return 'Galicia'; }
    public function isFullyImplemented(): bool { return false; }
    public function getManageUrl(int $reportId): string { return '/protocolo-acoso'; }
}