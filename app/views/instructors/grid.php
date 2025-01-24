<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-x-6 gap-y-8 p-4">
    <?php foreach ($personal as $instructor): ?>
        <div class="relative flex flex-col p-6 text-gray-400 shadow-lg bg-white rounded-lg">
            <!-- Imagen -->       
            <img src="<?= asset('img/profile_pics/'. $instructor['img_url']) ?>" 
                 alt="Foto de perfil"
                 class="w-28 h-28 rounded-full object-cover" 
                 style="padding-top: 5px;">

            <!-- Contenido de texto -->
            <div class="flex-1 mt-4">
                <h2 class="text-2xl font-bold text-gray-900"><?= $instructor['name'] ?></h2>
                <p class="text-sm text-blue-900 font-semibold"><?= $instructor['position'] ?></p>
                <p class="text-sm text-gray-400 mt-1"><?= count($instructor['lines_families']) ?> Lines/Products</p>
                <span class="inline-block bg-yellow-500 text-white text-sm font-semibold px-2 py-1 rounded mt-2">
                    ⭐ <?= $instructor['expertise'] ?> Expertise
                </span>

                <!-- Social Media Icons si existen -->
                <?php if (!empty($instructor['social_media'])): ?>
                    <div class="flex space-x-2 mt-4">
                        <?php if (!empty($instructor['social_media']['facebook'])): ?>
                            <a href="<?= $instructor['social_media']['facebook'] ?>" class="w-8 h-8 bg-white border-2 border-gray-300 rounded-full flex items-center justify-center hover:bg-gray-200 transition-colors">
                                <i class="fab fa-facebook-f text-gray-500 hover:text-blue-900"></i>
                            </a>
                        <?php endif; ?>
                        
                        <?php if (!empty($instructor['social_media']['twitter'])): ?>
                            <a href="<?= $instructor['social_media']['twitter'] ?>" class="w-8 h-8 bg-white border-2 border-gray-300 rounded-full flex items-center justify-center hover:bg-gray-200 transition-colors">
                                <i class="fab fa-twitter text-gray-500 hover:text-blue-900"></i>
                            </a>
                        <?php endif; ?>
                        
                        <?php if (!empty($instructor['social_media']['linkedin'])): ?>
                            <a href="<?= $instructor['social_media']['linkedin'] ?>" class="w-8 h-8 bg-white border-2 border-gray-300 rounded-full flex items-center justify-center hover:bg-gray-200 transition-colors">
                                <i class="fab fa-linkedin-in text-gray-500 hover:text-blue-900"></i>
                            </a>
                        <?php endif; ?>
                        
                        <?php if (!empty($instructor['social_media']['github'])): ?>
                            <a href="<?= $instructor['social_media']['github'] ?>" class="w-8 h-8 bg-white border-2 border-gray-300 rounded-full flex items-center justify-center hover:bg-gray-200 transition-colors">
                                <i class="fab fa-github text-gray-500 hover:text-blue-900"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>