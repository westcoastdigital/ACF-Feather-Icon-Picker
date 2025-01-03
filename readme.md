# ACF Feather Icon Picker

## Usage
```
// Get just the icon name
$icon = get_field('your_icon_field', false, false);

// Get the HTML (with Feather.js initialized)
$icon = get_field('your_icon_field', false, true);

// Using in a template with custom size
$icon = get_field('your_icon_field');
echo str_replace('data-feather', 'data-feather width="32" height="32"', $icon);
```