function showMovieModal(movie) {
  // Fetch the video
  fetch(`/movies/${movie.id}/videos`)
    .then((response) => response.json())
    .then((data) => {
      const videos = data.results;
      if (videos.length > 0) {
        const video = videos[0];
        document.getElementById(
          "videoIframe"
        ).src = `https://www.youtube.com/embed/${video.key}`;
        document.getElementById("modal-video-title").innerText = video.name;
      }
    })
    .catch((error) => console.error("Error fetching movie videos:", error));

  // Showing details of the movie
  document.getElementById("modal-movie-title").innerText = movie.title;
  document.getElementById(
    "modal-votes"
  ).innerText = `${movie.vote_count} votes`;

  // Generate stars based on the vote average (assuming max 5 stars)
  const starContainer = document.getElementById("modal-stars");
  starContainer.innerHTML = ""; // Clear previous stars
  const fullStars = Math.floor(movie.vote_average / 2);
  const halfStars = movie.vote_average % 2 ? 1 : 0;

  for (let i = 0; i < fullStars; i++) {
    const star = document.createElement("span");
    star.innerHTML = "&#9733;"; // Full star
    star.style.color = "#f4c542";
    starContainer.appendChild(star);
  }
  if (halfStars) {
    const halfStar = document.createElement("span");
    halfStar.innerHTML = "&#9734;"; // Empty star (can be replaced with a half-star glyph)
    halfStar.style.color = "#f4c542";
    starContainer.appendChild(halfStar);
  }
  for (let i = fullStars + halfStars; i < 5; i++) {
    const emptyStar = document.createElement("span");
    emptyStar.innerHTML = "&#9734;"; // Empty star
    emptyStar.style.color = "#ccc";
    starContainer.appendChild(emptyStar);
  }

  // Show the modal
  document.getElementById("movieModal").style.display = "block";
}

// close the modal
function closeModal() {
  document.getElementById("movieModal").style.display = "none";
}

// Event listener to close the modal when the close button is clicked
document.querySelector(".close").addEventListener("click", closeModal);

// Event listener to close the modal when the user clicks outside of it
window.onclick = function (event) {
  if (event.target === document.getElementById("movieModal")) {
    closeModal();
  }
};

// Event listener on movie-card element, the script is loaded after the DOMContentLoaded
document.addEventListener("DOMContentLoaded", function () {
  document.querySelectorAll(".movie-card").forEach(function (card) {
    card.addEventListener("click", function () {
      const movie = JSON.parse(card.dataset.movie);

      //Open the modal
      showMovieModal(movie);
    });
  });
});
