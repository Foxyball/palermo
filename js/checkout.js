$(document).ready(function () {

  $("#checkout-form").on("submit", function (e) {
    e.preventDefault();

    const address = $("#order_address").val().trim();

    if (!address) {
      showAlert("Please enter your delivery address", "danger");
      $("#order_address").focus();
      return;
    }

    const $btn = $("#place-order-btn");
    const originalHtml = $btn.html();
    $btn.prop("disabled", true).addClass("loading").html("Processing...");

    $.ajax({
      url: $(this).attr("action"),
      method: "POST",
      data: $(this).serialize(),
      dataType: "json",
    })
      .done(function (response) {
        if (response.success) {
          setTimeout(function () {
            window.location.href = response.redirect || "thank-you";
          }, 1500);
        } else {
          $btn
            .prop("disabled", false)
            .removeClass("loading")
            .html(originalHtml);
          showAlert(
            response.message || "Failed to place order. Please try again.",
            "danger"
          );
        }
      })
      .fail(function (xhr, status, error) {
        $btn.prop("disabled", false).removeClass("loading").html(originalHtml);
        showAlert("An error occurred. Please try again.", "danger");
        console.error("Order submission error:", error);
      });
  });


  function showAlert(message, type) {
    $(".bootstrap-alert-container .alert").remove();

    if (!$(".bootstrap-alert-container").length) {
      $("body").append('<div class="bootstrap-alert-container"></div>');
    }

    const alertHtml = `
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;

    $(".bootstrap-alert-container").append(alertHtml);

    setTimeout(function () {
      $(".bootstrap-alert-container .alert").fadeOut(300, function () {
        $(this).remove();
      });
    }, 5000);
  }

  $("textarea").on("input", function () {
    this.style.height = "auto";
    this.style.height = this.scrollHeight + "px";
  });
});
