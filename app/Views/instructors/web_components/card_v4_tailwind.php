<!-- Estilos base -->
<style>
    #info-switch:checked+label {
        background-color: #3b82f6;
    }

    input[type="checkbox"],
    label,
    [role="button"] {
        touch-action: manipulation;
        -webkit-tap-highlight-color: transparent;
    }
</style>

<!-- Template del componente -->
<template id="profile-card-template">
    <div class="relative">
        <!-- Tarjeta Principal -->
        <div
            class="relative flex flex-col md:flex-row items-center md:items-start p-6 bg-blue-900 text-white shadow-lg w-full max-w-4xl mx-auto">
            <img class="profile-image w-32 h-32 rounded-full object-cover md:mr-6" alt="Foto de perfil">

            <div class="flex-1 mt-4 md:mt-0">
                <h2 class="profile-name text-2xl font-bold"></h2>
                <p class="profile-specialty text-sm text-gray-400"></p>
                <span
                    class="profile-rating inline-flex items-center gap-1 bg-yellow-500 text-white text-sm font-semibold px-2 py-1 rounded mt-2"></span>
                <p class="profile-description mt-4"></p>
                <p class="profile-contact mt-2 text-sm text-gray-400"></p>
            </div>

            <div class="absolute top-4 right-4 flex flex-col space-y-2 md:flex-row md:space-y-0 md:space-x-2">
                <div class="w-8 h-8 bg-gray-500 rounded-full cursor-pointer hover:bg-gray-600 transition-colors flex items-center justify-center"
                    role="button">
                    <i class="fas fa-pencil-alt text-white"></i>
                </div>
                <div class="w-8 h-8 bg-gray-500 rounded-full cursor-pointer hover:bg-gray-600 transition-colors flex items-center justify-center"
                    role="button">
                    <i class="fas fa-file-pdf text-white"></i>
                </div>
                <div class="w-8 h-8 bg-red-500 rounded-full cursor-pointer hover:bg-red-600 transition-colors flex items-center justify-center"
                    role="button">
                    <i class="fas fa-trash text-white"></i>
                </div>
            </div>
        </div>

        <!-- Switch y contenido adicional -->
        <div class="relative max-w-4xl mx-auto">
            <input type="checkbox" id="info-switch" class="hidden peer">
            <label for="info-switch" class="absolute -top-12 right-4 w-12 h-6 bg-gray-600 rounded-full peer-checked:bg-blue-500 cursor-pointer transition-all duration-300 
                   before:content-[''] before:absolute before:top-1 before:left-1 before:w-4 before:h-4 before:bg-white before:rounded-full 
                   before:transition-all before:duration-300 peer-checked:before:translate-x-6">
            </label>

            <div class="extended-info mt-2 text-white shadow-lg overflow-hidden transition-all duration-300 max-h-0 peer-checked:max-h-[500px]"
                style="background-color: #e7e7e7"></div>
        </div>
    </div>
</template>

