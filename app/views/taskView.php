<?php

class TaskView {
    public function render($tasks) {
        ?>
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Lista de Tareas</title>
            <link rel="stylesheet" href="app/views/css/taskStyle.css">
            <script src="https://kit.fontawesome.com/a83aa45581.js" crossorigin="anonymous"></script>
        </head>
        <body>
            <div class="modal" id="modal-description">
                <div class="modal-content">
                    <h2 class="modal-title">Descripcion</h2>
                    <p class="modal-text" id="description-text"></p>
                </div>
            </div>

            <div class="container">
                <h1 >Lista de Tareas</h1>
                <div class="add-task">
                    <div class="add-task-title">
                        <input type="text" id="input-title" placeholder="Titulo">
                        <i class="fas fa-plus-circle add-button" ></i>
                    </div>
                    <input type="text" id="input-description" placeholder="Descripcion">

                 </div>
                
                <p id="error_tarea"></p>
                <?php if (isset($tasks) && count($tasks)> 0) {?>
                    <div class="task-section">
                        <h3>Tareas Pendientes</h3>
                        <ul>
                            <?php foreach ($tasks as $task): ?>
                                <li>
                                    <i class="<?= $task->status == 'completed' ? 'fas fa-check-circle' : 'far fa-circle' ?> co completed-button" data-id="<?= $task->id ?>"></i>
                                    <button class="text show-button <?= $task->status == 'completed' ? 'line-through' : '' ?>" 
                                        data-id="<?= $task->id ?>">
                                        <?= htmlspecialchars($task->title) ?>
                                    </button>                                
                                    <i class="fas fa-trash de del-button" data-id="<?= $task->id ?>"></i>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php } ?>
            </div>
            <script src="app/views/scripts/taskScript.js"></script>
        </body>
        </html>
        <?php
    }
}
?>