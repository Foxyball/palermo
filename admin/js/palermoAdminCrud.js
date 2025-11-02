$(function () {
  // Status update handler
  $(document).on("click", ".js-order-status-update-btn", function (e) {
    e.preventDefault();
    const $btn = $(this);
    const orderId = $btn.data("order-id");
    const currentStatusId = $btn.data("current-status-id");

    $("#order_id").val(orderId);
    $("#new_status").val(currentStatusId);
    $("#statusUpdateModal").modal("show");
  });

  // Confirm status update
  $("#confirmStatusUpdate").on("click", function () {
    const $btn = $(this);
    const orderId = $("#order_id").val();
    const newStatusId = $("#new_status").val();

    if (!orderId || !newStatusId) {
      toastr.error("Please select a status");
      return;
    }

    $btn.prop("disabled", true).text("Updating...");

    $.ajax({
      url: "./include/ajax_order_update_status.php",
      method: "POST",
      data: {
        id: orderId,
        status_id: newStatusId,
      },
      dataType: "json",
    })
      .done(function (resp) {
        if (resp && resp.success) {
          toastr.success("Order status updated successfully");
          $("#statusUpdateModal").modal("hide");
          // Reload the page to show updated status
          setTimeout(function () {
            location.reload();
          }, 1000);
        } else {
          toastr.error(resp && resp.message ? resp.message : "Update failed");
        }
      })
      .fail(function (xhr) {
        let msg = "Server error";
        if (xhr && xhr.responseJSON && xhr.responseJSON.message) {
          msg = xhr.responseJSON.message;
        }
        toastr.error(msg);
      })
      .always(function () {
        $btn.prop("disabled", false).text("Update Status");
      });
  });

  // SweetAlert2 delete handler
  $(document).on("click", ".js-order-delete-btn", async function (e) {
    e.preventDefault();
    const $btn = $(this);
    const orderId = $btn.data("order-id");
    const customerName = $btn.data("order-customer");

    const confirmed = await Swal.fire({
      title: "Delete Order?",
      html: `<p class="mb-1">You are about to delete order #${orderId} for <strong>${$(
        "<div>"
      )
        .text(customerName)
        .html()}</strong>.</p><small class="text-danger">This action cannot be undone.</small>`,
      icon: "warning",
      showCancelButton: true,
      confirmButtonText: "Yes, delete",
      cancelButtonText: "Cancel",
      confirmButtonColor: "#d33",
      reverseButtons: true,
      focusCancel: true,
    }).then((r) => r.isConfirmed);

    if (!confirmed) return;

    $btn.prop("disabled", true).addClass("opacity-50");

    $.ajax({
      url: "./include/ajax_order_delete.php",
      method: "POST",
      data: {
        id: orderId,
      },
      dataType: "json",
    })
      .done(function (resp) {
        if (resp && resp.success) {
          toastr.success("Order deleted");
          // Remove row from table
          const $row = $btn.closest("tr");
          $row.fadeOut(300, function () {
            $(this).remove();
          });
        } else {
          toastr.error(resp && resp.message ? resp.message : "Delete failed");
        }
      })
      .fail(function (xhr) {
        let msg = "Server error";
        if (xhr && xhr.responseJSON && xhr.responseJSON.message) {
          msg = xhr.responseJSON.message;
        }
        toastr.error(msg);
      })
      .always(function () {
        $btn.prop("disabled", false).removeClass("opacity-50");
      });
  });

  $(".js-order-status-toggle:not(:disabled)").on("change", function () {
    const $cb = $(this);
    const statusId = $cb.data("status-id");
    const originalChecked = !$cb.prop("checked");
    $cb.prop("disabled", true);

    $.ajax({
      url: "./include/ajax_order_toggle_status.php",
      method: "POST",
      data: {
        id: statusId,
      },
      dataType: "json",
    })
      .done(function (resp) {
        if (!resp || resp.success !== true) {
          $cb.prop("checked", originalChecked);
          toastr.error(
            resp && resp.message ? resp.message : "Failed to update status"
          );
        } else {
          toastr.success("Status updated");
        }
      })
      .fail(function (xhr) {
        $cb.prop("checked", originalChecked);
        let msg = "Network / server error";
        if (xhr && xhr.responseJSON && xhr.responseJSON.message) {
          msg = xhr.responseJSON.message;
        }
        toastr.error(msg);
      })
      .always(function () {
        $cb.prop("disabled", false);
      });
  });

  // SweetAlert2 delete handler
  $(document).on("click", ".js-order-status-delete-btn", async function (e) {
    e.preventDefault();
    const $btn = $(this);
    const statusId = $btn.data("status-id");
    const statusName = $btn.data("status-name");

    const confirmed = await Swal.fire({
      title: "Delete Order Status?",
      html: `<p class="mb-1">You are about to delete <strong>${$("<div>")
        .text(statusName)
        .html()}</strong>.</p><small class="text-danger">This action cannot be undone.</small>`,
      icon: "warning",
      showCancelButton: true,
      confirmButtonText: "Yes, delete",
      cancelButtonText: "Cancel",
      confirmButtonColor: "#d33",
      reverseButtons: true,
      focusCancel: true,
    }).then((r) => r.isConfirmed);

    if (!confirmed) return;

    $btn.prop("disabled", true).addClass("opacity-50");

    $.ajax({
      url: "./include/ajax_order_status_delete.php",
      method: "POST",
      data: {
        id: statusId,
      },
      dataType: "json",
    })
      .done(function (resp) {
        if (resp && resp.success) {
          toastr.success("Order status deleted");
          // Remove row from table
          const $row = $btn.closest("tr");
          $row.fadeOut(300, function () {
            $(this).remove();
          });
        } else {
          toastr.error(resp && resp.message ? resp.message : "Delete failed");
        }
      })
      .fail(function (xhr) {
        let msg = "Server error";
        if (xhr && xhr.responseJSON && xhr.responseJSON.message) {
          msg = xhr.responseJSON.message;
        }
        toastr.error(msg);
      })
      .always(function () {
        $btn.prop("disabled", false).removeClass("opacity-50");
      });
  });

  $(".js-category-status-toggle:not(:disabled)").on("change", function () {
    const $cb = $(this);
    const categoryId = $cb.data("category-id");
    const originalChecked = !$cb.prop("checked");
    $cb.prop("disabled", true);

    $.ajax({
      url: "./include/ajax_category_toggle_status.php",
      method: "POST",
      data: {
        id: categoryId,
      },
      dataType: "json",
    })
      .done(function (resp) {
        if (!resp || resp.success !== true) {
          $cb.prop("checked", originalChecked);
          toastr.error(
            resp && resp.message ? resp.message : "Failed to update status"
          );
        } else {
          toastr.success("Status updated");
        }
      })
      .fail(function (xhr) {
        $cb.prop("checked", originalChecked);
        let msg = "Network / server error";
        if (xhr && xhr.responseJSON && xhr.responseJSON.message) {
          msg = xhr.responseJSON.message;
        }
        toastr.error(msg);
      })
      .always(function () {
        $cb.prop("disabled", false);
      });
  });

  // SweetAlert2 delete handler
  $(document).on("click", ".js-category-delete-btn", async function (e) {
    e.preventDefault();
    const $btn = $(this);
    const categoryId = $btn.data("category-id");
    const categoryName = $btn.data("category-name");

    const confirmed = await Swal.fire({
      title: "Delete Category?",
      html: `<p class="mb-1">You are about to delete <strong>${$("<div>")
        .text(categoryName)
        .html()}</strong>.</p><small class="text-danger">This action cannot be undone.</small>`,
      icon: "warning",
      showCancelButton: true,
      confirmButtonText: "Yes, delete",
      cancelButtonText: "Cancel",
      confirmButtonColor: "#d33",
      reverseButtons: true,
      focusCancel: true,
    }).then((r) => r.isConfirmed);

    if (!confirmed) return;

    $btn.prop("disabled", true).addClass("opacity-50");

    $.ajax({
      url: "./include/ajax_category_delete.php",
      method: "POST",
      data: {
        id: categoryId,
      },
      dataType: "json",
    })
      .done(function (resp) {
        if (resp && resp.success) {
          toastr.success("Category deleted");
          // Remove row from table
          const $row = $btn.closest("tr");
          $row.fadeOut(300, function () {
            $(this).remove();
          });
        } else {
          toastr.error(resp && resp.message ? resp.message : "Delete failed");
        }
      })
      .fail(function (xhr) {
        let msg = "Server error";
        if (xhr && xhr.responseJSON && xhr.responseJSON.message) {
          msg = xhr.responseJSON.message;
        }
        toastr.error(msg);
      })
      .always(function () {
        $btn.prop("disabled", false).removeClass("opacity-50");
      });
  });

  $(".js-blog-category-status-toggle:not(:disabled)").on("change", function () {
    const $cb = $(this);
    const categoryId = $cb.data("category-id");
    const originalChecked = !$cb.prop("checked");
    $cb.prop("disabled", true);

    $.ajax({
      url: "./include/ajax_blog_category_toggle_status.php",
      method: "POST",
      data: {
        id: categoryId,
      },
      dataType: "json",
    })
      .done(function (resp) {
        if (!resp || resp.success !== true) {
          $cb.prop("checked", originalChecked);
          toastr.error(
            resp && resp.message ? resp.message : "Failed to update status"
          );
        } else {
          toastr.success("Status updated");
        }
      })
      .fail(function (xhr) {
        $cb.prop("checked", originalChecked);
        let msg = "Network / server error";
        if (xhr && xhr.responseJSON && xhr.responseJSON.message) {
          msg = xhr.responseJSON.message;
        }
        toastr.error(msg);
      })
      .always(function () {
        $cb.prop("disabled", false);
      });
  });

  // SweetAlert2 delete handler
  $(document).on("click", ".js-blog-category-delete-btn", async function (e) {
    e.preventDefault();
    const $btn = $(this);
    const categoryId = $btn.data("category-id");
    const categoryName = $btn.data("category-name");

    const confirmed = await Swal.fire({
      title: "Delete Blog Category?",
      html: `<p class="mb-1">You are about to delete <strong>${$("<div>")
        .text(categoryName)
        .html()}</strong>.</p><small class="text-danger">This action cannot be undone.</small>`,
      icon: "warning",
      showCancelButton: true,
      confirmButtonText: "Yes, delete",
      cancelButtonText: "Cancel",
      confirmButtonColor: "#d33",
      reverseButtons: true,
      focusCancel: true,
    }).then((r) => r.isConfirmed);

    if (!confirmed) return;

    $btn.prop("disabled", true).addClass("opacity-50");

    $.ajax({
      url: "./include/ajax_blog_category_delete.php",
      method: "POST",
      data: {
        id: categoryId,
      },
      dataType: "json",
    })
      .done(function (resp) {
        if (resp && resp.success) {
          toastr.success("Blog category deleted");
          // Remove row from table
          const $row = $btn.closest("tr");
          $row.fadeOut(300, function () {
            $(this).remove();
          });
        } else {
          toastr.error(resp && resp.message ? resp.message : "Delete failed");
        }
      })
      .fail(function (xhr) {
        let msg = "Server error";
        if (xhr && xhr.responseJSON && xhr.responseJSON.message) {
          msg = xhr.responseJSON.message;
        }
        toastr.error(msg);
      })
      .always(function () {
        $btn.prop("disabled", false).removeClass("opacity-50");
      });
  });

  $(".js-addon-status-toggle:not(:disabled)").on("change", function () {
    const $cb = $(this);
    const addonId = $cb.data("addon-id");
    const originalChecked = !$cb.prop("checked");
    $cb.prop("disabled", true);

    $.ajax({
      url: "./include/ajax_addon_toggle_status.php",
      method: "POST",
      data: {
        id: addonId,
      },
      dataType: "json",
    })
      .done(function (resp) {
        if (!resp || resp.success !== true) {
          $cb.prop("checked", originalChecked);
          toastr.error(
            resp && resp.message ? resp.message : "Failed to update status"
          );
        } else {
          toastr.success("Status updated");
        }
      })
      .fail(function (xhr) {
        $cb.prop("checked", originalChecked);
        let msg = "Network / server error";
        if (xhr && xhr.responseJSON && xhr.responseJSON.message) {
          msg = xhr.responseJSON.message;
        }
        toastr.error(msg);
      })
      .always(function () {
        $cb.prop("disabled", false);
      });
  });

  // SweetAlert2 delete handler
  $(document).on("click", ".js-addon-delete-btn", async function (e) {
    e.preventDefault();
    const $btn = $(this);
    const addonId = $btn.data("addon-id");
    const categoryName = $btn.data("category-name");

    const confirmed = await Swal.fire({
      title: "Delete Addon?",
      html: `<p class="mb-1">You are about to delete <strong>${$("<div>")
        .text(categoryName)
        .html()}</strong>.</p><small class="text-danger">This action cannot be undone.</small>`,
      icon: "warning",
      showCancelButton: true,
      confirmButtonText: "Yes, delete",
      cancelButtonText: "Cancel",
      confirmButtonColor: "#d33",
      reverseButtons: true,
      focusCancel: true,
    }).then((r) => r.isConfirmed);

    if (!confirmed) return;

    $btn.prop("disabled", true).addClass("opacity-50");

    $.ajax({
      url: "./include/ajax_addon_delete.php",
      method: "POST",
      data: {
        id: addonId,
      },
      dataType: "json",
    })
      .done(function (resp) {
        if (resp && resp.success) {
          toastr.success("Addon deleted");
          // Remove row from table
          const $row = $btn.closest("tr");
          $row.fadeOut(300, function () {
            $(this).remove();
          });
        } else {
          toastr.error(resp && resp.message ? resp.message : "Delete failed");
        }
      })
      .fail(function (xhr) {
        let msg = "Server error";
        if (xhr && xhr.responseJSON && xhr.responseJSON.message) {
          msg = xhr.responseJSON.message;
        }
        toastr.error(msg);
      })
      .always(function () {
        $btn.prop("disabled", false).removeClass("opacity-50");
      });
  });

  $(".js-gallery-status-toggle:not(:disabled)").on("change", function () {
    const $cb = $(this);
    const galleryId = $cb.data("gallery-id");
    const originalChecked = !$cb.prop("checked");
    $cb.prop("disabled", true);

    $.ajax({
      url: "./include/ajax_gallery_toggle_status.php",
      method: "POST",
      data: {
        id: galleryId,
      },
      dataType: "json",
    })
      .done(function (resp) {
        if (!resp || resp.success !== true) {
          $cb.prop("checked", originalChecked);
          toastr.error(
            resp && resp.message ? resp.message : "Failed to update status"
          );
        } else {
          toastr.success("Status updated");
        }
      })
      .fail(function (xhr) {
        $cb.prop("checked", originalChecked);
        let msg = "Network / server error";
        if (xhr && xhr.responseJSON && xhr.responseJSON.message) {
          msg = xhr.responseJSON.message;
        }
        toastr.error(msg);
      })
      .always(function () {
        $cb.prop("disabled", false);
      });
  });

  // SweetAlert2 delete handler
  $(document).on("click", ".js-gallery-delete-btn", async function (e) {
    e.preventDefault();
    const $btn = $(this);
    const galleryId = $btn.data("gallery-id");
    const galleryTitle = $btn.data("gallery-name");

    const confirmed = await Swal.fire({
      title: "Delete Gallery?",
      html: `<p class="mb-1">You are about to delete <strong>${$("<div>")
        .text(galleryTitle)
        .html()}</strong>.</p><small class="text-danger">This action cannot be undone.</small>`,
      icon: "warning",
      showCancelButton: true,
      confirmButtonText: "Yes, delete",
      cancelButtonText: "Cancel",
      confirmButtonColor: "#d33",
      reverseButtons: true,
      focusCancel: true,
    }).then((r) => r.isConfirmed);

    if (!confirmed) return;

    $btn.prop("disabled", true).addClass("opacity-50");

    $.ajax({
      url: "./include/ajax_gallery_delete.php",
      method: "POST",
      data: {
        id: galleryId,
      },
      dataType: "json",
    })
      .done(function (resp) {
        if (resp && resp.success) {
          toastr.success("Gallery deleted");
          // Remove row from table
          const $row = $btn.closest("tr");
          $row.fadeOut(300, function () {
            $(this).remove();
          });
        } else {
          toastr.error(resp && resp.message ? resp.message : "Delete failed");
        }
      })
      .fail(function (xhr) {
        let msg = "Server error";
        if (xhr && xhr.responseJSON && xhr.responseJSON.message) {
          msg = xhr.responseJSON.message;
        }
        toastr.error(msg);
      })
      .always(function () {
        $btn.prop("disabled", false).removeClass("opacity-50");
      });
  });
}); // end of document ready
