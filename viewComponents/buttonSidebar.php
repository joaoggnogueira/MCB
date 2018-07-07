<?PHP
if (! defined('VIEW_CTRL')) {
    exit('No direct script access allowed');
}
?>

<button class="sidebar-button" id="sidebar-<?= $data['id'] ?>-btn">
    <div class="sidebar-button-icon"><i class="fa <?= $data['fa-icon'] ?>"></i></div>
    <div class="sidebar-button-text"><?= $data['text'] ?></div>
</button>

