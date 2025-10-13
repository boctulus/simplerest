<template id="profile-template">
    <style>
        .profile-card {
            max-width: 800px;
            margin: 0 auto;
            padding: 1.5rem;
            background-color: #1a365d;
            color: white;
            border-radius: 0.5rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            position: relative;
        }

        .profile-image {
            width: 128px;
            height: 128px;
            border-radius: 50%;
            object-fit: cover;
            margin: 0 auto 1rem;
            display: block;
        }

        .name {
            font-size: 1.5rem;
            font-weight: bold;
            text-align: center;
            margin: 0;
        }

        .specialty {
            text-align: center;
            font-size: 0.875rem;
            margin: 0.25rem 0;
        }

        .rating {
            background-color: #f59e0b;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.875rem;
            display: inline-block;
            margin: 0.5rem auto;
        }

        .description,
        .contact-info {
            font-size: 0.875rem;
            margin-top: 1rem;
        }

        .button-group {
            position: absolute;
            top: 1.5rem;
            right: 1.5rem;
            display: flex;
            gap: 0.5rem;
        }

        .button {
            width: 2rem;
            height: 2rem;
            border-radius: 50%;
            border: none;
            background-color: #4a5568;
            color: white;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .button:hover {
            background-color: #2d3748;
        }

        .info-section {
            background-color: white;
            color: #1a202c;
            border-radius: 0.5rem;
            padding: 1rem;
            margin-top: 1.5rem;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-top: 1rem;
        }

        .info-card {
            background-color: #f7fafc;
            padding: 0.75rem;
            border-radius: 0.5rem;
        }

        .info-title {
            font-weight: bold;
            color: #2d3748;
            margin-bottom: 0.5rem;
        }

        .list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .list-item {
            padding: 0.25rem 0;
        }

        .star-rating {
            color: #f59e0b;
        }

        .star-empty {
            color: #cbd5e0;
        }
    </style>

    <div class="profile-card">
        <img class="profile-image" src="" alt="Foto de perfil">
        <h2 class="name"></h2>
        <p class="specialty"></p>
        <span class="rating"></span>
        <p class="description"></p>
        <p class="contact-info"></p>

        <div class="button-group">
            <button class="button" onclick="alert('Editar perfil')">✏️</button>
            <button class="button" onclick="alert('Descargar como PDF')">📄</button>
            <button class="button" onclick="alert('Borrar perfil')">🗑️</button>
        </div>

        <div class="info-section">
            <h3 class="name"></h3>
            <p class="position"></p>
            <p class="location"></p>
            <p class="contact-details"></p>

            <div class="info-grid">
                <div class="info-card">
                    <p class="info-title">Marcas:</p>
                    <ul class="brands list"></ul>
                </div>
                <div class="info-card">
                    <p class="info-title">Certificaciones:</p>
                    <ul class="certifications list"></ul>
                </div>
                <div class="info-card">
                    <p class="info-title">Habilidades:</p>
                    <ul class="skills list"></ul>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    class ProfileCard extends HTMLElement {
        static get observedAttributes() {
            return [
                'image-url', 'short-name', 'full-name', 'phone', 'specialty',
                'position', 'rating', 'description', 'email', 'country',
                'province', 'brands', 'certifications', 'skills'
            ];
        }

        constructor() {
            super();
            this.attachShadow({ mode: 'open' });
            const template = document.getElementById('profile-template');
            this.shadowRoot.appendChild(template.content.cloneNode(true));
        }

        connectedCallback() {
            this.render();
        }

        attributeChangedCallback(name, oldValue, newValue) {
            if (oldValue !== newValue) {
                this.render();
            }
        }

        render() {
            this.shadowRoot.querySelector('.profile-image').src = this.getAttribute('image-url') || '';
            this.shadowRoot.querySelector('.name').textContent = this.getAttribute('short-name') || '';
            this.shadowRoot.querySelector('.specialty').textContent = this.getAttribute('specialty') || '';
            this.shadowRoot.querySelector('.rating').textContent = `⭐ ${this.getAttribute('rating') || '0'} Reseñas`;
            this.shadowRoot.querySelector('.description').innerHTML = this.getAttribute('description') || '';
            this.shadowRoot.querySelector('.contact-info').textContent = `📧 ${this.getAttribute('email') || ''} 🌍 ${this.getAttribute('country') || ''}`;
            this.shadowRoot.querySelector('.info-section h3').textContent = this.getAttribute('full-name') || '';
            this.shadowRoot.querySelector('.position').textContent = this.getAttribute('position') || '';
            this.shadowRoot.querySelector('.location').textContent = this.getAttribute('province') || '';
            this.shadowRoot.querySelector('.contact-details').textContent = `${this.getAttribute('email') || ''} ${this.getAttribute('phone') || ''}`;

            const brands = JSON.parse(this.getAttribute('brands') || '[]');
            const brandsList = this.shadowRoot.querySelector('.brands');
            brandsList.innerHTML = brands.map(brand =>
                `<li class="list-item">${brand}</li>`
            ).join('');

            const certifications = JSON.parse(this.getAttribute('certifications') || '{}');
            const certificationsList = this.shadowRoot.querySelector('.certifications');
            certificationsList.innerHTML = Object.entries(certifications).map(([key, value]) =>
                `<li class="list-item"><strong>${key}:</strong> ${value}</li>`
            ).join('');

            const skills = JSON.parse(this.getAttribute('skills') || '{}');
            const skillsList = this.shadowRoot.querySelector('.skills');
            skillsList.innerHTML = Object.entries(skills).map(([key, value]) =>
                `<li class="list-item">
                        <strong>${key}:</strong> 
                        <span class="star-rating">${'★'.repeat(value)}</span>
                        <span class="star-empty">${'★'.repeat(5 - value)}</span>
                        (${value})
                    </li>`
            ).join('');
        }
    }

    customElements.define('profile-card', ProfileCard);
</script>