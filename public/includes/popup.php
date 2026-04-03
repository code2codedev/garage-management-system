<?php if (isset($_SESSION['popup_message'])): ?>
<div class="popup <?= $_SESSION['popup_type']; ?>">
    <?= $_SESSION['popup_message']; ?>
</div>
<?php unset($_SESSION['popup_message'], $_SESSION['popup_type']); ?>
<?php endif; ?>