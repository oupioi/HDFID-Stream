import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['overlay', 'title', 'description', 'year', 'runtime', 'rating', 'genres', 'poster', 'trailer'];

    connect() {
        this.handleEscape = this.handleEscape.bind(this);
        document.addEventListener('keydown', this.handleEscape);
    }

    disconnect() {
        document.removeEventListener('keydown', this.handleEscape);
    }

    handleEscape(event) {
        if (event.key === 'Escape') {
            this.close();
        }
    }

    open(event) {
        const movieData = event.currentTarget.dataset;

        if (this.hasTitleTarget) {
            this.titleTarget.textContent = movieData.movieTitle || 'Titre inconnu';
        }

        if (this.hasDescriptionTarget) {
            this.descriptionTarget.textContent = movieData.movieDescription || 'Aucune description disponible.';
        }

        if (this.hasYearTarget) {
            this.yearTarget.textContent = movieData.movieYear || '-';
        }

        if (this.hasRuntimeTarget) {
            const runtime = movieData.movieRuntime;
            if (runtime) {
                const hours = Math.floor(runtime / 60);
                const minutes = runtime % 60;
                this.runtimeTarget.textContent = hours > 0 ? `${hours}h ${minutes}min` : `${minutes}min`;
            } else {
                this.runtimeTarget.textContent = '-';
            }
        }

        if (this.hasRatingTarget) {
            const rating = movieData.movieRating;
            this.ratingTarget.textContent = rating ? `⭐ ${parseFloat(rating).toFixed(1)}` : '-';
        }

        if (this.hasGenresTarget) {
            const genres = movieData.movieGenres;
            if (genres) {
                const genreArray = genres.split(',');
                this.genresTarget.innerHTML = genreArray
                    .map(genre => `<span class="genre-tag">${genre.trim()}</span>`)
                    .join('');
            } else {
                this.genresTarget.innerHTML = '';
            }
        }

        if (this.hasPosterTarget) {
            const posterUrl = movieData.moviePoster;
            if (posterUrl) {
                this.posterTarget.src = posterUrl;
                this.posterTarget.style.display = 'block';
            } else {
                this.posterTarget.style.display = 'none';
            }
        }

        if (this.hasTrailerTarget) {
            const videoId = movieData.movieVideoId;
            if (videoId) {
                this.trailerTarget.innerHTML = `
                    <div class="trailer-frame">
                        <iframe 
                            src="https://www.youtube.com/embed/${videoId}?autoplay=0&rel=0" 
                            frameborder="0" 
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                            allowfullscreen>
                        </iframe>
                    </div>
                `;
            } else {
                this.trailerTarget.innerHTML = `
                    <div class="trailer-placeholder">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.91 11.672a.375.375 0 010 .656l-5.603 3.113a.375.375 0 01-.557-.328V8.887c0-.286.307-.466.557-.327l5.603 3.112z" />
                        </svg>
                        <span>Trailer non disponible</span>
                    </div>
                `;
            }
        }

        if (this.hasOverlayTarget) {
            this.overlayTarget.classList.add('active');
            document.body.style.overflow = 'hidden';
        }
    }

    close() {
        if (this.hasOverlayTarget) {
            this.overlayTarget.classList.remove('active');
            document.body.style.overflow = '';

            if (this.hasTrailerTarget) {
                const iframe = this.trailerTarget.querySelector('iframe');
                if (iframe) {
                    iframe.src = '';
                }
            }
        }
    }

    closeOnOverlay(event) {
        if (event.target === this.overlayTarget) {
            this.close();
        }
    }
}
