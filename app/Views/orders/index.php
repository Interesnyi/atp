<div class="container-fluid py-4">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">Главная</a></li>
            <li class="breadcrumb-item active" aria-current="page">Заказ-наряды</li>
        </ol>
    </nav>
    <?php if (!empty($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= $_SESSION['success']; unset($_SESSION['success']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    <?php if (!empty($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= $_SESSION['error']; unset($_SESSION['error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    <div class="card mb-4">
        <div class="card-header pb-0 d-flex flex-wrap gap-2 align-items-center justify-content-between">
            <h4 class="mb-0">Заказ-наряды</h4>
            <div>
                <a href="/orders/create" class="btn btn-success me-2"><i class="bi bi-plus"></i> Новый заказ-наряд</a>
                <a href="/orders/customers" class="btn btn-outline-primary me-2">Заказчики</a>
                <a href="/orders/cars" class="btn btn-outline-primary me-2">Автомобили</a>
                <a href="/orders/work_types" class="btn btn-outline-primary me-2">Работы</a>
                <a href="/inspection-acts" class="btn btn-outline-primary me-2">Акты осмотра</a>
                <a href="/contracts" class="btn btn-outline-primary">Договоры</a>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Номер</th>
                            <th>Клиент</th>
                            <th>Авто</th>
                            <th>Дата создания</th>
                            <th>Статус</th>
                            <th class="text-center">Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order): ?>
                            <tr>
                                <td><?= $order['id'] ?></td>
                                <td><?= htmlspecialchars($order['order_number']) ?></td>
                                <td><?= htmlspecialchars($order['customer_name']) ?></td>
                                <td><?= htmlspecialchars($order['brand']) ?> <?= htmlspecialchars($order['model']) ?> (<?= htmlspecialchars($order['license_plate']) ?>)</td>
                                <td><?= htmlspecialchars($order['date_created']) ?></td>
                                <td><?= htmlspecialchars($order['status']) ?></td>
                                <td class="text-center">
                                    <a href="/orders/view/<?= $order['id'] ?>" class="btn btn-sm btn-outline-primary me-1" title="Просмотр"><i class="bi bi-eye"></i></a>
                                    <a href="/orders/edit/<?= $order['id'] ?>" class="btn btn-sm btn-outline-secondary me-1" title="Редактировать"><i class="bi bi-pencil"></i></a>
                                    <form method="post" action="/orders/delete/<?= $order['id'] ?>" style="display:inline-block;" onsubmit="return confirm('Удалить заказ-наряд?');">
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Удалить"><i class="bi bi-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div> 