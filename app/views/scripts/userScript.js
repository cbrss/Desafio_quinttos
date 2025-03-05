document.addEventListener("DOMContentLoaded", () => {
    const loginForm = document.getElementById("login-form");
    const registerForm = document.getElementById("register-form");
    loginForm.addEventListener("submit", (event) => handleFormSubmit(event, "login"));
    registerForm.addEventListener("submit", (event) => handleFormSubmit(event, "register"));

    document.querySelectorAll(".go-to-auth").forEach(button => {
        button.addEventListener("click", (event) => {
            event.preventDefault();
            const target = button.dataset.target;

            if (target === "register") {
                loginForm.style.display = "none";
                registerForm.style.display = "block";
            } else {
                loginForm.style.display = "block";
                registerForm.style.display = "none";
            }
        });
    });

    function handleFormSubmit(event, formType) {
        event.preventDefault(); 

        const formData = new FormData(event.target);
        const data = Object.fromEntries(formData.entries());

        if (Object.values(data).some(value => value.trim() === "")) {
            alert("Error: Completa todos los campos.");
            return;
        }
        if (formType === "register") {
            const usernameRegex = /^[a-zA-Z0-9]{3,20}$/;
            const passwordRegex = /^.{6,}$/;
            
            if (!usernameRegex.test(data.username)) {
                alert("Error: El nombre de usuario debe tener entre 3 y 20 caracteres y solo contener letras y numeros.");
                return;
            }
            
            if (!passwordRegex.test(data.password)) {
                alert("Error: La contraseña debe tener al menos 6 caracteres.");
                return;
            }
        }

        fetch(`/users/${formType}`, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(data) 
        })
        .then(response => {
            handleErrorStatus(response.status);
            return response.json();

        })
        .then(result => {
            if (result.success) {
                if (formType == 'register') {
                    alert("Usuario registrado exitosamente.");
                    window.location.href = "/users/home";
                } else {
                    localStorage.setItem("authToken", result.token);
                    window.location.href = "/tasks/list";
                }
            }
        })
        .catch(error => console.error("Fetch error:", error));
    }

    function handleErrorStatus(status) 
    {
        msg = ""
        if (status === 400) {
            msg += "Peticion invalida";
        }
        else if (status === 401) {
            msg += "Usuario invalido.";
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
            alert(msg)
        }
    }
});
