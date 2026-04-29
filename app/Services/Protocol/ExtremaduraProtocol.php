<?php
namespace App\Services\Protocol;

class ExtremaduraProtocol implements ProtocolInterface {
    public function getCode(): string { return 'extremadura'; }
    public function getName(): string { return 'Extremadura'; }
    public function isFullyImplemented(): bool { return false; }
    public function getManageUrl(int $reportId): string { return '/protocolo-acoso'; }
}