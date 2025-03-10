<div class="bg-gray-100 text-gray-800">
    <div x-data="promptGenerator()" class="container mx-auto p-4 max-w-5xl" x-init="init()">
        <!-- Include all the partial view sections -->
        <?php include_once 'partials/header_section.php'; ?>
        <?php include_once 'partials/search_section.php'; ?>
        <?php include_once 'partials/introduction_section.php'; ?>
        <?php include_once 'partials/file_paths_section.php'; ?>
        <?php include_once 'partials/notes_section.php'; ?>
        <?php include_once 'partials/action_buttons_section.php'; ?>
        <?php include_once 'partials/generated_prompt_section.php'; ?>
    </div>

    <!-- Loader for Ajax operations -->
    <?php include_once 'partials/loader.php'; ?>

    <!-- Include the component script files -->    
    <script src="components/storage_utils.js"></script>
    <script src="components/ui_utils.js"></script>
    <script src="components/toast_config.js"></script>
    <script src="components/prompt_generator.js"></script>
</div>