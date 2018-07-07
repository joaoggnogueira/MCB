<?PHP
if (! defined('VIEW_CTRL')) {
    exit('No direct script access allowed');
}

$lista = $data['lista'];
$first = $lista[0];
$keyname = "id";

if(!isset($first[$keyname])){
    $keyname = 'cod';
}

?>

<div class='title'><?= $data['title'] ?></div>
<div class="options">
    <button class="select-all">Selecionar Tudo</button>
    <button class="select-one">Modo Único</button>
</div>
<div class='content'>
    <ul>
        <?PHP foreach($lista as $i => $elem): ?>
            <li>
                <input class="filter-checkbox" id='filter-<?= $data['id'] ?>-<?= $elem[$keyname] ?>' checked="checked" type='checkbox' value='<?= $elem[$keyname] ?>' title='<?= utf8_encode($elem['nome']) ?>' name='filter-<?= $data['id'] ?>'/>
                <label for='filter-<?= $data['id'] ?>-<?= $elem[$keyname] ?>'><?= utf8_encode($elem['nome']) ?></label>
            </li>
        <?PHP endforeach;?>
    </ul>
</div>