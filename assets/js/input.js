(function($) {
    function initialize_field($field) {
        const $wrapper = $field.find('.acf-feather-icon-wrapper');
        const $search = $wrapper.find('.feather-icon-search');
        const $options = $wrapper.find('.acf-feather-icon-option');
        const $selectedInput = $wrapper.find('input[type="radio"]:checked');

        // Initialize Feather icons
        if (typeof feather !== 'undefined') {
            feather.replace();
        }

        // Set initial search value if an icon is selected
        if ($selectedInput.length) {
            const selectedIconName = $selectedInput.closest('.acf-feather-icon-option').find('.icon-name').text();
            $search.val(selectedIconName);
            // Trigger search to show only the selected icon
            filterIcons(selectedIconName.toLowerCase());
        }

        // Search functionality
        $search.on('input', function() {
            const searchTerm = $(this).val().toLowerCase();
            filterIcons(searchTerm);
        });

        // Filter icons function
        function filterIcons(searchTerm) {
            $options.each(function() {
                const $option = $(this);
                const iconName = $option.find('.icon-name').text().toLowerCase();
                $option.toggle(iconName.includes(searchTerm));
            });
        }

        // Handle radio button changes
        $wrapper.find('input[type="radio"]').on('change', function() {
            $options.removeClass('selected');
            const $selectedOption = $(this).closest('.acf-feather-icon-option');
            $selectedOption.addClass('selected');

            // Update search field with selected icon name
            const selectedIconName = $selectedOption.find('.icon-name').text();
            $search.val(selectedIconName);

            // Show only the selected icon
            filterIcons(selectedIconName.toLowerCase());
        });

        // Add clear button functionality
        $search.on('keyup', function(e) {
            // Clear selection if search field is emptied
            if ($(this).val() === '') {
                filterIcons(''); // Show all icons
            }
        });
    }

    if(typeof acf.add_action !== 'undefined') {
        acf.add_action('ready_field/type=feather_icon', initialize_field);
        acf.add_action('append_field/type=feather_icon', initialize_field);
    }
})(jQuery);