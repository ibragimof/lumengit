<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Index view</title>
</head>
<body>
<h3>Список API функций:</h3>

<h4>Ветки:</h4>
<ul>
    <li>
        <a href="<?= route('branch.list', ['repository' => 'jquery']) ?>">Список веток в репозитории</a>
    </li>
    <li>
        <a href="<?= route('branch.current', ['repository' => 'jquery']) ?>">Текущая активная ветка в репозитории</a>
    </li>
</ul>
</body>
</html>