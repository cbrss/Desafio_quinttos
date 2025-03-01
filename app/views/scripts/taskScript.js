document.addEventListener("DOMContentLoaded", () => {
    document.querySelector("#add-button").addEventListener("click", () => {
        let title = document.querySelector("#input-title");
        let description = document.querySelector("#input-description");

        if (title.value.trim() === "") {
            alert("Error: La tarea no puede estar vacia");
            return;
        }
        if (description.value.trim() === "") {
            alert("Error: La descripcion no puede estar vacia");
            return;
        }
        fetch("tasks", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ title: title.value, description: description.value })
        }).then(() => location.reload());
    });

    window.addEventListener("click", (e) => {
        let modal = document.getElementById("modal-description")
        if (e.target == modal) {
            modal.style.display = "none";
        }
    })

    document.querySelectorAll(".show-button").forEach(button => {
        button.addEventListener("click", () => {
            let id = button.dataset.id;
            fetch("tasks/" + id)
                .then(response => response.json())
                .then(data => {
                    let task = data.task;
                    let descripcionTexto = document.getElementById("description-text");
                    let modal = document.getElementById("modal-description");

                    descripcionTexto.innerText = task.description || "No hay descripcion.";
                    modal.style.display = "block";
                });
        });
    });

    document.querySelectorAll(".completed-button").forEach(button => {
        button.addEventListener("click", () => {
            let id = button.dataset.id;

            fetch("tasks/" + id)
                .then(response => response.json())
                .then(data => {
                    let task = data.task

                    task.status = task.status == "in_progress" ? "completed" : "in_progress";
                    
                    fetch("tasks/" + id, {
                        method: "PUT",
                        headers: { "Content-Type": "application/json" },
                        body: JSON.stringify(task)
                    }).then(() => location.reload());
                });
        });
    });

    document.querySelectorAll(".del-button").forEach(button => {
        button.addEventListener("click", () => {
            let id = button.dataset.id;
            fetch("tasks/" + id, { method: "DELETE" }).then(() => location.reload());
        });
    });
});
