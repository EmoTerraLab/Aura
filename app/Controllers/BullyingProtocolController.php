<?php
namespace App\Controllers;

use App\Core\Auth;
use App\Core\Config;
use App\Core\Lang;
use App\Core\View;
use App\Data\BullyingProtocols;
use App\Models\Setting;

class BullyingProtocolController
{
    private Setting $settings;

    public function __construct(Setting $settings)
    {
        $this->settings = $settings;
    }

    public function index(): void
    {
        $ccaaCode = Config::get('ccaa_code');
        $active = Config::get('ccaa_protocol_active', '1') === '1';
        $showToStudents = Config::get('ccaa_show_to_students', '1') === '1';

        if (!$active) {
            header('Location: /');
            exit;
        }

        if (Auth::role() === 'alumno' && !$showToStudents) {
            header('Location: /alumno/dashboard');
            exit;
        }

        if (empty($ccaaCode)) {
            if (Auth::role() === 'admin') {
                View::render('protocol/not_configured', ['title' => Lang::t('protocol.not_configured')], 'app');
            } else {
                View::render('protocol/coming_soon', ['title' => Lang::t('protocol.coming_soon')], 'app');
            }
            return;
        }

        $protocol = BullyingProtocols::getByCode($ccaaCode);

        View::render('protocol/index', [
            'title' => Lang::t('protocol.nav_title'),
            'protocol' => $protocol
        ], 'app');
    }

    public function apiGet(): void
    {
        header('Content-Type: application/json');
        $ccaaCode = Config::get('ccaa_code');
        if (empty($ccaaCode)) {
            echo json_encode(['error' => 'not_configured']);
            return;
        }
        $protocol = BullyingProtocols::getByCode($ccaaCode);
        echo json_encode($protocol);
    }
}