<script>
    class ProfileCard extends HTMLElement {
        constructor() {
            super();
            this.template = document.getElementById('profile-card-template');
        }

        static get observedAttributes() {
            return [
                'image-url', 'short-name', 'full-name', 'phone', 'specialty',
                'position', 'rating', 'description', 'email', 'country',
                'province', 'brands', 'certifications', 'skills'
            ];
        }

        connectedCallback() {
            const content = this.template.content.cloneNode(true);

            // Generar IDs únicos para el switch
            const uniqueId = `info-switch-${Math.random().toString(36).substr(2, 9)}`;
            const switchInput = content.querySelector('#info-switch');
            const switchLabel = content.querySelector('label[for="info-switch"]');

            if (switchInput && switchLabel) {
                switchInput.id = uniqueId;
                switchLabel.setAttribute('for', uniqueId);
            }

            this.appendChild(content);
            this.render();
            this.setupEventListeners();
        }

        setupEventListeners() {
            const editButton = this.querySelector('.fa-pencil-alt').parentElement;
            const pdfButton = this.querySelector('.fa-file-pdf').parentElement;
            const deleteButton = this.querySelector('.fa-trash').parentElement;

            editButton.addEventListener('click', () => alert('Editar perfil'));
            pdfButton.addEventListener('click', () => alert('Descargar como PDF'));
            deleteButton.addEventListener('click', () => alert('Borrar perfil'));
        }

        attributeChangedCallback(name, oldValue, newValue) {
            if (this.isConnected) {
                this.render();
            }
        }

        render() {
            // Actualizar imagen de perfil
            const profileImage = this.querySelector('.profile-image');
            if (profileImage && this.getAttribute('image-url')) {
                profileImage.src = this.getAttribute('image-url');
            }

            // Actualizar nombre
            const profileName = this.querySelector('.profile-name');
            if (profileName) {
                profileName.textContent = this.getAttribute('short-name');
            }

            // Actualizar especialidad
            const profileSpecialty = this.querySelector('.profile-specialty');
            if (profileSpecialty) {
                profileSpecialty.textContent = this.getAttribute('specialty');
            }

            // Actualizar rating con ícono de Font Awesome
            const profileRating = this.querySelector('.profile-rating');
            if (profileRating) {
                const rating = this.getAttribute('rating');
                profileRating.innerHTML = `<i class="fas fa-star text-yellow-400"></i> ${rating} Reseñas`;
            }

            // Actualizar descripción
            const profileDescription = this.querySelector('.profile-description');
            if (profileDescription) {
                profileDescription.innerHTML = this.getAttribute('description');
            }

            // Actualizar información de contacto
            const profileContact = this.querySelector('.profile-contact');
            if (profileContact) {
                const email = this.getAttribute('email');
                const country = this.getAttribute('country');
                profileContact.innerHTML = `
                       <span class="inline-flex items-center gap-1">
                           <i class="far fa-envelope"></i>
                           ${email}
                       </span>
                       <span class="inline-flex items-center gap-1 ml-2">
                           <i class="fas fa-globe"></i>
                           ${country}
                       </span>
                   `;
            }

            // Actualizar información extendida
            this.updateExtendedInfo();
        }

        updateExtendedInfo() {
            const extendedInfo = this.querySelector('.extended-info');
            if (!extendedInfo) return;

            const brands = this.safeJSONParse(this.getAttribute('brands'), []);
            const certifications = this.safeJSONParse(this.getAttribute('certifications'), {});
            const skills = this.safeJSONParse(this.getAttribute('skills'), {});

            extendedInfo.innerHTML = `
                   <div class="p-4">
                       <div class="p-4 bg-gray-50 rounded-md shadow-md max-w-full md:max-w-4xl mx-auto">
                           <div class="flex flex-col md:flex-row items-start md:items-center justify-between">
                               <div class="mb-4 md:mb-0">
                                   <h1 class="text-lg font-semibold text-gray-800">${this.getAttribute('full-name')}</h1>
                                   <p class="text-gray-600">${this.getAttribute('position')}</p>
                               </div>
                               <div class="mb-4 text-sm text-gray-600 text-right md:text-left">
                                   <p>${this.getAttribute('province')}</p>
                               </div>
                               <div class="text-sm text-gray-600 text-right md:text-left">
                                   <p>${this.getAttribute('email')}</p>
                                   <p>${this.getAttribute('phone')}</p>
                               </div>
                           </div>

                           <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4 text-sm text-gray-700">
                               ${this.createSection('Marcas', brands)}
                               ${this.createSection('Certificaciones', certifications)}
                               ${this.createSkillsSection(skills)}
                           </div>
                       </div>
                   </div>
               `;
        }

        createSection(title, items) {
            const listItems = Array.isArray(items)
                ? items.map(item => `<li>${item}</li>`).join('')
                : Object.entries(items).map(([key, value]) => `<li>${key}: ${value}</li>`).join('');

            return `
                   <div>
                       <p class="mb-2"><strong>${title}:</strong></p>
                       <ul>${listItems}</ul>
                   </div>
               `;
        }

        createSkillsSection(skills) {
            const skillItems = Object.entries(skills)
                .map(([skill, level]) => `
                       <li>${skill}: 
                           <span class="text-yellow-500">${'★'.repeat(level)}</span> 
                           (${level})
                       </li>
                   `).join('');

            return `
                   <div>
                       <p class="mb-2"><strong>Habilidades:</strong></p>
                       <ul>${skillItems}</ul>
                   </div>
               `;
        }

        safeJSONParse(value, defaultValue) {
            try {
                return JSON.parse(value || JSON.stringify(defaultValue));
            } catch {
                return defaultValue;
            }
        }
    }

    customElements.define('profile-card', ProfileCard);
</script>