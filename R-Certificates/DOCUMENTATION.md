# Documentation: R-Certificates

This module manages the student certificates request system, certificate templates, and Good Moral certificate generation.

## Commit History & Changes Log:

* **June 29, 2026 (`de7f26f`)**:
  * Created the `R-Certificates` module and loaded it into the repository.
  * Added `CONTROLLER/Certificate.php` controller to manage certificates request forms and generation.
  * Added `MODEL/Principal_model.php` model to retrieve principal details for signatures.
  * Added views for requests management (`request_form.php`, `template_request_form.php`).
  * Added views for certificate creation, editing, listing, and templates (`certificateCreate.php`, `certificateEdit.php`, `certificateList.php`, `certificateList2.php`, `certificateShow.php`, `template_certificate.php`).
  * Added `good_moral_List.php` to search and generate student Good Moral certificates.
