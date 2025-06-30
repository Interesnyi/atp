<?php // @var $customers array ?>
<div class="container mt-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">Главная</a></li>
            <li class="breadcrumb-item"><a href="/contracts">Договоры</a></li>
            <li class="breadcrumb-item active" aria-current="page">Новый договор</li>
        </ol>
    </nav>
    <h2>Новый договор</h2>
    <form method="post" action="/contracts/store" enctype="multipart/form-data">
        <div class="row">
            <div class="col-md-4 mb-3">
                <label for="customer_id" class="form-label">Заказчик</label>
                <select class="form-select" id="customer_id" name="customer_id" required>
                    <option value="">Выберите заказчика</option>
                    <?php foreach ($customers as $c): ?>
                        <option value="<?= $c['id'] ?>"><?= !empty($c['company_name']) ? htmlspecialchars($c['company_name']) : htmlspecialchars($c['contact_person']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4 mb-3">
                <label for="contact_person_genitive" class="form-label">Контактное лицо (родительный падеж)</label>
                <input type="text" class="form-control" id="contact_person_genitive" name="contact_person_genitive" placeholder="например: Гусева Игоря Владимировича" required>
            </div>
            <div class="col-md-4 mb-3">
                <label for="contract_number" class="form-label">Номер договора</label>
                <input type="text" class="form-control" id="contract_number" name="contract_number" required>
            </div>
            <div class="col-md-4 mb-3">
                <label for="contract_date" class="form-label">Дата договора</label>
                <input type="date" class="form-control" id="contract_date" name="contract_date" required>
            </div>
        </div>
        <div class="mb-3">
            <label for="description" class="form-label">Описание</label>
            <textarea class="form-control" id="description" name="description" rows="2"></textarea>
        </div>
        <div class="mb-3">
            <label for="contract_file" class="form-label">Файл договора (PDF, JPG, PNG)</label>
            <input type="file" class="form-control" id="contract_file" name="contract_file" accept=".pdf,.jpg,.jpeg,.png">
        </div>
        <button type="submit" class="btn btn-primary">Сохранить</button>
        <a href="/contracts" class="btn btn-secondary">Отмена</a>
    </form>
</div> 