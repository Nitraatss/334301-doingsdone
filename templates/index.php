
<h2 class="content__main-heading">Список задач</h2>

<form class="search-form" action="index.html" method="post">
    <input class="search-form__input" type="text" name="" value="" placeholder="Поиск по задачам">

    <input class="search-form__submit" type="submit" name="" value="Искать">
</form>

<div class="tasks-controls">
    <nav class="tasks-switch">
        <a href="/" class="tasks-switch__item tasks-switch__item--active">Все задачи</a>
        <a href="/" class="tasks-switch__item">Повестка дня</a>
        <a href="/" class="tasks-switch__item">Завтра</a>
        <a href="/" class="tasks-switch__item">Просроченные</a>
    </nav>

    <label class="checkbox">
        <a href="/">
            <input class="checkbox__input visually-hidden" type="checkbox">
            <span class="checkbox__text">Показывать выполненные</span>
        </a>
    </label>
</div>

<table class="tasks">
    <?php foreach ($tasks as $key2 => $value2): ?>
    <tr
    class = "tasks__item task <?php if($value2["is_done"]=="Да"): ?> task--completed <?php endif ?>"
    >
        <td class="task__select">
            <label class="checkbox task__checkbox">
            <input class="checkbox__input visually-hidden" type="checkbox">
            <a href="/"><span class="checkbox__text"><?= $value2["title"] ?></span></a>
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