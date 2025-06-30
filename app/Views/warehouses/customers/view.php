<?php // @var $customer array ?>
<div class="container mt-4">
    <!-- Хлебные крошки -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">Главная</a></li>
            <li class="breadcrumb-item"><a href="/orders/customers">Клиенты</a></li>
            <li class="breadcrumb-item active" aria-current="page">Просмотр</li>
        </ol>
    </nav>
    <h2>Клиент: <?= htmlspecialchars($customer['company_name'] ?: $customer['contact_person']) ?></h2>
    <table class="table table-bordered w-auto">
        <tr><th>Тип</th><td><?= !empty($customer['is_individual']) ? 'Физическое лицо' : 'Юридическое лицо' ?></td></tr>
        <tr><th>Наименование клиента</th><td><?= htmlspecialchars($customer['company_name']) ?></td></tr>
        <tr><th>Контактное лицо</th><td><?= htmlspecialchars($customer['contact_person']) ?></td></tr>
        <tr><th>Должность</th><td><?= htmlspecialchars($customer['position'] ?? '') ?></td></tr>
        <tr><th>Телефон</th><td><?= htmlspecialchars($customer['phone']) ?></td></tr>
        <tr><th>Email</th><td><?= htmlspecialchars($customer['email']) ?></td></tr>
        <tr><th>Адрес</th><td><?= htmlspecialchars($customer['address']) ?></td></tr>
        <tr><th>ИНН</th><td><?= htmlspecialchars($customer['inn'] ?? '') ?></td></tr>
        <tr><th>ОГРН / ОГРНИП</th><td><?= htmlspecialchars($customer['ogrn'] ?? '') ?></td></tr>
        <tr><th>Наименование банка</th><td><?= htmlspecialchars($customer['bank_name'] ?? '') ?></td></tr>
        <tr><th>БИК</th><td><?= htmlspecialchars($customer['bik'] ?? '') ?></td></tr>
        <tr><th>Расчётный счёт</th><td><?= htmlspecialchars($customer['account_number'] ?? '') ?></td></tr>
        <tr><th>Корреспондентский счёт</th><td><?= htmlspecialchars($customer['correspondent_account'] ?? '') ?></td></tr>
        <tr><th>Карточка организации</th><td><?php if (!empty($customer['org_card_file'])): ?><a href="<?= htmlspecialchars($customer['org_card_file']) ?>" target="_blank">Скачать</a><?php else: ?>—<?php endif; ?></td></tr>
    </table>
    <a href="/orders/customers/edit/<?= $customer['id'] ?>" class="btn btn-warning">Редактировать</a>
    <a href="/orders/customers" class="btn btn-secondary">Назад</a>
</div> 