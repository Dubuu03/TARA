const dropdownTriggers = document.querySelectorAll(".dropdown-trigger");
window.addEventListener("scroll", () => {
  const scrollTopBtn = document.querySelector(".scroll-top-btn");

  if (window.scrollY) {
    scrollTopBtn.style.display = "flex";
  } else {
    scrollTopBtn.style.display = "none";
  }
});

const closeAllDropDowns = () => {
  dropdownTriggers.forEach((dropdownTrigger) => {
    dropdownTrigger.classList.remove("active-custom-dropdown");
  });
  document.querySelectorAll(".dropdown-content").forEach((dropdownContent) => {
    dropdownContent.style.display = "none";
  });
};

document.addEventListener("click", closeAllDropDowns);

dropdownTriggers.forEach((dropdownTrigger) => {
  const dropdownContent = dropdownTrigger.dataset.dropdownContent;

  // add click function to dropdowns
  dropdownTrigger.addEventListener("click", (e) => {
    e.stopPropagation();
    closeAllDropDowns();
    dropdownTrigger.classList.add("active-custom-dropdown");

    // show dropdown content based on their data attribute
    document.getElementById(dropdownContent).style.display = "flex";
  });
});

$("#saveChangesBtn").click(function () {
  $("#editProfile").click();
});

// function to show password
document
  .querySelectorAll("#show-login-password, #show-signup-password")
  .forEach((checkbox) => {
    checkbox.addEventListener("change", (e) => {
      const passwordInput = document.getElementById(
        e.target.dataset.checkboxFor
      );

      if (e.target.checked) {
        passwordInput.type = "text";
      } else {
        passwordInput.type = "password";
      }
    });
  });

document.querySelectorAll(".cancel-car-rental-btn").forEach((button) => {
  const carRentalID = button.dataset.rentalId;

  button.addEventListener("click", () => {
    document.getElementById("cancelCarBookingID").value = carRentalID;
  });
});

document.querySelectorAll(".cancel-hotel-booking-btn").forEach((button) => {
  const hotelBookingID = button.dataset.bookingId;

  button.addEventListener("click", () => {
    document.getElementById("cancelHotelBookingID").value = hotelBookingID;
  });
});

document.querySelectorAll(".cancel-booking-btn").forEach((button) => {
  const bookingId = button.dataset.bookingId;

  button.addEventListener("click", () => {
    document.getElementById("cancelBookingID").value = bookingId;
  });
});

document.querySelector(".menu").addEventListener("click", (e) => {
  document.querySelector(".custom-nav").classList.toggle("custom-nav-show");
  document.querySelector(".close-nav").classList.toggle("show-close-nav");
});

document.querySelector(".close-nav").addEventListener("click", (e) => {
  document.querySelector(".custom-nav").classList.remove("custom-nav-show");
  document.querySelector(".close-nav").classList.remove("show-close-nav");
});
