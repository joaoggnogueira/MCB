<?PHP
if (!defined('VIEW_CTRL')) {
    exit('No direct script access allowed');
}

$lista = $data['lista'];
if (!isset($lista[0])) {
    return;
}
$first = $lista[0];
$keyname = "id";

if (!isset($first[$keyname])) {
    $keyname = 'cod';
}
?>
<li name="<?= $data['li-name'] ?>" class="filter-type">
    <div class='title'> <i class="to-window-btn fa fa-reply" style="display: none"></i> <i class="to-window-btn fa fa-window-maximize"></i> <?= $data['title'] ?> <i class="fa fa-ellipsis-v draggable-sortable-btn"></i> </div>
    <div class="body body-filter" id="filter-body-<?= $data['id'] ?>">
        <div class="options">
            <button class="select-all">Selecionar Tudo</button>
            <button class="select-one">Modo Único</button>
        </div>
        <div class='content'>
            <ul>
                <?PHP foreach ($lista as $i => $elem): ?>
                    <li>
                        <input class="filter-checkbox" id='filter-<?= $data['id'] ?>-<?= $elem[$keyname] ?>' checked="checked" type='checkbox' value='<?= $elem[$keyname] ?>' title='<?= ($elem['nome']) ?>' name='filter-<?= $data['id'] ?>'/>
                        <label for='filter-<?= $data['id'] ?>-<?= $elem[$keyname] ?>'><?= ($elem['nome']) ?></label>
                    </li>
                <?PHP endforeach; ?>
            </ul>
        </div>
    </div>
</li>