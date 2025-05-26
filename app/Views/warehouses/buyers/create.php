<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Добавить получателя</h5>
                    <a href="/warehouses/buyers" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-arrow-left"></i> К списку
                    </a>
                </div>
                <form method="post" action="/warehouses/api/buyers/create" class="card-body needs-validation" novalidate>
                    <div class="mb-3">
                        <label for="name" class="form-label">Наименование <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="contact_person" class="form-label">Контактное лицо</label>
                        <input type="text" class="form-control" id="contact_person" name="contact_person">
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
                        <label for="description" class="form-label">Примечание</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                    </div>
                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Сохранить
                        </button>
                        <a href="/warehouses/buyers" class="btn btn-outline-secondary ms-2">Отмена</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        if (!form.checkValidity()) {
            form.classList.add('was-validated');
            return;
        }
        const formData = new FormData(form);
        fetch('/warehouses/api/buyers/create', {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.href = '/warehouses/buyers?added=1';
            } else {
                showNotification('error', data.message || 'Ошибка при добавлении');
            }
        })
        .catch(() => {
            showNotification('error', 'Ошибка при отправке данных');
        });
    });
});
</script> 