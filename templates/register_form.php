<h2 class="content__main-heading">Регистрация аккаунта</h2>

    <form class="form" action="index.php" method="post">
    <div class="form__row">
        <label class="form__label" for="email">E-mail <sup>*</sup></label>

        
        <?php
        //проверка на наличие ошибок и присвоение сохраненных значений
        $class_error = isset($registration_errors["Email"]) ? "form__input--error" : "";
        $value = isset($registration_data["email"]) ? $registration_data["email"] : "";
        ?>
        <input class="form__input <?= $class_error ?>" type="text" name="email" id="email" value="<?= $value ?>" placeholder="Введите e-mail">
        <!--Вывод ошибки-->
        <?php if (isset($registration_errors)): ?>
        <?php foreach($registration_errors as $err => $val): ?>
        <?php if($err=="Email"): ?>
        <p class="form__message"></strong> <?=$val;?></p>
        <?php endif ?>
        <?php endforeach; ?>
        <?php endif; ?>

    </div>

    <div class="form__row">
        <label class="form__label" for="password">Пароль <sup>*</sup></label>
        <?php
        //проверка на наличие ошибок и присвоение сохраненных значений
        $class_error = isset($registration_errors["Пароль"]) ? "form__input--error" : "";
        $value = isset($registration_data["password"]) ? $registration_data["password"] : "";
        ?>
        <input class="form__input <?= $class_error ?>" type="password" name="password" id="password" value="<?= $value ?>" placeholder="Введите пароль">
        <!--Вывод ошибки-->
        <?php if (isset($registration_errors)): ?>
        <?php foreach($registration_errors as $err => $val): ?>
        <?php if($err=="Пароль"): ?>
        <p class="form__message"></strong> <?=$val;?></p>
        <?php endif ?>
        <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <div class="form__row">
        <label class="form__label" for="name">Имя <sup>*</sup></label>
        <?php
        //проверка на наличие ошибок и присвоение сохраненных значений
        $class_error = isset($registration_errors["Имя"]) ? "form__input--error" : "";
        $value = isset($registration_data["name"]) ? $registration_data["name"] : "";
        ?>
        <input class="form__input <?= $class_error ?>" type="password <?= $class_error ?>" name="name" id="name" value="<?= $value ?>" placeholder="Введите имя">
        <?php if (isset($registration_errors)): ?>
        <?php foreach($registration_errors as $err => $val): ?>
        <?php if($err=="Имя"): ?>
        <p class="form__message"></strong> <?=$val;?></p>
        <?php endif ?>
        <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <div class="form__row form__row--controls">
        <?php if (isset($registration_errors)): ?>
        <p class="error-message">Пожалуйста, исправьте ошибки в форме</p>
        <?php endif; ?>

        <input class="button" type="submit" name="" value="Зарегистрироваться">
    </div>
</form>