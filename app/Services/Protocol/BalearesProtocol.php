<?php
namespace App\Services\Protocol;

class BalearesProtocol implements ProtocolInterface {
    public function getCode(): string { return 'baleares'; }
    public function getName(): string { return 'Baleares'; }
    public function isFullyImplemented(): bool { return false; }
    public function getManageUrl(int $reportId): string { return '/protocolo-acoso'; }
}