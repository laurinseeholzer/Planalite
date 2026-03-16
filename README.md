<div align="center">

# Planalite 

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
*   **Built-in Admin Panel:** Clean and minimal admin dashboard to manage all your unstructured content on the fly. 


---

## :gear: How It Works

Planalite separates **Data**, **Views**, and **Logic**:

1.  **Data (`/data`):** Content is saved as structured JSON objects. For collections, it's an array of objects; for singletons, it's a direct key-value object. Each page (singleton or collection) has its own JSON file stored in the `/data` directory.

2.  **Templates (`/templates`):** Pure HTML blueprints. When a user requests a URL, Planalite finds the appropriate HTML file in the `/templates` directory and injects the data from the corresponding JSON file.

---

## :rocket: How To Use

### 1. Creating a Page Template
Create an `HTML` file in the `/templates` directory (e.g. `templates/about.html`).

Add Planalite meta tags to define the page type (singleton or collection) and the name of the page as it will be displayed in the admin panel.

```html
<head>
    <!-- Define as a singleton page -->
    <meta name="cms-type" content="singleton">
    <!-- Define as a collection page -->
    <meta name="cms-type" content="collection">
    <meta name="cms-name" content="Page Name">
</head>
```

### 2. Making Content Editable
Tag normal HTML elements with `cms="keyName"` and a specific `cms-*` action class. The cms key name is used to identify data in the JSON file and should therfore be unique for each data field.

Use the following action classes to make diffrent attributes of the HTML element editable:

*   `cms-inner`: Makes the inner text of the element editable.
*   `cms-href`: Makes the `href` attribute of the element editable.
*   `cms-src`: Makes the `src` attribute of the element editable.

```html
    <!-- This will grab the 'pageTitle' from data/page.json and inject it -->
    <h1 cms="pageTitle" class="cms-inner">Default Title</h1>

    <!-- This will update the link reference and the link text -->
    <a cms="contactLink" class="cms-inner cms-href" href="/link">Link Text</a>
```

*   `cms-repeat`: Repeats the element for every item in the Data-Array stored in the JSON file.

```html
    <!-- This will grab the 'listItems' array from data/page.json and repeat the list item for every item in the array -->
    <ul>
        <li cms="listItems" class="cms-inner cms-repeat">list item</li>
    </ul>
```

### 3. Rendering Collections (e.g., Blogs)

A collection is a set of Pages that have the same template and are stored in the same JSON file. For example, a blog is a collection of blog posts where each blog post has its own page but they all share the same template.

To render a collecion as a list on some other singleton page you can use the `cms-collection-{name}` class to define the parent element to pull from data/{name}.json, limited to {n} items. The keys and corresponding values of that repeated list items are then directly pulled from the JSON file of the collection.

```html
<!-- Define the parent element to pull from data/blog-posts.json, limited to 3 items -->
<ul class="cms-collection-{name} cms-limit-{n}">
    
    <!-- Repeat this bullet point for every item -->
    <li class="cms-repeat">
        
        <!-- Inject dynamic link based on the slug -->
        <a class="cms-item-link" href="#">
            <span cms="title" class="cms-inner">Post Title</span>
        </a>
        
    </li>
</ul>
```

Use the `cms-item-link` class to create a link to the collection item's individual page.

## :control_knobs: Admin Panel

Planalite includes an integrated control panel to edit content securely without touching JSON files manually.

1.  Navigate to `/admin.php` via your browser.
2.  The engine will automatically scan all your `/templates`.
3.  You will instantly see a Dashboard separating your **Singletons** (Static pages) and **Collections** (Dynamic lists).
4.  Adding forms or modifying text saves natively down to the respective `data/` source.
5.  Planalite offers automatic json generation and auto-healing from your templates. This means you don't have to create json files manually, they will be created automatically when you create a new template or add a new cms attribute to an existing template. To update the json file after adding a new cms attribute to an existing template you need to click the `generate from template` button on the edit page.

---

## :wrench: Installation

1. **Upload**: Copy `install.php` to your web server's root directory (e.g., Apache, Nginx, or XAMPP).
2. **Execute**: Navigate to `/install.php` in your browser to install Planalite.
3. **Automate**: The script will automatically fetch the latest version of Planalite from GitHub and set up the environment.
4. **Cleanup**: Delete `install.php` and start building your site by adding files to `/templates` (and `/data` if you want to add some initial data).