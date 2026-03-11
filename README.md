<div align="center">

# 🪶 Planalite 

**A lightning-fast, lightweight, flat-file CMS written in PHP.**

[![PHP Version](https://img.shields.io/badge/PHP-8.0%2B-blue.svg)](https://php.net/)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)
[![No Database](https://img.shields.io/badge/Database-None_Required-success.svg)]()

Planalite offers a basic top-down structure for simple websites that need a fast, reliable, and easy-to-use Content Management System. Uncomplicated, hackable, and completely database-free.

[Features](#sparkles-features) •
[How It Works](#gear-how-it-works) •
[How To Use](#rocket-how-to-use) •
[Admin Panel](#control_knobs-admin-panel)

---

</div>

## :sparkles: Features

*   **Flat-File Storage:** No database required. All content is stored in simple, readable `.json` files.
*   **HTML-First Templating:** Design your site in pure `HTML`. Use special CSS classes and `cms="..."` attributes to make elements dynamic.
*   **Collections & Singletons:** Support for both single, unique pages (like an "About" page) and recurring collections (like "Blog Posts" or "Portfolio Items").
*   **Simple Routing:** Out-of-the-box SEO-friendly routing (`/about`, `/blog/my-first-post`).
*   **Built-in Admin Panel:** Clean and minimal admin dashboard to manage all your unstructured content on the fly. 

*(Here you can add a screenshot of your beautiful website!)*
> `![Planalite Frontend Preview](/docs/assets/frontend-preview.png)`

---

## :gear: How It Works

Planalite separates **Data**, **Views**, and **Logic**:

1.  **Data (`/data`):** Content is saved as structured JSON objects. For collections, it's an array of objects; for singletons, it's a direct key-value object.
2.  **Templates (`/templates`):** Pure HTML blueprints. When a user requests a URL, Planalite finds the appropriate HTML file.
3.  **Parsing Engine (`/src/core/elementParser.php`):** Planalite reads the HTML, looks for specific `cms-*` classes and attributes, injects the matching data from JSON, and serves the finalized page.

---

## :rocket: How To Use

### 1. Creating a Page Template
Create an `HTML` file in the `/templates` directory (e.g. `templates/about.html`).

Add Planalite meta tags to define the page type:
```html
<head>
    <!-- Define as a singleton page -->
    <meta name="cms-type" content="singleton">
    <meta name="cms-name" content="About Us">
</head>
```

### 2. Making Content Editable
Tag normal HTML elements with `cms="keyName"` and a specific `cms-*` action class. 

*   `cms-inner`: Changes the inner text of the element.
*   `cms-href`: Safely changes the `href` attribute for anchor links.

```html
<!-- This will grab the 'pageTitle' from data/about.json and inject it -->
<h1 cms="pageTitle" class="cms-inner">Default Title</h1>

<!-- This will update the link reference -->
<a cms="contactLink" class="cms-inner cms-href" href="/contact">Say Hello</a>
```

### 3. Rendering Collections (e.g., Blogs)
You can render repeat-lists out of collections easily by chaining Planalite classes:

```html
<!-- Define the parent element to pull from data/blog-posts.json, limited to 3 items -->
<ul class="cms-collection-blog-posts cms-limit-3">
    
    <!-- Repeat this bullet point for every item -->
    <li class="cms-repeat">
        
        <!-- Inject dynamic link based on the slug -->
        <a class="cms-item-link" href="#">
            <span cms="title" class="cms-inner">Post Title</span>
        </a>
        
    </li>
</ul>
```

### Reference: Available Planalite Classes
| Class Pattern | Description |
| :--- | :--- |
| `cms-collection-{name}` | Links a DOM node to a specific JSON array in `/data`. |
| `cms-limit-{n}` | Limits the fetched collection to `n` items. |
| `cms-repeat` | Clones the element for every entry in the data array. |
| `cms-item-link` | Automatically converts to a relative link pointing to the collection item's slug (`/collection/slug`). |
| `cms-inner` | Injects text content from the JSON key specified in `cms="key"`. |
| `cms-href` | Injects link target from the JSON value. |

---

## :control_knobs: Admin Panel

Planalite includes an integrated control panel to edit content securely without touching JSON files manually.

*(Feel free to add a screenshot of the Dashboard below!)*
> `![Planalite Admin Dashboard](/docs/assets/admin-preview.png)`

1.  Navigate to `/admin.php` via your browser.
2.  The engine will automatically scan all your `/templates`.
3.  You will instantly see a Dashboard separating your **Singletons** (Static pages) and **Collections** (Dynamic lists).
4.  Adding forms or modifying text saves natively down to the respective `data/` source.

---

## :wrench: Installation

1. Clone the repository into your web server directory (e.g. Apache/Nginx or local XAMPP).
2. Ensure your server is set to serve `/` and route unfound requests to `index.php` (if using Apache, `.htaccess` usually covers this).
3. Ensure PHP `8.x` is installed.
4. Set read/write permissions for the `/data` folder so the Admin panel can save JSON files.
5. Create, build, fly! 🪶
