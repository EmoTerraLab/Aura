<?php
/**
 * Protocol Timeline Component
 * Renders a horizontal timeline of protocol phases.
 * 
 * @param array $steps Array of steps from $protocol->getTimelineSteps()
 * @param string|null $currentPhase The current active phase key
 */

if (empty($steps)) {
    echo '<p class="text-[10px] text-slate-400 italic px-8 py-4">Timeline no disponible para este protocolo.</p>';
    return;
}

$activeIndex = -1;
foreach ($steps as $idx => $step) {
    if ($step['key'] === $currentPhase) {
        $activeIndex = $idx;
        break;
    }
}
?>

<div id="protocol-timeline" class="bg-white border-b px-4 md:px-8 py-4 flex flex-nowrap items-center justify-start overflow-x-auto no-scrollbar gap-4 w-full">
    <?php foreach ($steps as $idx => $step): 
        $isActual = ($idx === $activeIndex);
        $isPast = ($idx < $activeIndex && $activeIndex !== -1);
        
        $colorClass = $isActual ? 'bg-primary text-white shadow-lg shadow-primary/20 scale-110' : ($isPast ? 'bg-emerald-500 text-white' : 'bg-slate-100 text-slate-400');
        $textColor = $isActual ? 'text-primary' : ($isPast ? 'text-emerald-600' : 'text-slate-400');
        
        // Material symbols handling: if it's past, show check, else show the icon from the step
        $iconName = $isPast ? 'check' : ($step['icon'] ?? 'circle');
        
        // Fix for common icon name mismatches if necessary (e.g., envelope -> mail)
        if ($iconName === 'envelope') $iconName = 'mail';
        if ($iconName === 'clipboard-list') $iconName = 'assignment';
        if ($iconName === 'eye') $iconName = 'visibility';
        if ($iconName === 'check-circle') $iconName = 'check_circle';
    ?>
        <div class="flex items-center gap-2 shrink-0 transition-all">
            <div class="w-8 h-8 rounded-xl flex items-center justify-center text-[10px] font-bold <?= $colorClass ?>">
                <span class="material-symbols-outlined text-sm"><?= $iconName ?></span>
            </div>
            <div class="flex flex-col">
                <span class="text-[9px] font-black uppercase tracking-tighter <?= $textColor ?>"><?= htmlspecialchars($step['label']) ?></span>
                <?php if (!empty($step['deadline_days'])): ?>
                    <span class="text-[8px] font-bold text-slate-300">Día <?= $step['deadline_days'] ?></span>
                <?php endif; ?>
            </div>
            <?php if ($idx < count($steps) - 1): ?>
                <span class="material-symbols-outlined text-slate-200 text-sm mx-1">chevron_right</span>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>

    <?php if (isset($deadlineAlert) && !empty($deadlineAlert)): 
        $alertLevel = $deadlineAlert['level'] ?? 'default';
        $alertClasses = match($alertLevel) {
            'ok' => 'text-emerald-600 bg-emerald-50 border-emerald-100',
            'warning' => 'text-amber-600 bg-amber-50 border-amber-100',
            'danger' => 'text-red-600 bg-red-50 border-red-100',
            'overdue' => 'bg-red-900 text-white animate-pulse border-red-900',
            default => 'text-slate-500 bg-slate-50 border-slate-100'
        };
    ?>
        <div class="ml-auto px-4 py-2 rounded-xl text-[9px] font-black uppercase border <?= $alertClasses ?> flex items-center gap-2 shrink-0 shadow-sm">
            <span class="material-symbols-outlined text-xs">schedule</span> <?= htmlspecialchars($deadlineAlert['message']) ?>
        </div>
    <?php endif; ?>
</div>
