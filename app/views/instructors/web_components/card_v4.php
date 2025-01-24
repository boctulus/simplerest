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
                font-family: Arial, sans-serif;
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

            .description {
                font-size: 0.875rem;
                margin-top: 1rem;
            }

            .contact-info {
                display: flex;
                align-items: center;
                gap: 1rem;
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

            .switch {
                position: relative;
                display: inline-block;
                width: 40px;
                height: 20px;
                margin-left: auto;
            }

            .switch input {
                opacity: 0;
                width: 0;
                height: 0;
            }

            .slider {
                position: absolute;
                cursor: pointer;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background-color: #4a5568;
                transition: .4s;
                border-radius: 20px;
            }

            .slider:before {
                position: absolute;
                content: "";
                height: 16px;
                width: 16px;
                left: 2px;
                bottom: 2px;
                background-color: white;
                transition: .4s;
                border-radius: 50%;
            }

            input:checked + .slider {
                background-color: #f59e0b;
            }

            input:checked + .slider:before {
                transform: translateX(20px);
            }

            .info-section {
                display: none;
                background-color: white;
                color: #1a202c;
                border-radius: 0.5rem;
                padding: 1rem;
                margin-top: 1.5rem;
                transition: all 0.3s ease;
            }

            .info-section.visible {
                display: block;
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
            <div class="contact-info">
                <span class="email-country"></span>
                <label class="switch">
                    <input type="checkbox">
                    <span class="slider"></span>
                </label>
            </div>
            
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

                // Event listener para el switch
                this.shadowRoot.querySelector('.switch input').addEventListener('change', (e) => {
                    const infoSection = this.shadowRoot.querySelector('.info-section');
                    infoSection.classList.toggle('visible', e.target.checked);
                });
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
                this.shadowRoot.querySelector('h2.name').textContent = this.getAttribute('short-name') || '';
                this.shadowRoot.querySelector('.specialty').textContent = this.getAttribute('specialty') || '';
                this.shadowRoot.querySelector('.rating').textContent = `⭐ ${this.getAttribute('rating') || '0'} Reseñas`;
                this.shadowRoot.querySelector('.description').innerHTML = this.getAttribute('description') || '';
                this.shadowRoot.querySelector('.email-country').textContent = 
                    `📧 ${this.getAttribute('email') || ''} 🌍 ${this.getAttribute('country') || ''}`;
                
                // Información adicional
                this.shadowRoot.querySelector('.info-section h3.name').textContent = this.getAttribute('full-name') || '';
                this.shadowRoot.querySelector('.position').textContent = this.getAttribute('position') || '';
                this.shadowRoot.querySelector('.location').textContent = this.getAttribute('province') || '';
                this.shadowRoot.querySelector('.contact-details').textContent = 
                    `📧 ${this.getAttribute('email') || ''} 📱 ${this.getAttribute('phone') || ''}`;

                // Listas
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
                        <span class="star-empty">${'★'.repeat(5-value)}</span>
                        (${value})
                    </li>`
                ).join('');
            }
        }

        customElements.define('profile-card', ProfileCard);
    </script>