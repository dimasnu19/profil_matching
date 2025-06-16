document.addEventListener("DOMContentLoaded", function () {
  console.log("Futsal Selection App Loaded");

  // Initialize offcanvas
  const sidebar = document.getElementById("sidebarMenu");
  if (sidebar) {
    const bsOffcanvas = new bootstrap.Offcanvas(sidebar);

    // Close sidebar when a nav link is clicked
    const navLinks = document.querySelectorAll(".offcanvas .nav-link");
    navLinks.forEach((link) => {
      link.addEventListener("click", () => {
        bsOffcanvas.hide();
      });
    });

    // Debug: Log nav links to ensure they are rendered
    console.log("Nav links found:", navLinks.length);
    navLinks.forEach((link) => console.log("Link text:", link.textContent));
  } else {
    console.error("Sidebar element not found");
  }
});
