# Documentation: R-Teacher

This module handles teacher information management, teacher list pages, class list assignments, and profile details inside the registrar portal.

## Commit History & Changes Log:

* **June 23, 2026 (`267930f`)**:
  * Created `R-TEACHER/teacherShow.php` view file to show detailed teacher profiles and assignments.
* **June 23, 2026 (`5b44eed`)**:
  * Created the `R-Teacher/` module (initially loaded as `R-TEACHER/`).
  * Added controller `Teacher.php` to handle routing for teacher actions.
  * Added model `Teacher_model.php` to retrieve teacher data.
  * Added views for teacher list management: `classList.php`, `teacherCreate.php`, `teacherEdit.php`, and `teacherList.php`.
