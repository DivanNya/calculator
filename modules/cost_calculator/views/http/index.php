<?php

/**
 * @var ViewInterface $view
 * @var string $xDebugTag
 * @var string $errorMessage
 * @var array $priceMatrix
 * @var ListsDTO $lists
 * @var CalculatePriceDTO $paramsDto
 * @var boolean $showTable
 */

use app\modules\cost_calculator\dto\CalculatePriceDTO;
use app\modules\cost_calculator\dto\ListsDTO;
use Monoelf\Framework\view\ViewInterface;

?>

<!DOCTYPE html>
<html lang="ru-RU" class="h-100">

<head>
    <title>
        Калькулятор
    </title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link type="image/x-icon" href="/favicon.ico" rel="icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous">
    <style>
        .logo {
            width: 100px;
            filter: invert(100%) sepia(100%) saturate(1%) hue-rotate(207deg) brightness(102%) contrast(102%) drop-shadow(0px 0px 0px rgb(255, 255, 255));
        }

        td.with-border {
            border: 2px solid green;
        }
    </style>
</head>

<body class="d-flex flex-column h-100">

<header id="header">
    <nav id="w0" class="navbar-expand-md navbar-dark bg-dark navbar">
        <div class="container">
            <a class="navbar-brand" href="/">
                <img class="logo" src="/img/logo.png" alt="ЭФКО">
            </a>
        </div>
    </nav>
</header>

<main id="main" class="flex-shrink-0" role="main">
    <div class="container" id="main-block">

        <div class="text-center mb-4 mt-3">
            <h1> Калькулятор стоимости доставки сырья </h1>
        </div>

        <div class="row justify-content-center">
            <div class="col-md-6 border rounded-3 p-4 shadow ">

                <form id="calculate_form" action="/" method="post">

                    <div class="mb-3 required">
                        <label class="form-label" for="month-select">Месяц</label>
                        <select id="month-select" class="form-select" name="month" aria-required="true">
                            <option value="" disabled selected>Выберите параметр</option>

                            <?php
                            foreach ($lists->months as $id => $name): ?>
                                <option
                                    <?= $paramsDto->monthId === $id ? 'selected' : null ?>
                                        value="<?= $id ?>">
                                    <?= mb_convert_case($name, MB_CASE_TITLE, 'UTF-8') ?>
                                </option>
                            <?php
                            endforeach ?>

                        </select>
                    </div>

                    <div class="mb-3 required">
                        <label class="form-label" for="tonnage-select">Тоннаж</label>
                        <select id="tonnage-select" class="form-select" name="tonnage" aria-required="true">
                            <option value="" disabled selected>Выберите параметр</option>

                            <?php
                            foreach ($lists->tonnages as $id => $value): ?>
                                <option
                                    <?= $paramsDto->tonnageId === $id ? 'selected' : null ?>
                                        value="<?= $id ?>">
                                    <?= $value ?>
                                </option>
                            <?php
                            endforeach ?>

                        </select>
                    </div>

                    <div class="mb-3 required">
                        <label class="form-label" for="raw_type-select">Тип сырья</label>
                        <select id="raw_type-select" class="form-select" name="raw_type" aria-required="true">
                            <option value="" disabled selected>Выберите параметр</option>

                            <?php
                            foreach ($lists->rawTypes as $id => $name): ?>
                                <option
                                    <?= $paramsDto->rawTypeId === $id? 'selected' : null ?>
                                        value="<?= $id ?>">
                                    <?= mb_convert_case($name, MB_CASE_TITLE, 'UTF-8') ?>
                                </option>
                            <?php
                            endforeach ?>

                        </select>
                    </div>

                    <button type="submit" id="calculate_button" class="btn btn-success">Рассчитать</button>

                    <a href="/" type="button" class="btn btn-danger">Сброс</a>

                </form>
            </div>
        </div>

        <?php
        if ($errorMessage !== null): ?>
            <div class="row justify-content-center">
                <div class="col-md-4 mt-4">
                    <div class="alert alert-danger">
                        <?= $errorMessage ?>
                        <hr>
                        Идентификатор ошибки: <?= $xDebugTag ?>
                    </div>
                </div>
            </div>
        <?php
        endif;

        if ($showTable === true) {
            echo $view->render('@views-web/_priceTable', [
                'tonnageList' => $lists->tonnages,
                'priceMatrix' => $priceMatrix,
                'month' => $lists->months[$paramsDto->monthId],
                'rawType' => $lists->rawTypes[$paramsDto->rawTypeId],
                'tonnage' => $lists->tonnages[$paramsDto->tonnageId],
                'price' => $paramsDto->price,
            ]);
        }
        ?>
</main>

<footer id="footer" class="mt-auto py-3 bg-light">
    <div class="container">
        <div class="row text-muted">
            <div class="col-md-6 text-center text-md-start">
                &copy; ЭФКО <?= date('Y') ?>
            </div>
        </div>
    </div>
</footer>

</body>

</html>
