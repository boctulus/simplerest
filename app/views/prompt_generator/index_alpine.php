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

<!-- Include all components -->
<?php
    js_file(VIEWS_PATH . 'prompt_generator/components/storage_utils.js');
    js_file(VIEWS_PATH . 'prompt_generator/components/ui_utils.js');
    js_file(VIEWS_PATH . 'prompt_generator/components/toast_config.js');
    js_file(VIEWS_PATH . 'prompt_generator/components/prompt_generator.js');
?>