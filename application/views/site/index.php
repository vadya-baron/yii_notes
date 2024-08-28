<div class="site-index">
    <div class="jumbotron text-center bg-transparent mt-5 mb-5">
        <p>
            <?php if (Yii::$app->user->isGuest) : ?>
                <a class="btn btn-lg btn-success" href="/auth/login">Войти</a>
            <?php else : ?>
                <a class="btn btn-lg btn-success mb-3" href="/notes">Мои заметки</a><br/>
                <a class="btn btn-lg btn-success mb-3" href="/notes/store">Добавить заметку</a><br/>
                <a class="btn btn-lg btn-success mb-3" href="/notes/store-tag">Добавить тег</a><br/>
            <?php endif; ?>
        </p>
    </div>
</div>
