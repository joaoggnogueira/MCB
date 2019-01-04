<?PHP

if (!isset($data)) {
    echo "Não é permitido acesso direto! 0x0002";
    exit();
}
?> <br/><br/> <?php
if ($markerType == 0) {
    ?>
    <div class="clabel label-100 f15 bold">Município</div>
    <div class="clabel label-100 f15"><?= $data['codigo_municipio'] ?> - <?= utf8_encode($data['nome_municipio']) ?></div>
    <div class="clabel horizontal-space"></div>
    <div class="clabel label-100 f15 bold">Total de Habitantes do Município</div>
    <div class="clabel label-100 f15"><?= number_format($data['populacao'] , 0, ',', '.'); ?></div>
    <div class="clabel label-100 f12"><i>Dados estimados IBGE/2018</i></div>
    <div class="clabel horizontal-space"></div>
    <div class="clabel label-100 f15 bold">Geolocalização</div>
    <div class="clabel label-100 f15">Latitude <?= $data['latitude'] ?> e Longitude <?= $data['longitude'] ?></div>
    <div class="clabel horizontal-space"></div>
    <div class="clabel label-100 f15 bold">Estado</div>
    <div class="clabel label-100 f15"><?= utf8_encode($data['nome_estado']) ?> (<?= $data['sigla_estado'] ?>)</div>
    <div class="clabel horizontal-space"></div>
    <div class="clabel label-100 f15 bold">Região</div>
    <div class="clabel label-100 f15"><?= $data['nome_regiao'] ?></div>
    <?PHP
} else if ($markerType == 1) {
    ?>
    <div class="clabel label-100 f15 bold">Estado</div>
    <div class="clabel label-100 f15"><?= utf8_encode($data['nome_estado']) ?> (<?= $data['sigla_estado'] ?>)</div>
    <div class="clabel horizontal-space"></div> 
    <div class="clabel label-100 f15 bold">Total de Habitantes do Estado</div>
    <div class="clabel label-100 f15"><?= number_format($data['populacao'] , 0, ',', '.'); ?></div>
    <div class="clabel label-100 f12"><i>Dados estimados IBGE/2017</i></div>
    <div class="clabel horizontal-space"></div> 
    <div class="clabel label-100 f15 bold">Região</div>
    <div class="clabel label-100 f15"><?= $data['nome_regiao'] ?></div>
    <?PHP
} else if ($markerType == 2) {
    ?>
    <div class="clabel label-100 f15 bold">Região</div>
    <div class="clabel label-100 f15"><?= $data['nome_regiao'] ?></div>
    <div class="clabel horizontal-space"></div> 
    <div class="clabel label-100 f15 bold">Total de Habitantes do Região</div>
    <div class="clabel label-100 f15"><?= number_format($data['populacao'] , 0, ',', '.'); ?></div>
    <div class="clabel label-100 f12"><i>Dados estimados IBGE/2017</i></div>
    <?PHP
}
?>


