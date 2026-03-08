nam = document.getElementById("name").value;
email = document.getElementById("email").value;
department = document.getElementById("department").value;

function handleSubmit() {
    if(nam === "" || email === "" || department === "") {
        alert("Please fill in all fields.");
        return false;
    }
    else {
        alert("Form submitted successfully!");
        return true;
    }
}