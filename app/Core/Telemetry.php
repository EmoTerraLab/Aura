<?php
namespace App\Core;

class Telemetry {
    /**
     * Cumplimiento: Telemetría - Llamada silenciosa post-dispatch.
     * En un entorno real, esto podría usar fastcgi_finish_request() y luego
     * enviar datos con cURL, o publicarlo en una cola (RabbitMQ/Redis).
     * Para este MVP, simulamos el registro asíncrono.
     */
    public static function checkAndRunCron() {
        $lockFile = __DIR__ . '/../../storage/telemetry_cron.lock';
        $today = date('Y-m-d');
        
        // Verifica si ya se ejecutó hoy
        if (file_exists($lockFile)) {
            $lastRun = file_get_contents($lockFile);
            if ($lastRun === $today) {
                return; // Ya se ejecutó
            }
        }
        
        // Registrar la ejecución de hoy
        if (!is_dir(dirname($lockFile))) {
            mkdir(dirname($lockFile), 0750, true);
        }
        file_put_contents($lockFile, $today);

        // Simulamos envío asíncrono (Poor Man's Cron)
        // En producción real, la ejecución de la lógica pesada de recopilación
        // debería realizarse en un proceso separado o background (ej. fastcgi_finish_request)
        self::dispatch('daily_cron_executed', [
            'timestamp' => time()
        ]);
    }

    public static function dispatch($event, $data = []) {
        // Ejecución silenciosa simulada en log (o base de datos si existiera la tabla app_meta)
        $logEntry = date('Y-m-d H:i:s') . " | EVENT: {$event} | DATA: " . json_encode($data) . PHP_EOL;
        
        // Escribimos en un archivo log temporal de forma asíncrona ("fire and forget")
        $logFile = __DIR__ . '/../../storage/telemetry.log';
        
        // Crear directorio si no existe
        if (!is_dir(dirname($logFile))) {
            mkdir(dirname($logFile), 0750, true);
        }

        file_put_contents($logFile, $logEntry, FILE_APPEND);
    }
}