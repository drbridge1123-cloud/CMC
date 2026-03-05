<?php
/**
 * Shared Modal Component
 *
 * Usage:
 *   <?php $modalVar='showCreateModal'; $modalTitle='New Item'; $modalSize='md'; ?>
 *   <?php include __DIR__.'/../../components/_modal.php'; ?>
 *       <!-- Your body content here -->
 *   <?php include __DIR__.'/../../components/_modal-end.php'; ?>
 *
 * Parameters:
 *   $modalVar   - Alpine.js variable that controls visibility (required)
 *   $modalTitle - Title shown in navy header (required)
 *   $modalSize  - 'sm' (440px), 'md' (600px, default), 'lg' (800px), 'xl' (95vw)
 */
$sizeClass = match($modalSize ?? 'md') {
    'sm' => 'sp-modal-box-sm',
    'lg' => 'sp-modal-box-lg',
    'xl' => 'sp-modal-box-xl',
    default => ''
};
?>
<div x-show="<?= $modalVar ?>" x-cloak class="sp-modal-overlay" @click.self="<?= $modalVar ?> = false">
    <div class="sp-modal-box <?= $sizeClass ?>" @click.stop>
        <div class="sp-modal-header">
            <h3 class="sp-modal-title"><?= $modalTitle ?></h3>
            <button @click="<?= $modalVar ?> = false" class="sp-modal-close">&times;</button>
        </div>
        <div class="sp-modal-body">
