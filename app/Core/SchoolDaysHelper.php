<?php
declare(strict_types=1);
namespace App\Core;
use DateTime;
class SchoolDaysHelper {
    public static function addSchoolDays(string $startDate, int $daysToAdd, array $holidays = []): string {
        $date = new DateTime($startDate);
        $added = 0;
        while ($added < $daysToAdd) {
            $date->modify("+1 day");
            if ((int)$date->format("N") < 6 && !in_array($date->format("Y-m-d"), $holidays)) $added++;
        }
        return $date->format("Y-m-d H:i:s");
    }
}
