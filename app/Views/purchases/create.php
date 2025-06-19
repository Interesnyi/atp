<div class="container mt-4">
    <h1>Добавить закупку</h1>
    <form id="purchaseForm" method="post" action="/purchases/store">
        <div class="row mb-3">
            <div class="col-md-12">
                <label for="comment" class="form-label">Комментарий</label>
                <textarea class="form-control" id="comment" name="comment" rows="2"></textarea>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-12 d-flex justify-content-between">
                <a href="/purchases" class="btn btn-secondary">Отмена</a>
                <button type="submit" class="btn btn-primary">Создать закупку</button>
            </div>
        </div>
    </form>
</div>

<script src="/assets/js/app.js"></script> 