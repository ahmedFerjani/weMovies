/**
 * Listen to checkbox change event to submit the form
 * Resulting the reload of movies list based on selected genres
 */

document.querySelectorAll(".genre-checkbox").forEach(function (checkbox) {
  checkbox.addEventListener("change", function () {
    document.getElementById("genre-form").submit();
  });
});
