<?php // Страница-плитка для раздела Ремонты ?>
<div class="container mt-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">Главная</a></li>
            <li class="breadcrumb-item active" aria-current="page">Ремонты</li>
        </ol>
    </nav>
    <h2 class="mb-4">Ремонты</h2>
    <div class="row g-4">
        <div class="col-md-3">
            <div class="card h-100 text-center p-4">
                <div class="mb-2" style="font-size: 2.5rem;">
                    <i class="bi bi-people"></i>
                </div>
                <h5 class="card-title">Заказчики</h5>
                <a href="/buyers" class="btn btn-outline-primary">Перейти</a>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card h-100 text-center p-4">
                <div class="mb-2" style="font-size: 2.5rem;">
                    <i class="bi bi-truck"></i>
                </div>
                <h5 class="card-title">Автомобили</h5>
                <a href="/cars" class="btn btn-outline-primary">Перейти</a>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card h-100 text-center p-4">
                <div class="mb-2" style="font-size: 2.5rem;">
                    <i class="bi bi-wrench"></i>
                </div>
                <h5 class="card-title">Работы</h5>
                <a href="/work-types" class="btn btn-outline-primary">Перейти</a>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card h-100 text-center p-4">
                <div class="mb-2" style="font-size: 2.5rem;">
                    <i class="bi bi-gear"></i>
                </div>
                <h5 class="card-title">Запчасти</h5>
                <a href="/parts" class="btn btn-outline-primary">Перейти</a>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card h-100">
                <div class="card-body d-flex flex-column justify-content-center align-items-center">
                    <i class="bi bi-file-earmark-text display-4 mb-2"></i>
                    <h5 class="card-title">Договоры</h5>
                    <a href="/contracts" class="btn btn-outline-primary mt-2">Перейти</a>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card h-100">
                <div class="card-body d-flex flex-column justify-content-center align-items-center">
                    <i class="bi bi-clipboard-check display-4 mb-2"></i>
                    <h5 class="card-title">Акты осмотра</h5>
                    <a href="/inspection-acts" class="btn btn-outline-primary mt-2">Перейти</a>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card h-100">
                <div class="card-body d-flex flex-column justify-content-center align-items-center">
                    <i class="bi bi-search display-4 mb-2"></i>
                    <h5 class="card-title">Экспертизы</h5>
                    <a href="/examinations" class="btn btn-outline-primary mt-2">Перейти</a>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card h-100">
                <div class="card-body d-flex flex-column justify-content-center align-items-center">
                    <i class="bi bi-journal-text display-4 mb-2"></i>
                    <h5 class="card-title">Заказ-наряды</h5>
                    <a href="/orders" class="btn btn-outline-primary mt-2">Перейти</a>
                </div>
            </div>
        </div>
    </div>
</div> 