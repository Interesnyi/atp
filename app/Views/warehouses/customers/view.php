<?php // @var $customer array ?>
<div class="container mt-4">
    <h2>Клиент: <?= htmlspecialchars($customer['company_name'] ?: $customer['contact_person']) ?></h2>
    <table class="table table-bordered w-auto">
        <tr><th>Тип</th><td><?= !empty($customer['is_individual']) ? 'Физическое лицо' : 'Юридическое лицо' ?></td></tr>
        <tr><th>Наименование клиента</th><td><?= htmlspecialchars($customer['company_name']) ?></td></tr>
        <tr><th>Контактное лицо</th><td><?= htmlspecialchars($customer['contact_person']) ?></td></tr>
        <tr><th>Телефон</th><td><?= htmlspecialchars($customer['phone']) ?></td></tr>
        <tr><th>Email</th><td><?= htmlspecialchars($customer['email']) ?></td></tr>
        <tr><th>Описание</th><td><?= htmlspecialchars($customer['description']) ?></td></tr>
    </table>
    <a href="/orders/customers/edit/<?= $customer['id'] ?>" class="btn btn-warning">Редактировать</a>
    <a href="/orders/customers" class="btn btn-secondary">Назад</a>
</div> 