<?php
/**
 * Protocol Timeline Component
 * Renders a horizontal timeline of protocol phases.
 * 
 * @param array $steps Array of steps from $protocol->getTimelineSteps()
 * @param string|null $currentPhase The current active phase key
 */

if (empty($steps)) {
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

<div id="protocol-timeline" class="bg-surface-container-lowest border-b border-surface-variant/40 px-4 md:px-6 py-3 flex flex-nowrap items-center justify-start overflow-x-auto gap-3 w-full font-display">
    <?php foreach ($steps as $idx => $step): 
        $isActual = ($idx === $activeIndex);
        $isPast = ($idx < $activeIndex && $activeIndex !== -1);
        
        $colorClass = $isActual ? 'bg-primary text-on-primary shadow-xs' : ($isPast ? 'bg-prisma-mint/40 text-teal-950 font-bold' : 'bg-surface-container-low text-on-surface-variant/60');
        $textColor = $isActual ? 'text-primary font-bold' : ($isPast ? 'text-teal-900 font-bold' : 'text-on-surface-variant/70');
        
        $iconName = $isPast ? 'check' : ($step['icon'] ?? 'circle');
        if ($iconName === 'envelope') $iconName = 'mail';
        if ($iconName === 'clipboard-list') $iconName = 'assignment';
        if ($iconName === 'eye') $iconName = 'visibility';
        if ($iconName === 'check-circle') $iconName = 'check_circle';
    ?>
        <div class="flex items-center gap-2 shrink-0 transition-all">
            <div class="w-7 h-7 rounded-lg flex items-center justify-center text-[10px] font-bold <?= $colorClass ?>">
                <span class="material-symbols-outlined text-[16px]"><?= $iconName ?></span>
            </div>
            <div class="flex flex-col">
                <span class="text-[10px] uppercase tracking-wider <?= $textColor ?>"><?= htmlspecialchars($step['label']) ?></span>
                <?php if (!empty($step['deadline_days'])): ?>
                    <span class="text-[9px] font-semibold text-on-surface-variant/60">Día <?= $step['deadline_days'] ?></span>
                <?php endif; ?>
            </div>
            <?php if ($idx < count($steps) - 1): ?>
                <span class="material-symbols-outlined text-surface-variant text-base mx-0.5">chevron_right</span>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>

    <?php if (isset($deadlineAlert) && !empty($deadlineAlert)): 
        $alertLevel = $deadlineAlert['level'] ?? 'default';
        $alertClasses = match($alertLevel) {
            'ok' => 'text-teal-900 bg-prisma-mint/20 border-teal-600/30',
            'warning' => 'text-amber-900 bg-amber-500/10 border-amber-500/30',
            'danger' => 'text-error bg-error/10 border-error/20',
            'overdue' => 'bg-error text-white animate-pulse border-error',
            default => 'text-on-surface-variant bg-surface-container-low border-surface-variant/40'
        };
    ?>
        <div class="ml-auto px-3 py-1.5 rounded-xl text-[9px] font-bold uppercase border <?= $alertClasses ?> flex items-center gap-1.5 shrink-0 shadow-xs">
            <span class="material-symbols-outlined text-sm">schedule</span> <?= htmlspecialchars($deadlineAlert['message']) ?>
        </div>
    <?php endif; ?>
</div>
