
document.addEventListener("DOMContentLoaded", () => {
    window.addEventListener("click", (e) => {
        let modal = document.getElementById("modal-description")
        if (e.target == modal) {
            modal.style.display = "none";
        }
    })
    
    document.querySelectorAll(".add-button").forEach(button => {
        button.addEventListener("click", () => {
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
            disableButtons();
            fetch("tasks", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ title: title.value, description: description.value })
                })
                .then(() => location.reload())
        })
    });

    document.querySelectorAll(".show-button").forEach(button => {
        button.addEventListener("click", () => {
            let id = button.dataset.id;
            disableButtons();
            fetch("tasks/" + id)
                .then(response => response.json())
                .then(data => {
                    let task = data.task;
                    let descripcionText = document.getElementById("description-text");
                    let modal = document.getElementById("modal-description");

                    descripcionText.innerText = task.description;
                    modal.style.display = "block";
                })
                .finally(() => enableButtons());
        });
    });

    document.querySelectorAll(".completed-button").forEach(button => {
        button.addEventListener("click", () => {
            let id = button.dataset.id;
            disableButtons();
            fetch("tasks/" + id)
                .then(response => response.json())
                .then(data => {
                        let task = data.task;
                        task.status = task.status == "in_progress" ? "completed" : "in_progress";
                        fetch("tasks/" + id, {
                            method: "PUT",
                            headers: { "Content-Type": "application/json" },
                            body: JSON.stringify(task)
                        }).then(() => location.reload());
                    })
        });
    });

    document.querySelectorAll(".del-button").forEach(button => {
        button.addEventListener("click", () => {
            let id = button.dataset.id;
            disableButtons();
            fetch("tasks/" + id, { method: "DELETE" })
                .then(() => location.reload())
            });
    });

    function disableButtons() {
        document.querySelectorAll(".add-button, .completed-button, .del-button").forEach(button => {
            button.classList.add("disabled");
        });
    }
    function enableButtons() {
        document.querySelectorAll(".add-button, .completed-button, .del-button").forEach(button => {
            button.classList.remove("disabled");
        });
    }
});
