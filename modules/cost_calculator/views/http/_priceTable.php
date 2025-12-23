<?php

/**
 * @var array $priceMatrix
 * @var array $tonnageList
 * @var string $month
 * @var string $rawType
 * @var int $tonnage
 * @var int $price
 */

use app\modules\cost_calculator\dto\CalculatePriceDTO;

?>

<div id="result" class="mb-4">
    <div class="row justify-content-center mt-5">
        <div class="col-md-3 me-3">
            <div class="card shadow-lg">
                <div class="card-header bg-success text-white" style="font-weight: bold; font-size: 17px;">
                    Введенные данные:
                </div>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item">
                        <strong>
                            Месяц: </strong> <?= mb_convert_case($month, MB_CASE_TITLE, 'UTF-8') ?>
                    </li>
                    <li class="list-group-item">
                        <strong>
                            Тоннаж: </strong> <?= mb_convert_case($tonnage, MB_CASE_TITLE, 'UTF-8') ?>
                    </li>
                    <li class="list-group-item">
                        <strong> Тип
                            сырья: </strong> <?= mb_convert_case($rawType, MB_CASE_TITLE, 'UTF-8') ?>
                    </li>
                    <li class="list-group-item">
                        <strong> Итог, руб.: </strong>
                        <?= $price ?>
                    </li>
                </ul>
            </div>
        </div>
        <div class="col-md-6 table-responsive border rounded-1 shadow-lg p-0">
            <table class="table table-hover table-striped text-center mb-0">

                <thead>
                <tr>
                    <th>Т/M</th>
                    <?php
                    foreach ($tonnageList as $tonnageValue): ?>
                        <th><?= mb_convert_case($tonnageValue, MB_CASE_TITLE, 'UTF-8') ?></th>
                    <?php
                    endforeach ?>
                </tr>
                </thead>

                <tbody>
                <?php
                foreach ($priceMatrix as $monthName => $priceRow): ?>
                    <tr>
                        <td>
                            <?= mb_convert_case($monthName, MB_CASE_TITLE, 'UTF-8') ?>
                        </td>
                        <?php
                        foreach ($priceRow as $tonnageValue => $priceValue):
                            $cellClass = null;

                            if ($month === $monthName && $tonnage === $tonnageValue) {
                                $cellClass = 'with-border';
                            }
                            ?>
                            <td class="<?= $cellClass ?>">
                                <?= $priceValue ?? '-' ?>
                            </td>
                        <?php
                        endforeach ?>
                    </tr>
                <?php
                endforeach ?>
                </tbody>

            </table>
        </div>
    </div>
</div>
