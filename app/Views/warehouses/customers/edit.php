<?php // @var $customer array ?>
<div class="container mt-4">
    <h2>Редактировать клиента</h2>
    <form method="post" action="/orders/customers/update/<?= $customer['id'] ?>">
        <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" id="is_individual" name="is_individual" value="1" <?= !empty($customer['is_individual']) ? 'checked' : '' ?>>
            <label class="form-check-label" for="is_individual">Физическое лицо</label>
        </div>
        <div class="mb-3">
            <label for="company_name" class="form-label">Наименование клиента</label>
            <input type="text" class="form-control" id="company_name" name="company_name" value="<?= htmlspecialchars($customer['company_name']) ?>">
        </div>
        <div class="mb-3">
            <label for="contact_person" class="form-label">Контактное лицо</label>
            <input type="text" class="form-control" id="contact_person" name="contact_person" value="<?= htmlspecialchars($customer['contact_person']) ?>" required>
        </div>
        <div class="mb-3">
            <label for="phone" class="form-label">Телефон</label>
            <input type="text" class="form-control" id="phone" name="phone" value="<?= htmlspecialchars($customer['phone']) ?>">
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($customer['email']) ?>">
        </div>
        <div class="mb-3">
            <label for="address" class="form-label">Адрес</label>
            <input type="text" class="form-control" id="address" name="address" value="<?= htmlspecialchars($customer['address']) ?>">
        </div>
        <div class="mb-3">
            <label for="description" class="form-label">Описание</label>
            <textarea class="form-control" id="description" name="description"><?= htmlspecialchars($customer['description']) ?></textarea>
        </div>
        <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" id="is_internal" name="is_internal" value="1" <?= !empty($customer['is_internal']) ? 'checked' : '' ?>>
            <label class="form-check-label" for="is_internal">Внутренний клиент</label>
        </div>
        <button type="submit" class="btn btn-primary">Сохранить</button>
        <a href="/orders/customers" class="btn btn-secondary">Отмена</a>
    </form>
</div>
<script>
const isIndividual = document.getElementById('is_individual');
const companyName = document.getElementById('company_name');
const contactPerson = document.getElementById('contact_person');

function updateCompanyNameRequired() {
    if (isIndividual.checked) {
        companyName.required = false;
        companyName.value = contactPerson.value;
    } else {
        companyName.required = true;
    }
}

isIndividual.addEventListener('change', updateCompanyNameRequired);
contactPerson.addEventListener('input', function() {
    if (isIndividual.checked) {
        companyName.value = this.value;
    }
});
window.addEventListener('DOMContentLoaded', updateCompanyNameRequired);
</script> 