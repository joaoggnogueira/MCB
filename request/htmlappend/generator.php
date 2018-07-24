<?PHP

function select($id, $data, $id_attr, $name_attr, $classname) {
    ?>
    <select <?= ($id_attr ? "" : "disabled") ?> id="<?= $id ?>" class="<?= $classname ?>">
        <option value="-1" selected disabled class="first-option">Selecione</option>
        <?PHP if ($data): ?>
            <?PHP foreach ($data as $key => $value): ?>
                <option value="<?= $value[$id_attr] ?>"><?= $value[$name_attr] ?></option>
            <?PHP endforeach; ?>
    <?PHP endif; ?>
    </select>
    <?PHP
}
