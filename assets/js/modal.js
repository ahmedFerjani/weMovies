import { fetchMovieDataAndVideo } from "./movie_details";

/**
 * Stars display for the rating of the movie
 * Possibility for the user to rate the movie
 */

// Show the stars and adding event listeners of mouse actions
function renderStars(container, currentRating, maxStars = 5) {
  container.innerHTML = ""; 

  for (let i = 1; i <= maxStars; i++) {
    const star = document.createElement("span");
    star.innerHTML = i <= currentRating ? "&#9733;" : "&#9734;"; 
    star.style.color = i <= currentRating ? "#f4c542" : "#ccc";
    star.style.fontSize = "24px";
    star.dataset.value = i;

    star.addEventListener("mouseover", () => highlightStars(container, i));
    star.addEventListener("mouseout", () =>
      highlightStars(container, currentRating)
    );
    star.addEventListener("click", () => handleRating(container, i));

    container.appendChild(star);
  }
}

// Dynamically change the color of stars 
function highlightStars(container, hoverRating) {
  const stars = container.querySelectorAll("span");
  stars.forEach((star, index) => {
    star.innerHTML = index < hoverRating ? "&#9733;" : "&#9734;";
    star.style.color = index < hoverRating ? "#f4c542" : "#ccc";
  });
}

// API Call for submitting the movie rate
async function handleRating(container, rating) {
  const movieId = container.dataset.movieId;

  try {
    const response = await fetch(`/movies/${movieId}/rate`, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ rating }),
    });

    if (response.ok) {
      const data = await response.json();
      if (data.success) {
        renderStars(container, rating);
        alert("Thank you for rating!");
      } else {
        alert(data.message || "Failed to rate movie.");
      }
    } else {
      throw new Error("Failed to submit rating");
    }
  } catch (error) {
    console.error("Error submitting rating:", error);
    alert("Error submitting your rating. Please try again.");
  }
}

/**
 * Hide the modal upon click on the close button or outside the box
 */

function closeModal() {
  document.getElementById("movieModal").style.display = "none";
}

document.querySelector(".close").addEventListener("click", closeModal);

window.onclick = function (event) {
  if (event.target === document.getElementById("movieModal")) {
    closeModal();
  }
};

/**
 * show with model with api response date about its details
 */

function showMovieModal(movie, video) {
  document.getElementById("modal-movie-title").innerText = movie.title;
  document.getElementById(
    "modal-votes"
  ).innerText = `${movie.vote_count} votes`;

  const starContainer = document.getElementById("modal-stars");
  starContainer.dataset.movieId = movie.id; // Attach movie ID to the container
  renderStars(starContainer, Math.round(movie.vote_average / 2)); // Convert vote_average to 5-star scale

  if (video) {
    const iframe = document.getElementById("videoIframe");
    iframe.src = `https://www.youtube.com/embed/${video.key}`;
  } else {
    document.getElementById("modal-video-title").innerText =
      "No video available";
  }

  document.getElementById("movieModal").style.display = "block";
}

document.addEventListener("DOMContentLoaded", function () {
  document.querySelectorAll(".movie-card").forEach(function (card) {
    card.addEventListener("click", async function () {
      try {
        const movie = JSON.parse(card.dataset.movie);
        const movieId = movie.id;

        const { movieData, video } = await fetchMovieDataAndVideo(movieId);
        showMovieModal(movieData, video);

        document.getElementById("movieModal").style.display = "block";
      } catch (error) {
        console.error("Error fetching movie or video details:", error);
      }
    });
  });
});
