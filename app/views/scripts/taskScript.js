document.addEventListener("DOMContentLoaded", () => {

    window.addEventListener("click", (e) => {
        let modal = document.getElementById("modal-description")
        if (e.target == modal) {
            modal.style.display = "none";
        }
    })
    
    document.querySelectorAll(".show-button").forEach(button => {
        button.addEventListener("click", () => {
            let id = button.dataset.id;
            disableButtons();
            
            fetch(`/tasks/${id}`, {
                method: "GET",
                headers: {
                    "Content-Type": "application/json",
                }
            })
            .then(response => {
                handleErrorStatus(response.status);
                return response.json()
            })
            .then(data => {
                let task = data.task;
                let descripcionText = document.getElementById("description-text");
                let modal = document.getElementById("modal-description");
                descripcionText.innerText = task.description;
                modal.style.display = "block";
            })
            .catch(error => console.error("Fetch error:", error))
            .finally(() => enableButtons())

        });
    });
    
    document.querySelectorAll(".add-button").forEach(button => {
        button.addEventListener("click", () => {
            let title = document.querySelector("#input-title");
            let description = document.querySelector("#input-description");

            const validateTaskTitle = /^[a-zA-Z0-9 ]{1,255}$/; 
            const validateTaskDescription = /^.{1,255}$/s;
            
            if (!validateTaskTitle.test(title.value)) {
                alert("Error: El titulo debe tener entre 1 y 255 caracteres y solo puede contener letras y numeros");
                return;
            }
            if (!validateTaskDescription.test(description.value)) {
                alert("Error: La descripcion debe tener entre 1 y 255 caracteres");
                return;
            }
            disableButtons();

            fetch("/tasks", {
                method: "POST",
                headers: { 
                    "Content-Type": "application/json",
                 },
                body: JSON.stringify({ title: title.value, description: description.value })
            })
            .then(response => {
                handleErrorStatus(response.status);
            })
            .then(() => location.reload())
            .catch(error => console.error("Fetch error:", error));
        })
    });

    document.querySelectorAll(".completed-button").forEach(button => {
        button.addEventListener("click", () => {
            let id = button.dataset.id;
            disableButtons();

            fetch(`/tasks/${id}`, {
                method: "GET",
                headers: {
                    "Content-Type": "application/json",
                }
            })
            .then(response => {
                handleErrorStatus(response.status); 
                return response.json();
            })
            .then(data => {
                    let task = data.task;
                    task.status = task.status == "in_progress" ? "completed" : "in_progress";
                    fetch('/tasks',{
                        method: "PUT",
                        headers: { 
                            "Content-Type": "application/json",
                        },
                        body: JSON.stringify(task)
                    })
                    .then(response => {
                        handleErrorStatus(response.status)
                        return response.json();
                    })
                    .then(() => location.reload())
                    .catch(error => console.error("Fetch error:", error));
                })
            .catch(error => console.error("Fetch error:", error));

        });
    });

    document.querySelectorAll(".del-button").forEach(button => {
        button.addEventListener("click", () => {
            let id = button.dataset.id;
            disableButtons();
            
            fetch('/tasks',{ 
                    method: "DELETE",
                    headers: {
                        "Content-Type": "application/json",
                    },
                    body: JSON.stringify({"id": id})
            })
            .then(response => {
                handleErrorStatus(response.status);
                return response.json();
            })
            .then(() => location.reload())
            .catch(error => console.error("Fetch error:", error));
            })
            

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

    function handleErrorStatus(status) 
    {
        msg = ""
        if (status === 400) {
            msg += "Peticion invalida";
        }
        else if (status === 401) {
            msg += "Error en los campos de la peticion";
        } else if (status === 403) {
            localStorage.removeItem("authToken");
            localStorage.removeItem("tokenExpiration");

            alert("Tu sesion ha expirado. Por favor, inicia sesion nuevamente.");
            window.location.href = "/users/login";
            return;
        } else if (status === 404 ) {
            msg += "Recurso no encontrado";
        } else if (status === 409) {
            msg += "Conflicto con la solicitud";
        } else if (status === 500) {
            msg += "Error con el servidor";
        }
        if (msg != "" ){
            alert(msg + ". Consulte la consola para mayor informacion.")
        }
    }

});
