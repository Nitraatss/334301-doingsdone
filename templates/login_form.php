<button class="modal__close" type="button" name="button" onclick="location.href = '/'; return false;">Закрыть</button>

<h2 class="modal__heading">Вход на сайт</h2>

<form class="form" action="index.php" method="post">

    <div class="form__row">
        <?php 
            //проверка на наличие ошибок и присвоение сохраненных значений
            $class_error = isset($login_errors["Email"]) ? "form__input--error" : "";
            $value = isset($login_data["email"]) ? $login_data["email"] : "";
        ?>

        <label class="form__label" for="email">E-mail <sup>*</sup></label>
        <input class="form__input <?= $class_error ?>" type="text" name="email" id="email" value="<?= htmlspecialchars($value) ?>" placeholder="Введите e-mail">

        <!--Вывод ошибки-->
        <?php if (isset($login_errors)): ?>
        <?php foreach($login_errors as $err => $val): ?>
        <?php if($err=="Email"): ?>
        <p class="form__message"><strong><?=$err;?>:</strong> <?=$val;?></p>
        <?php endif ?>
        <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <div class="form__row">
        <?php 
            $class_error = isset($login_errors["Пароль"]) ? "form__input--error" : "";
            $value = isset($login_data["password"]) ? $login_data["password"] : "";
        ?>

        <label class="form__label" for="password">Пароль <sup>*</sup></label>
        <input class="form__input <?= $class_error ?>" type="password" name="password" id="password" value="<?= htmlspecialchars($value) ?>" placeholder="Введите пароль">

        <?php if (isset($login_errors)): ?>
        <?php foreach($login_errors as $err => $val): ?>
        <?php if($err=="Пароль"): ?>
        <p class="form__message"><strong><?=$err;?>:</strong> <?=$val;?></p>
        <?php endif ?>
        <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <div class="form__row form__row--controls">
        <input class="button" type="submit" name="" value="Войти">
    </div>
</form>