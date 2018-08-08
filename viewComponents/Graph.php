<?PHP
if (! defined('VIEW_CTRL')) {
    exit('No direct script access allowed');
}


?>
<li>
    <details open id="graph-item-<?= $data['id'] ?>" class="graph-item wrapper-graph">
        <summary> <?= $data['title'] ?> <i class="fa fa-ellipsis-v draggable-sortable-btn"></i></summary>
        <div class="graph-content graph-<?= $data['type'] ?>" categoria="<?= $data['title'] ?>" name="<?= $data['id'] ?>" id="graph-content-<?= $data['id'] ?>"></div>
    </details>
</li>