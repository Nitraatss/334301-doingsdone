<h2 class="content__main-heading">Список задач</h2>

<form class="search-form" action="index.html" method="post">
    <input class="search-form__input" type="text" name="" value="" placeholder="Поиск по задачам">

    <input class="search-form__submit" type="submit" name="" value="Искать">
</form>

<div class="tasks-controls">
    <nav class="tasks-switch">
        <a href="/index.php?task_switch=all" class="tasks-switch__item <?php if ($_GET["task_switch"]=='all' || !isset($_GET["task_switch"])):?> tasks-switch__item--active <?php endif; ?>">Все задачи</a>
        <a href="/index.php?task_switch=today" class="tasks-switch__item <?php if ($_GET["task_switch"]=='today'):?> tasks-switch__item--active <?php endif; ?>">Повестка дня</a>
        <a href="/index.php?task_switch=tomorrow" class="tasks-switch__item <?php if ($_GET["task_switch"]=='tomorrow'):?> tasks-switch__item--active <?php endif; ?>">Завтра</a>
        <a href="/index.php?task_switch=wasted" class="tasks-switch__item <?php if ($_GET["task_switch"]=='wasted'):?> tasks-switch__item--active <?php endif; ?>">Просроченные</a>
    </nav>

    <label class="checkbox">
        <!-- присваивание параметра при нажатии на чекбокс -->
        <a href="/index.php?show_completed=checked">
            <input class="checkbox__input visually-hidden" type="checkbox" <?= $check ?>>
            <span class="checkbox__text">Показывать выполненные</span>
        </a>
    </label>
</div>

<table class="tasks">
    <?php $tasks = isset($specific_tasks)?$specific_tasks:$tasks ?>
    <?php foreach ($tasks as $key2 => $value2): ?>

    <tr
    class = "tasks__item task <?php if($value2["is_done"]==true): ?> task--completed <?php endif ?>
    <?php if($value2["deadline_date"] < $current_date && $value2["is_done"]==false): ?> task--important <?php endif ?>"

    <?php 
        if($value2["is_done"]==true)
        {print("hidden");}
        if ($value2["is_done"]==true && $check=="checked")
        {print("hidden");}
    ?>
    >
        <td class="task__select">
            <label class="checkbox task__checkbox">
            <input class="checkbox__input visually-hidden" type="checkbox">
            <a href="/index.php?changestatus=<?= $key2 ?>"><span class="checkbox__text"><?= $value2["title"] ?></span></a>
            </label>
        </td>
        <td class="task__file">
            <a class="download-link" href="#">Home.psd</a>
        </td>
        <td class="task__date">
            <?= $value2["deadline_date"] ?>
        </td>
    </tr>
    <?php endforeach; ?>
</table>
