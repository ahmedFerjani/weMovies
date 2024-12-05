export async function fetchMovieDataAndVideo(movieId) {
  try {
    // Fetch movie details
    const movieResponse = await fetch(`/movies/${movieId}`);
    const movieData = await movieResponse.json();

    // Fetch movie videos
    const videoResponse = await fetch(`/movies/${movieId}/videos`);
    const videoData = await videoResponse.json();

    const video =
      videoData.results && videoData.results.length > 0
        ? videoData.results[0]
        : null;

    return { movieData, video };
  } catch (error) {
    console.error("Error fetching movie and video data:", error);
    throw error;
  }
}
