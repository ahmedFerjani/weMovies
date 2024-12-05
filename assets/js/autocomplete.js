import { fetchMovieDataAndVideo } from "./movie_details";

document.addEventListener("DOMContentLoaded", () => {
  // Select & Create DOM elements
  const searchInput = document.querySelector(".search-input");
  const autocompleteResults = document.createElement("div");
  autocompleteResults.classList.add("autocomplete-results");
  document.querySelector(".search-wrapper").appendChild(autocompleteResults);

  // Listening on search input values to perform autocomplete
  searchInput.addEventListener("input", async (e) => {
    const query = e.target.value.trim();

    const response = await fetch(
      `/movies/autocomplete?query=${encodeURIComponent(query)}`
    );

    const data = await response.json();

    autocompleteResults.innerHTML = data.results
      .map(
        (movie) =>
          `<div class="autocomplete-item" data-id="${movie.id}">${movie.title}</div>`
      )
      .join("");

    // Listening on click event on every item of the autocomplete list
    document.querySelectorAll(".autocomplete-item").forEach((item) => {
      item.addEventListener("click", async () => {
        const movieId = item.dataset.id;

        // Fetching the details & video of the selected movie then display the modal
        try {
          const { movieData, video } = await fetchMovieDataAndVideo(movieId);
          showMovieModal(movieData, video);
        } catch (error) {
          console.error("Error fetching movie details and video:", error);
        }
      });
    });
  });
});
