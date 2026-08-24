# IY Finance Solutions Website

A professional, responsive PHP website prepared for Afrihost/cPanel hosting. The public website includes all company and service pages, while a protected administrator area manages gallery uploads and deletions.

## Included pages

- Home
- About
- Gallery with automatic folder scanning and image lightbox
- Debt Counselling
- Personal Loans
- Funeral Cover
- Legal Insurance
- Credit Clearance
- Financial Assessment
- Custom 404 page
- Secure gallery administrator

## Requirements

- PHP 8.1 or newer
- Apache with `.htaccess` support
- PHP Fileinfo extension
- HTTPS enabled on the live domain

No database, Composer installation or Node build is required.

## Afrihost/cPanel installation

1. Open **File Manager** in cPanel.
2. Back up the current website.
3. Upload the contents of this project to `public_html`.
4. Confirm that hidden files such as `.htaccess` and `.user.ini` were uploaded.
5. Set PHP to version 8.1 or newer in cPanel.
6. Open `https://your-domain.co.za/admin/` immediately after uploading.
7. Create the first administrator username and password.
8. Delete the installation ZIP from `public_html` after extraction.

The setup page disables itself permanently as soon as the administrator account has been created.

## Folder permissions

The following folders must be writable by PHP:

```text
storage/          750 or 755
uploads/gallery/  755
```

Files saved inside the gallery normally use permission `644`.

Avoid using `777` unless Afrihost support specifically instructs you to do so.

## Gallery administration

Visit `/admin/` to:

- upload up to 12 images at once;
- publish JPG, PNG, WEBP or GIF files;
- see all currently published gallery images;
- remove outdated images securely.

Each image is validated by its real MIME type and image dimensions. Uploaded files receive unpredictable names, and executable files are blocked inside the upload folder.

## Administrator account

The password is stored as a secure one-way hash in `storage/admin.json`. The storage folder is blocked from browser access by Apache.

To intentionally reset the administrator account:

1. Back up the website.
2. Delete only `storage/admin.json` through cPanel File Manager.
3. Open `/admin/` and complete the one-time setup again.

## Project structure

```text
IY-Website-Professional/
├── admin/                  # Administrator routes
├── assets/
│   ├── css/                # Public and administrator styling
│   ├── images/             # Brand and page imagery
│   └── js/                 # Navigation, gallery and dashboard behaviour
├── config/                 # Site settings, services and security helpers
├── includes/               # Shared public/admin templates
├── storage/                # Protected administrator record
├── uploads/gallery/        # Published gallery images
├── index.php               # Homepage
├── about.php               # Company page
├── gallery.php             # Dynamic public gallery
├── debt-counselling.php    # Individual service pages
├── personal-loans.php
├── funeral-cover.php
├── legal-insurance.php
├── credit-clearance.php
├── financial-assessment.php
├── .htaccess               # Apache routes and security headers
└── .user.ini               # PHP upload limits
```

## Updating company details

Contact details, the Durban address and registration numbers are managed centrally in:

```text
config/bootstrap.php
```

Service-page content is managed centrally in:

```text
config/services.php
```

## Security checklist

- Keep HTTPS enabled.
- Use a unique administrator password.
- Do not share administrator credentials through email or WhatsApp.
- Keep cPanel and PHP updated.
- Back up `uploads/gallery/` and `storage/admin.json` regularly.
- Remove former administrators’ access to cPanel immediately.
