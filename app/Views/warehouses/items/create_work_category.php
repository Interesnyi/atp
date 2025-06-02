<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12 col-md-8 col-lg-6 mx-auto">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Добавить категорию работ</h5>
                </div>
                <div class="card-body">
                    <form action="/orders/work_categories/store" method="POST">
                        <div class="mb-3">
                            <label for="name" class="form-label">Наименование</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Описание</label>
                            <textarea class="form-control" id="description" name="description" rows="2"></textarea>
                        </div>
                        <button type="submit" class="btn btn-success">Сохранить</button>
                        <a href="/orders/work_categories" class="btn btn-secondary">Отмена</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div> 