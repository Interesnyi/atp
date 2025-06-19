<?php /** @var array $entity */ ?>
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12 col-md-8 col-lg-6 mx-auto">
            <div class="card mb-4">
                <div class="card-header pb-0">
                    <h4 class="mb-0">Редактировать юридическое лицо</h4>
                </div>
                <div class="card-body">
                    <form method="post" action="/legal-entities/update/<?= $entity['id'] ?>">
                        <div class="mb-3">
                            <label for="name" class="form-label">Название</label>
                            <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($entity['name']) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="inn" class="form-label">ИНН</label>
                            <input type="text" class="form-control" id="inn" name="inn" value="<?= htmlspecialchars($entity['inn']) ?>">
                        </div>
                        <div class="mb-3">
                            <label for="kpp" class="form-label">КПП</label>
                            <input type="text" class="form-control" id="kpp" name="kpp" value="<?= htmlspecialchars($entity['kpp']) ?>">
                        </div>
                        <div class="mb-3">
                            <label for="address" class="form-label">Адрес</label>
                            <input type="text" class="form-control" id="address" name="address" value="<?= htmlspecialchars($entity['address']) ?>">
                        </div>
                        <div class="mb-3">
                            <label for="phone" class="form-label">Телефон</label>
                            <input type="text" class="form-control" id="phone" name="phone" value="<?= htmlspecialchars($entity['phone']) ?>">
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($entity['email']) ?>">
                        </div>
                        <button type="submit" class="btn btn-success">Сохранить</button>
                        <a href="/legal-entities" class="btn btn-secondary">Отмена</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div> 