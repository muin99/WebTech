var btn = document.getElementsByClassName("btn");
var display = document.getElementById("display");

for (var i = 0; i < btn.length; i++) {
    btn[i].onclick = function (e) {
        handleInput(e.target);
    }
}

document.addEventListener("keydown", function (e) {
    var key = e.key;
    if ("0123456789+-*/().".includes(key)) {
        display.value += key;
    } else if (key === "Enter") {
        calculate();
    } else if (key === "Escape" || key === "Backspace") {
        display.value = "";
    }
});

function handleInput(target) {
    var value = target.getAttribute("data-value");
    if (value) {
        display.value += value;
    } else if (target.id === "clear") {
        display.value = "";
    } else if (target.id === "equals") {
        calculate();
    }
}

function calculate() {
    try {
        display.value = eval(display.value);
    } catch {
        display.value = "Error";
    }
}