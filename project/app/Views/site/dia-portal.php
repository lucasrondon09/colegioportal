
<?php

use App\Controllers\Site\Home;

$gridPortal = new Home;
$diaPortal = $gridPortal->gridPortal();

?>

<div class="container">
    <div class="row">
        <div class="col-lg-12 text-center">
            <div class="dia_dia py-5 shadow-lg">
                <h1 class="text-white">DIA A DIA NO PORTAL</h1>
                <ul class="list-inline">
                    <?php foreach($diaPortal as $diaPortalItem):?>
                    <li class="list-inline-item"><a href="<?= base_url('/Dia-a-Dia').'/'.$diaPortalItem->id?>" class="text-decoration-none"><img src="<?= $diaPortalItem->capa?>" alt="dia_dia" style="height: 100px; width: auto;"></a></li>
                    <?php endforeach;?>
                </ul>
            </div>
        </div>
    </div>
</div>