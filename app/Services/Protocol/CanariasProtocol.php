<?php
namespace App\Services\Protocol;

class CanariasProtocol implements ProtocolInterface {
    public function getCode(): string { return 'canarias'; }
    public function getName(): string { return 'Canarias'; }
    public function isFullyImplemented(): bool { return false; }
    public function getManageUrl(int $reportId): string { return '/protocolo-acoso'; }
}