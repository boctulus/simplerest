<!-- Definición del Web Component -->
<template id="social-profile-card-template">
    <div class="relative flex flex-col md:flex-row items-center md:items-start p-6 text-gray-400 shadow-lg w-full md:w-1/2 lg:w-1/3 mx-auto">
        <!-- Imagen -->
        <img class="w-40 h-40 rounded-full object-cover md:mr-6" style="padding-top: 5px;" alt="Foto de perfil">

        <!-- Contenido de texto -->
        <div class="flex-1 mt-4 md:mt-0">
            <h2 class="text-2xl font-bold text-gray-900"></h2>
            <p class="text-sm text-blue-900 font-semibold"></p>
            <p class="text-sm text-gray-400 mt-1"></p>
            <span class="inline-block bg-yellow-500 text-white text-sm font-semibold px-2 py-1 rounded mt-2"></span>

            <!-- Social Media Icons -->
            <div class="flex space-x-2 mt-4"></div>
        </div>
    </div>
</template>

<script>
    class SocialProfileCard extends HTMLElement {
        constructor() {
            super();
            const template = document.getElementById('social-profile-card-template').content;
            this.attachShadow({ mode: 'open' }).appendChild(template.cloneNode(true));
        }

        connectedCallback() {
            this.render();
        }

        static get observedAttributes() {
            return ['name', 'subtitle', 'stats', 'rating', 'image', 'facebook', 'twitter', 'linkedin', 'github'];
        }

        attributeChangedCallback(name, oldValue, newValue) {
            if (oldValue !== newValue) {
                this.render();
            }
        }

        render() {
            const shadow = this.shadowRoot;

            // Obtener atributos
            const name = this.getAttribute('name') || '';
            const subtitle = this.getAttribute('subtitle') || '';
            const stats = this.getAttribute('stats') || '';
            const rating = this.getAttribute('rating') || '';
            const image = this.getAttribute('image') || '';
            const facebook = this.getAttribute('facebook');
            const twitter = this.getAttribute('twitter');
            const linkedin = this.getAttribute('linkedin');
            const github = this.getAttribute('github');

            // Actualizar contenido
            shadow.querySelector('h2').textContent = name;
            shadow.querySelector('p.text-blue-900').textContent = subtitle;
            shadow.querySelector('p.text-gray-400').textContent = stats;
            shadow.querySelector('span').textContent = rating;
            shadow.querySelector('img').src = image;

            // Renderizar íconos de redes sociales
            const socialIconsContainer = shadow.querySelector('.flex.space-x-2.mt-4');
            socialIconsContainer.innerHTML = '';

            const socialLinks = [
                { url: facebook, icon: 'fab fa-facebook-f' },
                { url: twitter, icon: 'fab fa-twitter' },
                { url: linkedin, icon: 'fab fa-linkedin-in' },
                { url: github, icon: 'fab fa-github' },
            ];

            let hasSocialLinks = false;

            socialLinks.forEach(link => {
                if (link.url) {
                    hasSocialLinks = true;
                    const iconLink = document.createElement('a');
                    iconLink.href = link.url;
                    iconLink.target = "_blank";
                    iconLink.classList.add(
                        'w-8', 'h-8', 'bg-white', 'border-2', 'border-gray-300', 'rounded-full',
                        'flex', 'items-center', 'justify-center', 'hover:bg-gray-200', 'transition-colors'
                    );
                    iconLink.innerHTML = `<i class="${link.icon} text-gray-500 hover:text-blue-900"></i>`;
                    socialIconsContainer.appendChild(iconLink);
                }
            });

            // Ajustar el tamaño de la imagen si no hay íconos
            const profileImage = shadow.querySelector('img');
            if (!hasSocialLinks) {
                profileImage.classList.remove('w-40', 'h-40');
                profileImage.classList.add('w-32', 'h-32');
            } else {
                profileImage.classList.remove('w-32', 'h-32');
                profileImage.classList.add('w-40', 'h-40');
            }
        }
    }

    customElements.define('social-profile-card', SocialProfileCard);
</script>