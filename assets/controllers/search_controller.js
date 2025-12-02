import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    connect() {
        this.timeout = null;
        this.modal = null;
        this.input = null;
        this.results = null;
        this.boundHandleEscape = this.handleEscape.bind(this);
        this.boundHandleClickOutside = this.handleClickOutside.bind(this);
    }

    createModal() {
        const container = document.getElementById('search-modal-container');
        if (!container) return;

        const modalHTML = `
            <div class="search-modal hidden">
                <div class="search-modal-content">
                    <div class="search-header">
                        <input 
                            type="text" 
                            placeholder="Rechercher un film ou une série..." 
                            class="search-modal-input"
                        >
                        <button type="button" class="close-btn">
                            <span class="material-icons">close</span>
                        </button>
                    </div>
                    <div class="search-results"></div>
                </div>
            </div>
        `;
        container.innerHTML = modalHTML;

        this.modal = container.querySelector('.search-modal');
        this.input = container.querySelector('.search-modal-input');
        this.results = container.querySelector('.search-results');

        this.input.addEventListener('input', (e) => this.search(e));
        container.querySelector('.close-btn').addEventListener('click', () => this.closeModal());
    }

    openModal() {
        if (!this.modal) {
            this.createModal();
        }
        this.modal.classList.remove('hidden');
        this.input.focus();

        window.addEventListener('keydown', this.boundHandleEscape);
        this.modal.addEventListener('click', this.boundHandleClickOutside);
    }

    closeModal() {
        if (!this.modal) return;
        this.modal.classList.add('hidden');
        this.input.value = '';
        this.results.innerHTML = '';

        window.removeEventListener('keydown', this.boundHandleEscape);
        this.modal.removeEventListener('click', this.boundHandleClickOutside);
    }

    handleClickOutside(event) {
        if (event.target === this.modal) {
            this.closeModal();
        }
    }

    handleEscape(event) {
        if (event.key === 'Escape') {
            this.closeModal();
        }
    }

    search(event) {
        const query = event.target.value.trim();
        clearTimeout(this.timeout);

        if (query.length < 2) {
            this.results.innerHTML = '';
            return;
        }

        this.results.innerHTML = '<div class="loading">Recherche en cours...</div>';

        this.timeout = setTimeout(() => {
            this.performSearch(query);
        }, 300);
    }

    async performSearch(query) {
        try {
            const response = await fetch(`/api/search?q=${encodeURIComponent(query)}`);
            const data = await response.json();
            this.displayResults(data.results || []);
        } catch (error) {
            console.error('Search error:', error);
            this.results.innerHTML = '<div class="error">Erreur lors de la recherche</div>';
        }
    }

    displayResults(results) {
        if (results.length === 0) {
            this.results.innerHTML = '<div class="no-results">Aucun résultat trouvé</div>';
            return;
        }

        const html = results.map(movie => `
            <div class="search-result-card">
                ${movie.primaryImage && movie.primaryImage.url
                ? `<img src="${movie.primaryImage.url}" alt="${movie.titleText.text}" class="result-poster">`
                : `<div class="result-poster placeholder">${movie.titleText.text}</div>`
            }
                <div class="result-info">
                    <h3 class="result-title">${movie.titleText.text}</h3>
                    ${movie.releaseYear ? `<span class="result-year">${movie.releaseYear.year}</span>` : ''}
                    ${movie.ratingsSummary?.aggregateRating
                ? `<span class="result-rating">⭐ ${movie.ratingsSummary.aggregateRating.toFixed(1)}</span>`
                : ''
            }
                </div>
            </div>
        `).join('');

        this.results.innerHTML = html;
    }
}
