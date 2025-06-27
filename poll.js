const jsonUrl = 'https://secret-wizard.github.io/movie-title-screens/1990s/images.json';
fetch(jsonUrl)
  .then(response => response.json())
  .then(data => {
    const images = data.images;
    const randomIndex = Math.floor(Math.random() * images.length);
    const randomUrl = images[randomIndex];
    return {
      file: randomUrl,
      timestamp: Date.now()
    };
  })
  .then(result => {
    // Serve as JSON
    console.log(JSON.stringify(result));
    // For GitHub Pages, this would need a server, but we'll use a static approach
    // Write to a temporary file or use a proxy; for now, assume manual update
  });
