# Wonderland Website Description

This website features a fantasy world theme with a dreamy and imaginative design style. The homepage navigation includes four main sections: "HOME", "ABOUT US", "SERVICES", and "CONTACT" for easy user browsing.

The main visual area presents large text slogans with background slideshow animations, creating a serene dreamlike atmosphere. The homepage background slideshow uses fade-in and fade-out transitions between images, presenting smooth and gentle visual transitions. Interactive elements such as navigation, buttons use slide-in, gradient, color change and other animation effects to enhance the dynamic and interesting user experience.

Each section contains the following:

The logo.svg is placed in the upper left corner. Clicking it returns to the top of the page.

- ABOUT US: Emphasizes beliefs with icons and titles, describing life philosophy and exploring the boundaries between dreams and reality through multiple paragraphs.
- SERVICES: Reads services.json and creates related cards. Simply displays service titles and URLs as clickable links.
- CONTACT: Provides my Line Official Account https://lin.ee/cdZA0Ok (with small icon available)

The footer is simple, indicating copyright information. "© 2017 - Powered by wolfheluo."

The overall color scheme is elegant white and gray minimalist style, paired with SVG icons and web fonts to enhance visual quality. Responsive design supports multi-device browsing, and embedded background music adds immersion.

## Key Features

- **Modern Minimalist Design**: Clean white and gray color scheme
- **Responsive Layout**: Works perfectly on desktop, tablet, and mobile devices
- **Background Music**: Auto-plays at 30% volume with user-friendly controls in bottom-right corner
- **Smooth Animations**: Fade-in effects and hover animations throughout
- **Dynamic Services**: Automatically loads service information from JSON with fallback support
- **Multi-language Support**: Full English content with modern typography

## Technical Stack

Main technologies include HTML5, CSS3, jQuery, PHP and various JS plugins to ensure interactivity and modern experience.

## File Structure

```
wonderland/
├── index.php              # Main PHP file
├── get_services.php       # PHP endpoint for loading services
├── README.md              # This file
├── services.json          # Service data
└── assets/
    ├── css/
    │   └── style.css      # Main stylesheet
    ├── js/
    │   └── script.js      # Main JavaScript file
    ├── images/
    │   ├── favicon.ico    # Website icon
    │   ├── homeBG.webp    # Homepage background
    │   └── logo.svg       # Website logo
    └── audio/
        └── The girl In The Forest.mp3  # Background music
```

## Browser Support

- Chrome (recommended)
- Firefox
- Safari
- Edge

## Setup

1. **Web Server Required**: This project uses PHP, so you need a web server with PHP support
2. **XAMPP/WAMP/LAMP**: Recommended for local development
3. **Access**: Open `index.php` in your browser via the web server (e.g., `http://localhost/wonderland/index.php`)
4. **PHP Version**: PHP 7.0 or higher recommended
5. Ensure all asset files are in their correct directories