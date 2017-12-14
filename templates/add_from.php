 <button class="modal__close" type="button" name="button" onclick="location.href = '/'; return false;">Закрыть</button>

<h2 class="modal__heading">Добавление задачи</h2>

<form class="form" action="index.php" method="post" enctype="multipart/form-data">
    <div class="form__row">

    <?php
    //проверка на наличие ошибок и присвоение сохраненных значений
        $class_error = isset($task_errors["Название"]) ? "form__input--error" : "";
        $value = isset($task["title"]) ? $task["title"] : "";
    ?>

        <label class="form__label" for="title">Название <sup>*</sup></label>
        <input class="form__input <?= $class_error ?>" type="text" name="title" id="title" value="<?= strip_tags($value) ?>" placeholder="Введите название">
    </div>

    <?php
        $class_error = isset($task_errors["Проект"]) ? "form__input--error" : "";
    ?>

    <div class="form__row">
        <label class="form__label" for="category">Проект <sup>*</sup></label>
        <select class="form__input form__input--select <?= $class_error ?>" name="category" id="category">
            <?php for($i = 1; $i < count($projects); $i++): ?>
            <option value="<?= strip_tags($projects[$i]) ?>"><?= strip_tags($projects[$i]) ?></option>
            <?php endfor; ?>
        </select>
    </div>

    <?php
        $value = isset($task["deadline_date"]) ? $task["deadline_date"] : "";
    ?>

    <div class="form__row">
        <label class="form__label" for="deadline_date">Дата выполнения</label>
        <input class="form__input form__input--date" type="date" name="deadline_date" id="deadline_date" value="<?= $value ?>" placeholder="Введите дату в формате ДД.ММ.ГГГГ">
    </div>

    <div class="form__row">
        <label class="form__label" for="preview">Файл</label>
        <div class="form__input-file">
            <input class="visually-hidden" type="file" name="preview" id="preview" value="">

            <label class="button button--transparent" for="preview">
                <span>Выберите файл</span>
            </label>
        </div>
    </div>

    <!--Вывод информации об ошибках-->
    <div class="form__row form__row--controls">
        <input class="button" type="submit" name="" value="Добавить">
        <?php if (isset($task_errors)): ?>
        <?php foreach($task_errors as $err => $val): ?>
        <p class="form__message"><strong><?=$err;?>:</strong> <?=$val;?></p>
        <?php endforeach; ?>
        <?php endif; ?>
    </div>

</form>