<?php
namespace App\Services\Protocol;

class NavarraProtocol implements ProtocolInterface {
    public function getCode(): string { return 'navarra'; }
    public function getName(): string { return 'Navarra'; }
    public function isFullyImplemented(): bool { return false; }
    public function getManageUrl(int $reportId): string { return '/protocolo-acoso'; }
}