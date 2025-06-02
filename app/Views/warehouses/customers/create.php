<div class="container mt-4">
    <h2>Добавить клиента</h2>
    <form method="post" action="/orders/customers/store">
        <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" id="is_individual" name="is_individual" value="1">
            <label class="form-check-label" for="is_individual">Физическое лицо</label>
        </div>
        <div class="mb-3">
            <label for="company_name" class="form-label">Наименование клиента</label>
            <input type="text" class="form-control" id="company_name" name="company_name">
        </div>
        <div class="mb-3">
            <label for="contact_person" class="form-label">Контактное лицо</label>
            <input type="text" class="form-control" id="contact_person" name="contact_person" required>
        </div>
        <div class="mb-3">
            <label for="phone" class="form-label">Телефон</label>
            <input type="text" class="form-control" id="phone" name="phone">
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email">
        </div>
        <div class="mb-3">
            <label for="address" class="form-label">Адрес</label>
            <input type="text" class="form-control" id="address" name="address">
        </div>
        <div class="mb-3">
            <label for="description" class="form-label">Описание</label>
            <textarea class="form-control" id="description" name="description"></textarea>
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