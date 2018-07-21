<?PHP
if (! defined('VIEW_CTRL')) {
    exit('No direct script access allowed');
}


?>
<li>
    <details id="graphs-<?= $data['id'] ?>" class="wrapper-graph graph-<?= $data['type'] ?>">
        <summary> <?= $data['title'] ?> <i class="fa fa-ellipsis-v draggable-sortable-btn"></i></summary>
        <div class="visual"></div>
    </details>
</li>