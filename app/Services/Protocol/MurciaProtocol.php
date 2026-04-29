<?php
namespace App\Services\Protocol;

class MurciaProtocol implements ProtocolInterface {
    public function getCode(): string { return 'murcia'; }
    public function getName(): string { return 'Murcia'; }
    public function isFullyImplemented(): bool { return false; }
    public function getManageUrl(int $reportId): string { return '/protocolo-acoso'; }
}