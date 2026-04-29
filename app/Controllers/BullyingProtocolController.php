<?php
namespace App\Controllers;

use App\Core\Auth;
use App\Core\Config;
use App\Core\Lang;
use App\Core\View;
use App\Models\Setting;
use App\Services\ProtocolDataService;

class BullyingProtocolController
{
    private Setting $settings;
    private ProtocolDataService $protocolDataService;

    public function __construct(Setting $settings)
    {
        $this->settings = $settings;
        $this->protocolDataService = new ProtocolDataService();
    }

    public function index(): void
    {
        $active = Config::get('ccaa_protocol_active', '1') === '1';

        if (!$active) {
            header('Location: /');
            exit;
        }

        if (Auth::role() === 'alumno' && !$this->protocolDataService->isVisibleToStudents()) {
            header('Location: /alumno/dashboard');
            exit;
        }

        $protocol = $this->protocolDataService->getCurrentProtocol();

        if (!$protocol) {
            if (Auth::role() === 'admin') {
                View::render('protocol/not_configured', ['title' => Lang::t('protocol.not_configured')], 'app');
            } else {
                View::render('protocol/coming_soon', ['title' => Lang::t('protocol.coming_soon')], 'app');
            }
            return;
        }

        View::render('protocol/index', [
            'title' => Lang::t('protocol.nav_title'),
            'protocol' => $protocol
        ], 'app');
    }

    public function apiGet(): void
    {
        header('Content-Type: application/json');
        $protocol = $this->protocolDataService->getCurrentProtocol();
        
        if (!$protocol) {
            echo json_encode(['error' => 'not_configured']);
            return;
        }
        
        echo json_encode($protocol);
    }
}
