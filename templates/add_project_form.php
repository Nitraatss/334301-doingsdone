 <button class="modal__close" type="button" name="button" onclick="location.href = '/'; return false;">Закрыть</button>

<h2 class="modal__heading">Добавление проекта</h2>

<form class="form" action="index.php" method="post" enctype="multipart/form-data">
    <div class="form__row">
        <?php
        //проверка на наличие ошибок
        $class_error = isset($project_errors["Название"]) ? "form__input--error" : "";
        ?>

        <label class="form__label" for="project">Проект <sup>*</sup></label>
        <input class="form__input <?=  $class_error ?>" type="text" name="project" id="project" value="" placeholder="Введите название проекта">
        
    </div>
    
    <!--Вывод информации об ошибках-->
    <div class="form__row form__row--controls">
        <input class="button" type="submit" name="" value="Добавить">

        <?php if (isset($project_errors)): ?>
        <?php foreach($project_errors as $err => $val): ?>
        <p class="form__message"><strong><?=$err;?>:</strong> <?=$val;?></p>
        <?php endforeach; ?>
        <?php endif; ?>
    </div>

</div>