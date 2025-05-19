
document.addEventListener("DOMContentLoaded", () => {
  const buttons = document.querySelectorAll("button");
  buttons.forEach(button => {
    button.addEventListener("click", () => {
      alert("This is a demo action.");
    });
  });
});
